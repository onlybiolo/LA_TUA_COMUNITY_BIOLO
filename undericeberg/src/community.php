<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Post.php';

$currUser = utente_corrente();
$currUserId = $currUser ? (int)$currUser['id'] : null;

$sort = $_GET['sort'] ?? 'hot';
$flairFiltro = $_GET['flair'] ?? null;
if (!array_key_exists($flairFiltro, PostRepo::FLAIRS)) $flairFiltro = null;

$posts = PostRepo::tutti($flairFiltro, null, $sort, $currUserId);

$titolo = 'Community Underground';
$attiva = 'community';
include 'head.php';
?>

<div style="margin-bottom: 2rem;">
  <div class="hero-tagline">
    <span>🗣️ FORUM DEL SOTTOSUOLO</span>
    <span>•</span>
    <span>DISCUSSIONE SENZA CENSURE</span>
  </div>
  <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
    <div>
      <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; color: #fff; margin-bottom: 0.5rem;">
        Community Threads
      </h1>
      <p style="color: var(--text-muted); font-size: 14.5px;">
        Snippet illegali, dissing tra producer, teorie complottistiche sugli 808 e celebrazioni del sacro impero di Layyd.
      </p>
    </div>
    <a href="nuovo_post.php" class="btn lime">+ Crea Discussione</a>
  </div>
</div>

<div class="layout-cols">
  <div>
    <!-- SORTING BAR -->
    <div class="feed-header">
      <div style="display: flex; gap: 0.4rem; overflow-x: auto; padding-bottom: 0.5rem;">
        <a href="community.php?sort=<?= $sort ?>" class="flair-pill" 
           style="background: <?= !$flairFiltro ? 'var(--text-main)' : 'var(--bg-card)' ?>; color: <?= !$flairFiltro ? '#000' : 'var(--text-muted)' ?>; border: 1px solid var(--border);">
          TUTTI
        </a>
        <?php foreach (PostRepo::FLAIRS as $flairName => $fData): ?>
          <a href="community.php?sort=<?= $sort ?>&flair=<?= urlencode($flairName) ?>" class="flair-pill" 
             style="background: <?= $flairFiltro === $flairName ? $fData['colore'] : $fData['bg'] ?>; 
                    color: <?= $flairFiltro === $flairName ? '#000' : $fData['colore'] ?>; 
                    border: 1px solid <?= $fData['colore'] ?>;">
            <?= $fData['icon'] ?> <?= $flairName ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="feed-sort">
        <a href="community.php?sort=hot<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'hot' ? 'on' : '' ?>">🔥 Hot</a>
        <a href="community.php?sort=new<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'new' ? 'on' : '' ?>">⚡ New</a>
        <a href="community.php?sort=top<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'top' ? 'on' : '' ?>">🏆 Top</a>
      </div>
    </div>

    <!-- POSTS LIST -->
    <?php if (empty($posts)): ?>
      <div class="side-card" style="text-align: center; padding: 3rem 1rem;">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Nessun post trovato con questo filtro.</p>
        <a href="nuovo_post.php" class="btn lime">+ Inizia la conversazione</a>
      </div>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <?php $flairData = PostRepo::FLAIRS[$p['flair']] ?? ['colore' => '#a3e635', 'bg' => 'rgba(163,230,53,0.15)', 'icon' => '💬']; ?>
        <article class="post-card">
          <div class="upvote-col">
            <button class="btn-upvote <?= !empty($p['ha_votato']) ? 'voted' : '' ?>" data-post-id="<?= $p['id'] ?>">▲</button>
            <span class="upvote-num <?= !empty($p['ha_votato']) ? 'voted' : '' ?>"><?= $p['upvotes'] ?></span>
          </div>

          <div class="post-main">
            <div class="post-meta">
              <span class="flair-pill" style="background: <?= $flairData['bg'] ?>; color: <?= $flairData['colore'] ?>; border: 1px solid <?= $flairData['colore'] ?>;">
                <?= $flairData['icon'] ?> <?= h($p['flair']) ?>
              </span>
              <span>postato da</span>
              <span class="author-pill" style="color: <?= h($p['avatar_color']) ?>">
                u/<?= h($p['username']) ?>
              </span>
              <span class="badge-tag"><?= h($p['badge']) ?></span>
              <span>•</span>
              <span><?= tempo_fa($p['creato_il']) ?></span>
              <?php if (!empty($p['artista_nome'])): ?>
                <span>•</span>
                <a href="artista.php?slug=<?= h($p['artista_slug']) ?>" class="post-artist-chip">
                  <?php if (!empty($p['artista_avatar'])): ?>
                    <img src="<?= h($p['artista_avatar']) ?>" alt="<?= h($p['artista_nome']) ?>">
                  <?php endif; ?>
                  <span><?= h($p['artista_nome']) ?></span>
                  <?php if (!empty($p['artista_is_goat'])): ?>👑<?php endif; ?>
                </a>
              <?php endif; ?>
            </div>

            <a href="post.php?id=<?= $p['id'] ?>" class="post-title-link">
              <?= h($p['titolo']) ?>
            </a>

            <p class="post-snippet">
              <?= nl2br(h(mb_strimwidth($p['contenuto'], 0, 300, '...'))) ?>
            </p>

            <div class="post-footer">
              <a href="post.php?id=<?= $p['id'] ?>" class="comments-count-btn">
                <span>💬</span> <strong><?= $p['commenti_count'] ?></strong> commenti
              </a>
              <a href="post.php?id=<?= $p['id'] ?>">Condividi</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="side-card">
      <div class="side-title">
        <span>Regolamento Sottosuolo</span>
      </div>
      <ol style="font-size: 13px; color: var(--text-muted); line-height: 1.6; padding-left: 1.2rem;">
        <li>Non chiedere "chi è questo" se ha meno di 50 ascolti: fai finta di conoscerlo da 3 anni.</li>
        <li>I preset stock di FL Studio sono tollerati solo se distorsi fino al clipping.</li>
        <li>Layyd ha 113 ascolti storici. Il numero non si discute.</li>
        <li>Gatekeep con moderazione, ma non troppo.</li>
      </ol>
    </div>

    <div class="side-card">
      <div class="side-title">
        <span>Top Flairs</span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 0.4rem;">
        <?php foreach (PostRepo::FLAIRS as $fName => $fD): ?>
          <a href="community.php?flair=<?= urlencode($fName) ?>" style="display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0;">
            <span style="color: <?= $fD['colore'] ?>;"><?= $fD['icon'] ?> <?= $fName ?></span>
            <span style="color: var(--text-dim);">→</span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </aside>
</div>

<?php include 'foot.php'; ?>
