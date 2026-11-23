<?php
require_once __DIR__ . '/config.php';

class Artista {
    const LIVELLI = [
        'punta' => '0 m',
        'superficie' => '-10 m',
        'profondo' => '-40 m',
        'abisso' => '-100 m'
    ];

    const LIVELLI_LABELS = [
        'punta' => 'Punta dell\'Iceberg (0m - Riconosciuti)',
        'superficie' => 'Superficie (-10m - SoundCloud Core)',
        'profondo' => 'Profondo (-40m - Solo per veri adepti)',
        'abisso' => 'Abisso (-100m - Mistero totale / 20 ascolti)'
    ];

    public function __construct(
        public ?int $id = null,
        public string $nome = '',
        public ?string $slug = null,
        public string $livello = 'superficie',
        public string $stile = '',
        public ?string $bio = null,
        public ?string $progetto = null,
        public ?string $brano = null,
        public int $plays = 0,
        public int $monthly_listeners = 0,
        public ?string $link = null,
        public ?string $link_soundcloud = null,
        public ?string $link_youtube = null,
        public ?string $link_instagram = null,
        public ?string $link_spotify = null,
        public ?string $avatar_url = null,
        public ?string $note = null,
        public bool $is_goat = false,
        public ?string $creato_il = null,
    ) {
        if (!$this->slug && $this->nome) {
            $this->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->nome), '-'));
        }
    }

    public static function daRiga(array $r): self {
        return new self(
            (int)$r['id'],
            $r['nome'],
            $r['slug'] ?? null,
            $r['livello'] ?? 'superficie',
            $r['stile'] ?? '',
            $r['bio'] ?? null,
            $r['progetto'] ?? null,
            $r['brano'] ?? null,
            (int)($r['plays'] ?? 0),
            (int)($r['monthly_listeners'] ?? 0),
            $r['link'] ?? null,
            $r['link_soundcloud'] ?? null,
            $r['link_youtube'] ?? null,
            $r['link_instagram'] ?? null,
            $r['link_spotify'] ?? null,
            $r['avatar_url'] ?? null,
            $r['note'] ?? null,
            (bool)($r['is_goat'] ?? false),
            $r['creato_il'] ?? null
        );
    }

    public static function daPost(array $p, ?int $id = null): self {
        $livello = array_key_exists($p['livello'] ?? '', self::LIVELLI) ? $p['livello'] : 'superficie';
        $link = trim($p['link'] ?? '');
        if ($link !== '' && !preg_match('#^https?://#i', $link)) $link = '';

        $nome = trim($p['nome'] ?? '');
        $is_goat = !empty($p['is_goat']) || (strtolower($nome) === 'layyd');

        return new self(
            $id,
            $nome,
            strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nome), '-')),
            $livello,
            trim($p['stile'] ?? ''),
            trim($p['bio'] ?? '') ?: null,
            trim($p['progetto'] ?? '') ?: null,
            trim($p['brano'] ?? '') ?: null,
            (int)($p['plays'] ?? 0),
            (int)($p['monthly_listeners'] ?? 0),
            $link ?: null,
            trim($p['link_soundcloud'] ?? '') ?: null,
            trim($p['link_youtube'] ?? '') ?: null,
            trim($p['link_instagram'] ?? '') ?: null,
            trim($p['link_spotify'] ?? '') ?: null,
            trim($p['avatar_url'] ?? '') ?: null,
            trim($p['note'] ?? '') ?: null,
            $is_goat
        );
    }

    public function profondita(): string {
        return self::LIVELLI[$this->livello] ?? '-10 m';
    }

    public function livelloBadge(): string {
        return strtoupper($this->livello) . " (" . $this->profondita() . ")";
    }

    public function avatar(): string {
        if ($this->avatar_url) return $this->avatar_url;
        if ($this->is_goat) return 'assets/layyd_proof.png';
        return '';
    }
}

