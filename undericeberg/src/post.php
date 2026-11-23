<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Post.php';

$currUser = utente_corrente();
$currUserId = $currUser ? (int)$currUser['id'] : null;

$id = (int)($_GET['id'] ?? 0);
$post = PostRepo::trova($id, $currUserId);

if (!$post) {
    header('Location: community.php');
    exit;
}

$errore = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commento'])) {
    if (!$currUser) {
        header('Location: login.php');
        exit;
    }
    $testo = trim($_POST['commento']);
    if (strlen($testo) < 2) {
        $errore = 'Il commento è troppo corto.';
    } else {
        PostRepo::aggiungiCommento($post['id'], (int)$currUser['id'], $testo);
        header('Location: post.php?id=' . $post['id']);
        exit;
    }
}

$commenti = PostRepo::commenti($post['id'], $currUserId);
$flairData = PostRepo::FLAIRS[$post['flair']] ?? ['colore' => '#a3e635', 'bg' => 'rgba(163,230,53,0.15)', 'icon' => '💬'];

$titolo = $post['titolo'];
$attiva = 'community';
include 'head.php';
?>

<div style="margin-bottom: 1.5rem;">
  <a href="community.php" style="font-family: var(--font-mono); font-size: 13px; color: var(--text-dim);">
    ← Torna alle discussioni
  </a>
</div>

