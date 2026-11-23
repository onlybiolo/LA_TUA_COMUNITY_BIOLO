<?php
require_once __DIR__ . '/config.php';

class PostRepo {
    const FLAIRS = [
        'LAYYD EMPIRE' => ['colore' => '#facc15', 'bg' => 'rgba(250,204,21,0.15)', 'icon' => '👑'],
        'SNIPPET' => ['colore' => '#38bdf8', 'bg' => 'rgba(56,189,248,0.15)', 'icon' => '🔥'],
        'HOT TAKE' => ['colore' => '#fb7185', 'bg' => 'rgba(251,113,133,0.15)', 'icon' => '🧨'],
        'DISCUSSIONE' => ['colore' => '#a3e635', 'bg' => 'rgba(163,230,53,0.15)', 'icon' => '💬'],
        'GATEKEEPING' => ['colore' => '#c084fc', 'bg' => 'rgba(192,132,252,0.15)', 'icon' => '🔒'],
        'PRODUCER LORE' => ['colore' => '#f97316', 'bg' => 'rgba(249,115,22,0.15)', 'icon' => '🎛️'],
        'RANKING' => ['colore' => '#2dd4bf', 'bg' => 'rgba(45,212,191,0.15)', 'icon' => '📊'],
        'SCOPERTA <100' => ['colore' => '#e2e8f0', 'bg' => 'rgba(226,232,240,0.15)', 'icon' => '🔍'],
    ];