class ArtistaRepo {
    public static function tutti(?string $livello = null, ?string $q = null, string $ordina = 'iceberg'): array {
        $pdo = db();
        $sql = "SELECT * FROM artisti WHERE 1=1";
        $params = [];

        if ($livello && array_key_exists($livello, Artista::LIVELLI)) {
            $sql .= " AND livello = ?";
            $params[] = $livello;
        }

        if ($q) {
            $sql .= " AND (nome LIKE ? OR stile LIKE ? OR brano LIKE ? OR progetto LIKE ?)";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
            $params[] = "%$q%";
        }

        if ($ordina === 'plays') {
            $sql .= " ORDER BY is_goat DESC, plays DESC, nome ASC";
        } elseif ($ordina === 'nome') {
            $sql .= " ORDER BY nome ASC";
        } elseif ($ordina === 'recente') {
            $sql .= " ORDER BY id DESC";
        } else {
            // Iceberg order: GOAT first, then by iceberg depths
            $sql .= " ORDER BY is_goat DESC, FIELD(livello,'punta','superficie','profondo','abisso'), nome ASC";
        }

        $st = $pdo->prepare($sql);
        $st->execute($params);
        return array_map([Artista::class, 'daRiga'], $st->fetchAll());
    }

    public static function trova(int $id): ?Artista {
        $st = db()->prepare("SELECT * FROM artisti WHERE id = ?");
        $st->execute([$id]);
        $r = $st->fetch();
        return $r ? Artista::daRiga($r) : null;
    }

    public static function trovaPerSlug(string $slug): ?Artista {
        $st = db()->prepare("SELECT * FROM artisti WHERE slug = ? OR nome = ? LIMIT 1");
        $st->execute([$slug, $slug]);
        $r = $st->fetch();
        return $r ? Artista::daRiga($r) : null;
    }

    public static function trovaGoat(): ?Artista {
        $st = db()->prepare("SELECT * FROM artisti WHERE is_goat = 1 OR LOWER(nome) = 'layyd' ORDER BY is_goat DESC LIMIT 1");
        $st->execute();
        $r = $st->fetch();
        return $r ? Artista::daRiga($r) : null;
    }

    public static function tracce(int $artistaId): array {
        $st = db()->prepare("SELECT * FROM tracce WHERE artista_id = ? ORDER BY is_hit DESC, plays DESC, id ASC");
        $st->execute([$artistaId]);
        return $st->fetchAll();
    }

    public static function aggiungiTraccia(int $artistaId, string $titolo, ?string $progetto = null, int $anno = 2024, int $plays = 0, ?string $prod = null, ?string $link = null, bool $isHit = false): void {
        $st = db()->prepare("INSERT INTO tracce (artista_id, titolo, progetto, anno, plays, produttore, link_audio, is_hit) VALUES (?,?,?,?,?,?,?,?)");
        $st->execute([$artistaId, $titolo, $progetto, $anno, $plays, $prod, $link, $isHit ? 1 : 0]);
    }

    public static function incrementaPlays(int $id): int {
        $pdo = db();
        $pdo->prepare("UPDATE artisti SET plays = plays + 1 WHERE id = ?")->execute([$id]);
        $st = $pdo->prepare("SELECT plays FROM artisti WHERE id = ?");
        $st->execute([$id]);
        return (int)$st->fetchColumn();
    }

    public static function inserisci(Artista $a): int {
        $pdo = db();
        $st = $pdo->prepare("INSERT INTO artisti (nome, slug, livello, stile, bio, progetto, brano, plays, monthly_listeners, link, link_soundcloud, link_youtube, link_instagram, link_spotify, avatar_url, note, is_goat)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $st->execute([
            $a->nome, $a->slug, $a->livello, $a->stile, $a->bio, $a->progetto, $a->brano,
            $a->plays, $a->monthly_listeners, $a->link, $a->link_soundcloud, $a->link_youtube,
            $a->link_instagram, $a->link_spotify, $a->avatar_url, $a->note, $a->is_goat ? 1 : 0
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function aggiorna(Artista $a): void {
        $st = db()->prepare("UPDATE artisti SET 
            nome=?, slug=?, livello=?, stile=?, bio=?, progetto=?, brano=?, plays=?, monthly_listeners=?,
            link=?, link_soundcloud=?, link_youtube=?, link_instagram=?, link_spotify=?, avatar_url=?, note=?, is_goat=?
            WHERE id=?");
        $st->execute([
            $a->nome, $a->slug, $a->livello, $a->stile, $a->bio, $a->progetto, $a->brano,
            $a->plays, $a->monthly_listeners, $a->link, $a->link_soundcloud, $a->link_youtube,
            $a->link_instagram, $a->link_spotify, $a->avatar_url, $a->note, $a->is_goat ? 1 : 0, $a->id
        ]);
    }

    public static function elimina(int $id): void {
        db()->prepare("DELETE FROM artisti WHERE id = ?")->execute([$id]);
    }
}