<div class="layout-cols">
  <div>
    <!-- SCHEDA DEL POST -->
    <article class="post-card" style="margin-bottom: 1.5rem;">
      <div class="upvote-col">
        <button class="btn-upvote <?= !empty($post['ha_votato']) ? 'voted' : '' ?>" data-post-id="<?= $post['id'] ?>">▲</button>
        <span class="upvote-num <?= !empty($post['ha_votato']) ? 'voted' : '' ?>"><?= $post['upvotes'] ?></span>
      </div>

      <div class="post-main">
        <div class="post-meta">
          <span class="flair-pill" style="background: <?= $flairData['bg'] ?>; color: <?= $flairData['colore'] ?>; border: 1px solid <?= $flairData['colore'] ?>;">
            <?= $flairData['icon'] ?> <?= h($post['flair']) ?>
          </span>
          <span>postato da</span>
          <span class="author-pill" style="color: <?= h($post['avatar_color']) ?>">
            u/<?= h($post['username']) ?>
          </span>
          <span class="badge-tag"><?= h($post['badge']) ?></span>
          <span>•</span>
          <span><?= tempo_fa($post['creato_il']) ?></span>
        </div>

        <h1 style="font-family: var(--font-display); font-size: 1.75rem; color: #fff; line-height: 1.25; margin: 0.25rem 0;">
          <?= h($post['titolo']) ?>
        </h1>

        <?php if (!empty($post['artista_nome'])): ?>
          <div style="background: var(--bg-elevated); border: 1px solid var(--border); border-radius: 8px; padding: 0.6rem 0.95rem; display: flex; align-items: center; justify-content: space-between; margin: 0.75rem 0;">
            <div style="font-size: 13.5px; display: flex; align-items: center; gap: 0.55rem;">
              <?php if (!empty($post['artista_avatar'])): ?>
                <img src="<?= h($post['artista_avatar']) ?>" alt="<?= h($post['artista_nome']) ?>" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.15);">
              <?php endif; ?>
              <span>Focus Artista:</span>
              <strong style="color: #fff;"><?= h($post['artista_nome']) ?></strong>
              <?php if (!empty($post['artista_is_goat'])): ?>
                <span class="badge-tag" style="background: var(--accent-gold); color: #000; font-weight: bold;">👑 GOAT</span>
              <?php endif; ?>
            </div>
            <a href="artista.php?slug=<?= h($post['artista_slug']) ?>" class="btn sm sec">Vedi Profilo →</a>
          </div>
        <?php endif; ?>

        <div style="font-size: 15.5px; line-height: 1.7; color: var(--text-main); margin: 1rem 0; white-space: pre-line;">
          <?= nl2br(h($post['contenuto'])) ?>
        </div>

        <?php if (!empty($post['link_esterno'])): ?>
          <div style="margin: 0.75rem 0;">
            <a href="<?= h($post['link_esterno']) ?>" target="_blank" rel="noopener" class="btn sm sec">
              Link Allegato: <?= h($post['link_esterno']) ?> ↗
            </a>
          </div>
        <?php endif; ?>

        <div class="post-footer">
          <span>💬 <strong><?= count($commenti) ?></strong> risposte</span>
        </div>
      </div>
    </article>

    <!-- SEZIONE COMMENTI -->
    <div class="comments-section">
      <h3 style="font-family: var(--font-display); font-size: 1.25rem; color: #fff; margin-bottom: 1.25rem;">
        Discussione (<?= count($commenti) ?>)
      </h3>

      <?php if (!empty($errore)): ?>
        <p class="alert-err"><?= h($errore) ?></p>
      <?php endif; ?>

      <!-- FORM NUOVO COMMENTO -->
      <?php if ($currUser): ?>
        <form method="post" class="comment-form">
          <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
            <span>Commenta come:</span>
            <strong style="color: <?= h($currUser['avatar_color']) ?>">u/<?= h($currUser['username']) ?></strong>
            <span class="badge-tag"><?= h($currUser['badge']) ?></span>
          </div>
          <textarea name="commento" rows="3" placeholder="Scrivi una risposta senza compromessi morali..." required></textarea>
          <div style="display: flex; justify-content: flex-end;">
            <button class="btn lime sm">Pubblica Risposta</button>
          </div>
        </form>
      <?php else: ?>
        <div style="background: var(--bg-elevated); border: 1px solid var(--border); padding: 1.25rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
          <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 0.75rem;">
            Per commentare devi autenticarti nel sottosuolo.
          </p>
          <div style="display: flex; gap: 0.5rem; justify-content: center;">
            <a href="login.php" class="btn sm sec">Accedi</a>
            <a href="registrati.php" class="btn sm lime">Registrati</a>
          </div>
        </div>
      <?php endif; ?>

      <!-- LISTA COMMENTI -->
      <?php if (empty($commenti)): ?>
        <p style="color: var(--text-dim); font-size: 14px; text-align: center; padding: 2rem 0;">
          Ancora nessun commento. Sii il primo a commentare questo capolavoro.
        </p>
      <?php else: ?>
        <?php foreach ($commenti as $c): ?>
          <div class="comment-item">
            <div style="display: flex; flex-direction: column; align-items: center; width: 36px;">
              <button class="btn-upvote <?= !empty($c['ha_votato']) ? 'voted' : '' ?>" data-comment-id="<?= $c['id'] ?>" style="font-size: 0.9rem;">
                ▲
              </button>
              <span class="comm-upvote-count" style="font-family: var(--font-mono); font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                <?= $c['upvotes'] ?>
              </span>
            </div>

            <div class="comment-body">
              <div class="comment-header">
                <span class="user-dot" style="background: <?= h($c['avatar_color']) ?>"></span>
                <span class="author-pill" style="color: <?= h($c['avatar_color']) ?>">u/<?= h($c['username']) ?></span>
                <span class="badge-tag"><?= h($c['badge']) ?></span>
                <span>•</span>
                <span><?= tempo_fa($c['creato_il']) ?></span>
              </div>
              <div class="comment-text">
                <?= nl2br(h($c['testo'])) ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- SIDEBAR AUTORE POST -->
  <aside class="sidebar">
    <div class="side-card">
      <div class="side-title">
        <span>Informazioni Autore</span>
      </div>
      <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
        <span class="user-dot" style="width: 14px; height: 14px; background: <?= h($post['avatar_color']) ?>"></span>
        <div>
          <strong style="color: #fff; font-size: 15px;">u/<?= h($post['username']) ?></strong>
          <div style="font-size: 11.5px; color: var(--text-muted);"><?= h($post['badge']) ?></div>
        </div>
      </div>
      <?php if (!empty($post['autore_bio'])): ?>
        <p style="font-size: 13px; color: var(--text-muted); line-height: 1.4;">
          <?= nl2br(h($post['autore_bio'])) ?>
        </p>
      <?php endif; ?>
    </div>

    <div class="side-card">
      <div class="side-title">
        <span>Condividi il Verbo</span>
      </div>
      <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 1rem;">
        Non condividere sui social mainstream. Manda il link solo su gruppi Telegram chiusi.
      </p>
      <input type="text" readonly value="<?= 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost:8080') . '/post.php?id=' . $post['id'] ?>"
             style="width: 100%; font-family: var(--font-mono); font-size: 11px; background: var(--bg-elevated); border: 1px solid var(--border); padding: 6px; border-radius: 4px; color: var(--text-dim);"
             onclick="this.select();">
    </div>
  </aside>
</div>

<?php include 'foot.php'; ?>