    public static function tutti(?string $flair = null, ?int $artistaId = null, string $sort = 'hot', ?int $currUserId = null): array {
        $pdo = db();
        $sql = "SELECT p.*, 
                       u.username, u.badge, u.avatar_color,
                       a.nome AS artista_nome, a.slug AS artista_slug, a.avatar_url AS artista_avatar, a.is_goat AS artista_is_goat,
                       (SELECT COUNT(*) FROM commenti c WHERE c.post_id = p.id) AS commenti_count";
        if ($currUserId) {
            $sql .= ", (SELECT 1 FROM post_upvotes pu WHERE pu.post_id = p.id AND pu.utente_id = ?) AS ha_votato";
        } else {
            $sql .= ", 0 AS ha_votato";
        }
        $sql .= " FROM post p 
                  JOIN utenti u ON p.utente_id = u.id
                  LEFT JOIN artisti a ON p.artista_id = a.id
                  WHERE 1=1";
        $params = $currUserId ? [$currUserId] : [];

        if ($flair) {
            $sql .= " AND p.flair = ?";
            $params[] = $flair;
        }

        if ($artistaId) {
            $sql .= " AND p.artista_id = ?";
            $params[] = $artistaId;
        }

        if ($sort === 'new') {
            $sql .= " ORDER BY p.id DESC";
        } elseif ($sort === 'top') {
            $sql .= " ORDER BY p.upvotes DESC, p.id DESC";
        } else {
            // 'hot' algorithm: (upvotes * 10) + commenti_count * 5 - ore_passate
            $sql .= " ORDER BY (p.upvotes * 3 + (SELECT COUNT(*) FROM commenti c WHERE c.post_id = p.id) * 2) DESC, p.id DESC";
        }

        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function trova(int $id, ?int $currUserId = null): ?array {
        $pdo = db();
        $sql = "SELECT p.*, 
                       u.username, u.bio AS autore_bio, u.badge, u.avatar_color,
                       a.nome AS artista_nome, a.slug AS artista_slug, a.avatar_url AS artista_avatar, a.is_goat AS artista_is_goat,
                       (SELECT COUNT(*) FROM commenti c WHERE c.post_id = p.id) AS commenti_count";
        if ($currUserId) {
            $sql .= ", (SELECT 1 FROM post_upvotes pu WHERE pu.post_id = p.id AND pu.utente_id = ?) AS ha_votato";
        } else {
            $sql .= ", 0 AS ha_votato";
        }
        $sql .= " FROM post p 
                  JOIN utenti u ON p.utente_id = u.id
                  LEFT JOIN artisti a ON p.artista_id = a.id
                  WHERE p.id = ?";
        $params = $currUserId ? [$currUserId, $id] : [$id];

        $st = $pdo->prepare($sql);
        $st->execute($params);
        $p = $st->fetch();
        return $p ?: null;
    }

    public static function crea(int $utenteId, string $titolo, string $contenuto, string $flair = 'DISCUSSIONE', ?int $artistaId = null, ?string $link = null): int {
        $pdo = db();
        if (!array_key_exists($flair, self::FLAIRS)) {
            $flair = 'DISCUSSIONE';
        }
        $st = $pdo->prepare("INSERT INTO post (utente_id, artista_id, titolo, flair, contenuto, link_esterno, upvotes) VALUES (?,?,?,?,?,?,1)");
        $st->execute([$utenteId, $artistaId, trim($titolo), $flair, trim($contenuto), trim($link ?? '') ?: null]);
        $postId = (int)$pdo->lastInsertId();

        // Autore auto-upvota il proprio post
        $pdo->prepare("INSERT IGNORE INTO post_upvotes (post_id, utente_id) VALUES (?, ?)")->execute([$postId, $utenteId]);

        return $postId;
    }

    public static function toggleUpvote(int $postId, int $utenteId): array {
        $pdo = db();
        $check = $pdo->prepare("SELECT 1 FROM post_upvotes WHERE post_id = ? AND utente_id = ?");
        $check->execute([$postId, $utenteId]);
        $voted = (bool)$check->fetch();

        if ($voted) {
            $pdo->prepare("DELETE FROM post_upvotes WHERE post_id = ? AND utente_id = ?")->execute([$postId, $utenteId]);
            $pdo->prepare("UPDATE post SET upvotes = GREATEST(0, upvotes - 1) WHERE id = ?")->execute([$postId]);
            $isUpvoted = false;
        } else {
            $pdo->prepare("INSERT INTO post_upvotes (post_id, utente_id) VALUES (?, ?)")->execute([$postId, $utenteId]);
            $pdo->prepare("UPDATE post SET upvotes = upvotes + 1 WHERE id = ?")->execute([$postId]);
            $isUpvoted = true;
        }

        $st = $pdo->prepare("SELECT upvotes FROM post WHERE id = ?");
        $st->execute([$postId]);
        $count = (int)$st->fetchColumn();

        return ['upvoted' => $isUpvoted, 'count' => $count];
    }

    public static function commenti(int $postId, ?int $currUserId = null): array {
        $pdo = db();
        $sql = "SELECT c.*, u.username, u.badge, u.avatar_color";
        if ($currUserId) {
            $sql .= ", (SELECT 1 FROM comment_upvotes cu WHERE cu.comment_id = c.id AND cu.utente_id = ?) AS ha_votato";
        } else {
            $sql .= ", 0 AS ha_votato";
        }
        $sql .= " FROM commenti c 
                  JOIN utenti u ON c.utente_id = u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.upvotes DESC, c.id ASC";
        $params = $currUserId ? [$currUserId, $postId] : [$postId];
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function aggiungiCommento(int $postId, int $utenteId, string $testo): int {
        $pdo = db();
        $st = $pdo->prepare("INSERT INTO commenti (post_id, utente_id, testo, upvotes) VALUES (?, ?, ?, 1)");
        $st->execute([$postId, $utenteId, trim($testo)]);
        $cId = (int)$pdo->lastInsertId();
        $pdo->prepare("INSERT IGNORE INTO comment_upvotes (comment_id, utente_id) VALUES (?, ?)")->execute([$cId, $utenteId]);
        return $cId;
    }

    public static function toggleUpvoteCommento(int $commentId, int $utenteId): array {
        $pdo = db();
        $check = $pdo->prepare("SELECT 1 FROM comment_upvotes WHERE comment_id = ? AND utente_id = ?");
        $check->execute([$commentId, $utenteId]);
        $voted = (bool)$check->fetch();

        if ($voted) {
            $pdo->prepare("DELETE FROM comment_upvotes WHERE comment_id = ? AND utente_id = ?")->execute([$commentId, $utenteId]);
            $pdo->prepare("UPDATE commenti SET upvotes = GREATEST(0, upvotes - 1) WHERE id = ?")->execute([$commentId]);
            $isUpvoted = false;
        } else {
            $pdo->prepare("INSERT INTO comment_upvotes (comment_id, utente_id) VALUES (?, ?)")->execute([$commentId, $utenteId]);
            $pdo->prepare("UPDATE commenti SET upvotes = upvotes + 1 WHERE id = ?")->execute([$commentId]);
            $isUpvoted = true;
        }

        $st = $pdo->prepare("SELECT upvotes FROM commenti WHERE id = ?");
        $st->execute([$commentId]);
        $count = (int)$st->fetchColumn();

        return ['upvoted' => $isUpvoted, 'count' => $count];
    }
}
