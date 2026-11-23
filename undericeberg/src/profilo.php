<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Post.php';

$currUser = richiedi_login();
$messaggio = '';
$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');
    $badge = trim($_POST['badge'] ?? $currUser['badge']);

    $pdo = db();
    $st = $pdo->prepare("UPDATE utenti SET bio = ?, badge = ? WHERE id = ?");
    $st->execute([$bio, $badge, $currUser['id']]);

    $messaggio = 'Profilo aggiornato con successo!';
    // Refresh user
    $currUser = utente_corrente();
}

$stMieiPost = db()->prepare("SELECT p.*, 
    (SELECT COUNT(*) FROM commenti c WHERE c.post_id = p.id) AS commenti_count 
    FROM post p WHERE utente_id = ? ORDER BY p.id DESC");
$stMieiPost->execute([$currUser['id']]);
$mieiPost = $stMieiPost->fetchAll();

$titolo = 'Profilo di ' . $currUser['username'];
include 'head.php';
?>

<div class="layout-cols">
  <div>
    <div class="side-card" style="margin-bottom: 2rem;">
      <div style="display: flex; gap: 1.25rem; align-items: center; margin-bottom: 1.5rem;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: <?= h($currUser['avatar_color']) ?>; display: flex; align-items: center; justify-content: center; font-family: var(--font-display); font-size: 1.8rem; color: #000;">
          <?= strtoupper(substr($currUser['username'], 0, 1)) ?>
        </div>
        <div>
          <h1 style="font-family: var(--font-display); font-size: 1.8rem; color: #fff; margin-bottom: 0.25rem;">
            u/<?= h($currUser['username']) ?>
          </h1>
          <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span class="badge-tag" style="color: var(--accent-lime); font-size: 11px;"><?= h($currUser['badge']) ?></span>
            <span style="font-size: 12px; color: var(--text-dim);">Iscritto: <?= tempo_fa($currUser['creato_il']) ?></span>
          </div>
        </div>
      </div>

      <?php if (!empty($messaggio)): ?>
        <div class="alert-ok"><?= h($messaggio) ?></div>
      <?php endif; ?>

      <!-- FORM MODIFICA PROFILO -->
      <form method="post" style="border-top: 1px solid var(--border); padding-top: 1.5rem;">
        <div class="form-group">
          <label for="badge">Badge di Appartenenza</label>
          <select id="badge" name="badge">
            <option value="113 Believer" <?= $currUser['badge'] === '113 Believer' ? 'selected' : '' ?>>👑 113 Believer</option>
            <option value="OG Listener" <?= $currUser['badge'] === 'OG Listener' ? 'selected' : '' ?>>🎧 OG Listener</option>
            <option value="FL Studio Warrior" <?= $currUser['badge'] === 'FL Studio Warrior' ? 'selected' : '' ?>>🎛️ FL Studio Warrior</option>
            <option value="Gatekeeper Emeritus" <?= $currUser['badge'] === 'Gatekeeper Emeritus' ? 'selected' : '' ?>>🔒 Gatekeeper Emeritus</option>
            <option value="Unemployed Critic" <?= $currUser['badge'] === 'Unemployed Critic' ? 'selected' : '' ?>>📝 Unemployed Critic</option>
            <option value="Rage Scientist" <?= $currUser['badge'] === 'Rage Scientist' ? 'selected' : '' ?>>⚡ Rage Scientist</option>
            <option value="SoundCloud Lurker" <?= $currUser['badge'] === 'SoundCloud Lurker' ? 'selected' : '' ?>>🌙 SoundCloud Lurker</option>
          </select>
        </div>

        <div class="form-group">
          <label for="bio">Biografia / Credo Personale</label>
          <textarea id="bio" name="bio" rows="3" placeholder="Scrivi una bio per spiegare il tuo percorso nel sottosuolo..."><?= h($currUser['bio'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn lime sm">Salva Profilo</button>
      </form>
    </div>

    <!-- I MIEI POST -->
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.4rem; color: #fff; margin-bottom: 1rem;">
        Le Mie Discussioni (<?= count($mieiPost) ?>)
      </h2>

      <?php if (empty($mieiPost)): ?>
        <div class="side-card" style="text-align: center; padding: 2.5rem 1rem;">
          <p style="color: var(--text-muted); margin-bottom: 0.75rem;">Non hai ancora aperto nessuna discussione.</p>
          <a href="nuovo_post.php" class="btn lime sm">+ Apri il tuo primo post</a>
        </div>
      <?php else: ?>
        <?php foreach ($mieiPost as $p): ?>
          <?php $flairData = PostRepo::FLAIRS[$p['flair']] ?? ['colore' => '#a3e635', 'bg' => 'rgba(163,230,53,0.15)', 'icon' => '💬']; ?>
          <article class="post-card">
            <div class="upvote-col">
              <span class="upvote-num"><?= $p['upvotes'] ?></span>
            </div>
            <div class="post-main">
              <div class="post-meta">
                <span class="flair-pill" style="background: <?= $flairData['bg'] ?>; color: <?= $flairData['colore'] ?>; border: 1px solid <?= $flairData['colore'] ?>;">
                  <?= $flairData['icon'] ?> <?= h($p['flair']) ?>
                </span>
                <span><?= tempo_fa($p['creato_il']) ?></span>
              </div>
              <a href="post.php?id=<?= $p['id'] ?>" class="post-title-link"><?= h($p['titolo']) ?></a>
              <div class="post-footer">
                <span>💬 <?= $p['commenti_count'] ?> commenti</span>
                <a href="post.php?id=<?= $p['id'] ?>">Visualizza →</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <aside class="sidebar">
    <div class="side-card">
      <div class="side-title">
        <span>Sessione</span>
      </div>
      <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 1rem;">
        Email collegata: <strong><?= h($currUser['email']) ?></strong><br>
        Ruolo: <strong><?= strtoupper($currUser['ruolo']) ?></strong>
      </p>
      <a href="logout.php" class="btn danger sm" style="width: 100%; justify-content: center;">Disconnetti</a>
    </div>
  </aside>
</div>

<?php include 'foot.php'; ?>
