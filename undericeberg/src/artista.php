<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';
require_once __DIR__ . '/Post.php';

$id = (int)($_GET['id'] ?? 0);
$slug = trim($_GET['slug'] ?? '');

$a = null;
if ($id > 0) {
    $a = ArtistaRepo::trova($id);
} elseif ($slug !== '') {
    $a = ArtistaRepo::trovaPerSlug($slug);
}

if (!$a) {
    header('Location: artisti.php');
    exit;
}

$tracce = ArtistaRepo::tracce($a->id);
$postsCollegati = PostRepo::tutti(null, $a->id);
$avatar = $a->avatar();

$titolo = $a->nome . ($a->is_goat ? ' (IL GOAT)' : '');
$attiva = $a->is_goat ? 'layyd' : 'artisti';
include 'head.php';
?>

<!-- ARTIST PROFILE HEADER -->
<div class="artist-profile-header" style="<?= $a->is_goat ? 'border-color: rgba(250, 204, 21, 0.45); background: linear-gradient(135deg, rgba(250, 204, 21, 0.09) 0%, rgba(18, 20, 30, 0.95) 100%); box-shadow: 0 0 50px -10px rgba(250, 204, 21, 0.2);' : '' ?>">
  <div class="artist-avatar-wrap">
    <?php if ($avatar): ?>
      <img src="<?= h($avatar) ?>" alt="<?= h($a->nome) ?>">
    <?php else: ?>
      <span><?= strtoupper(substr($a->nome, 0, 1)) ?></span>
    <?php endif; ?>
  </div>

  <div class="artist-info">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-bottom: 0.5rem;">
      <span class="depth-meter-tag" style="background: var(--bg-elevated); padding: 3px 8px; border-radius: 4px; font-weight: 700;">
        <?= $a->profondita() ?> • <?= strtoupper($a->livello) ?>
      </span>
      <?php if ($a->is_goat): ?>
        <span class="goat-badge">👑 PLATFORM GOAT</span>
      <?php endif; ?>
      <?php if ($a->stile): ?>
        <span class="artist-genre-pill"><?= h($a->stile) ?></span>
      <?php endif; ?>
    </div>

    <h1 class="artist-name-title">
      <?= h($a->nome) ?>
      <?php if ($a->is_goat): ?>
        <span style="font-size: 1.8rem;" title="GOAT">👑</span>
      <?php endif; ?>
    </h1>

    <?php if ($a->is_goat): ?>
      <div style="font-family: var(--font-mono); font-size: 13.5px; color: var(--accent-gold); margin-bottom: 0.75rem; font-weight: 600;">
        Autore del leggendario progetto <strong>camelCase</strong> • Traccia cult <strong>nonSoCosaSia!</strong>
      </div>
    <?php elseif ($a->progetto): ?>
      <div style="font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); margin-bottom: 0.75rem;">
        Progetto di punta: <strong style="color: #fff;"><?= h($a->progetto) ?></strong>
      </div>
    <?php endif; ?>

    <p class="artist-bio-text">
      <?= nl2br(h($a->bio ?: ($a->note ?: 'Nessuna biografia ufficiale nel registro. L\'artista preferisce far parlare i propri 808.'))) ?>
    </p>

    <!-- STREAMING QUICK LINKS -->
    <div style="display: flex; gap: 0.65rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
      <?php if ($a->link_spotify): ?>
        <a href="<?= h($a->link_spotify) ?>" target="_blank" rel="noopener" class="stream-pill spotify" style="font-size: 12px; padding: 6px 14px;">
          <span>🟢</span> Profilo Spotify ↗
        </a>
      <?php endif; ?>

      <?php if ($a->link_youtube): ?>
        <a href="<?= h($a->link_youtube) ?>" target="_blank" rel="noopener" class="stream-pill youtube" style="font-size: 12px; padding: 6px 14px;">
          <span>▶</span> Canale / Video YouTube ↗
        </a>
      <?php endif; ?>

      <?php if ($a->link_soundcloud): ?>
        <a href="<?= h($a->link_soundcloud) ?>" target="_blank" rel="noopener" class="stream-pill soundcloud" style="font-size: 12px; padding: 6px 14px;">
          <span>☁</span> SoundCloud Ufficiale ↗
        </a>
      <?php endif; ?>

      <?php if ($a->link_instagram): ?>
        <a href="<?= h($a->link_instagram) ?>" target="_blank" rel="noopener" class="stream-pill instagram" style="font-size: 12px; padding: 6px 14px;">
          <span>📷</span> Instagram Ufficiale ↗
        </a>
      <?php endif; ?>
    </div>

    <!-- METRICHE E STATISTICHE -->
    <div class="artist-stats-grid">
      <div class="stat-box">
        <span class="val" id="<?= $a->is_goat ? 'layyd-play-count' : '' ?>" style="<?= $a->is_goat ? 'color: var(--accent-gold);' : '' ?>">
          <?= $a->plays ? format_plays($a->plays) : ($a->is_goat ? '113' : '—') ?>
        </span>
        <span class="lbl"><?= $a->is_goat ? 'Ascolti Storici Certificati' : 'Plays Registrati' ?></span>
      </div>

      <div class="stat-box">
        <span class="val"><?= $a->monthly_listeners ? number_format($a->monthly_listeners) : ($a->is_goat ? '38' : 'Underground') ?></span>
        <span class="lbl">Ascoltatori Mensili</span>
      </div>

      <div class="stat-box">
        <span class="val"><?= count($tracce) ?: ($a->brano ? 1 : 0) ?></span>
        <span class="lbl">Tracce Censite</span>
      </div>

      <div class="stat-box">
        <span class="val"><?= $a->profondita() ?></span>
        <span class="lbl">Profondità Iceberg</span>
      </div>
    </div>
  </div>

  <!-- ACTION BUTTONS -->
  <div style="display: flex; flex-direction: column; gap: 0.65rem; align-self: flex-start; margin-left: auto;">
    <?php if ($a->is_goat): ?>
      <button id="btn-play-layyd" data-artista-id="<?= $a->id ?>" class="btn-play-goat">
        <span>▶</span> Ascolta nonSoCosaSia! (+1 Play)
      </button>
    <?php endif; ?>
    <a href="nuovo_post.php?artista_id=<?= $a->id ?>" class="btn sm lime">+ Apri Discussione</a>
    <a href="modifica.php?id=<?= $a->id ?>" class="btn sm sec">Modifica Scheda</a>
  </div>
</div>

<?php if ($a->is_goat): ?>
<!-- SCHERMATA PROVA LORE GOAT -->
<div class="side-card" style="border-color: rgba(250, 204, 21, 0.4); background: rgba(250, 204, 21, 0.04); margin-bottom: 2.5rem; box-shadow: 0 0 35px -10px rgba(250, 204, 21, 0.15);">
  <div style="display: flex; gap: 2rem; flex-wrap: wrap; align-items: center;">
    <div style="max-width: 380px; border-radius: 12px; overflow: hidden; border: 1px solid rgba(250, 204, 21, 0.35); box-shadow: 0 8px 30px rgba(0,0,0,0.6);">
      <img src="assets/layyd_proof.png" alt="Layyd Google Proof" style="width: 100%; display: block;">
    </div>
    <div style="flex: 1; min-width: 280px;">
      <div class="goat-badge" style="margin-bottom: 0.5rem;">PROVA STORICA CERTIFICATA</div>
      <h3 style="font-family: var(--font-display); font-size: 1.45rem; color: var(--accent-gold); margin-bottom: 0.65rem; letter-spacing: -0.01em;">
        LA VERIFICA UFFICIALE: @ITSLAYYDBABY
      </h3>
      <p style="font-size: 14.5px; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.25rem;">
        <em>"Artista e produttore emergente della scena musicale underground. Produce principalmente trap, hood trap e sonorità sperimentali. 
        Tra i suoi contenuti, il brano underground intitolato NonSoCosaSia."</em>
      </p>
      <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
        <a href="https://www.instagram.com/itslayydbaby" target="_blank" rel="noopener" class="stream-pill instagram" style="padding: 6px 14px;">
          <span>📷</span> Instagram (@itslayydbaby) ↗
        </a>
        <a href="https://soundcloud.com/search?q=itslayydbaby" target="_blank" rel="noopener" class="stream-pill soundcloud" style="padding: 6px 14px;">
          <span>☁</span> SoundCloud ↗
        </a>
        <a href="https://www.youtube.com/results?search_query=layyd+nonSoCosaSia" target="_blank" rel="noopener" class="stream-pill youtube" style="padding: 6px 14px;">
          <span>▶</span> YouTube Search ↗
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- DISCOGRAFIA E TRACCE -->
<div style="margin-bottom: 2.75rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.15rem;">
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.45rem; color: #fff; letter-spacing: -0.02em;">
        Tracce & Rilasci Ufficiali
      </h2>
      <p style="color: var(--text-muted); font-size: 13px;">Registri audio, crediti di produzione e streaming link</p>
    </div>
  </div>

  <?php if (empty($tracce)): ?>
    <div class="side-card">
      <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
          <strong style="color: #fff; font-size: 1.05rem;"><?= h($a->brano ?: 'Traccia Principale') ?></strong>
          <div style="font-size: 12.5px; color: var(--text-muted);"><?= h($a->progetto ?: 'Single') ?></div>
        </div>
        <div>
          <?php if ($a->link_youtube): ?>
            <a href="<?= h($a->link_youtube) ?>" target="_blank" rel="noopener" class="stream-pill youtube">Ascolta su YouTube ↗</a>
          <?php elseif ($a->link_spotify): ?>
            <a href="<?= h($a->link_spotify) ?>" target="_blank" rel="noopener" class="stream-pill spotify">Ascolta su Spotify ↗</a>
          <?php elseif ($a->link): ?>
            <a href="<?= h($a->link) ?>" target="_blank" rel="noopener" class="stream-pill soundcloud">SoundCloud ↗</a>
          <?php else: ?>
            <span class="badge-tag">Snippet non archiviato</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="tracks-table">
        <thead>
          <tr>
            <th style="width: 40px;">#</th>
            <th>Titolo</th>
            <th>Progetto</th>
            <th>Anno</th>
            <th>Produttore</th>
            <th>Ascolti</th>
            <th style="text-align: right;">Stream</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($tracce as $idx => $t): ?>
            <tr>
              <td style="font-family: var(--font-mono); color: var(--text-dim);"><?= $idx + 1 ?></td>
              <td>
                <strong style="color: #fff;"><?= h($t['titolo']) ?></strong>
                <?php if (!empty($t['is_hit'])): ?>
                  <span class="badge-tag" style="background: var(--accent-gold); color: #000; font-weight: bold; margin-left: 0.5rem;">CULT HIT</span>
                <?php endif; ?>
              </td>
              <td style="color: var(--text-muted);"><?= h($t['progetto'] ?: 'Single') ?></td>
              <td style="font-family: var(--font-mono); font-size: 12px; color: var(--text-muted);"><?= $t['anno'] ?></td>
              <td style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-cyan);"><?= h($t['produttore'] ?: 'Self-produced') ?></td>
              <td style="font-family: var(--font-mono); font-weight: bold; color: <?= $a->is_goat ? 'var(--accent-gold)' : '#fff' ?>;">
                <?= format_plays($t['plays']) ?>
              </td>
              <td style="text-align: right;">
                <?php if ($a->is_goat): ?>
                  <button id="btn-play-layyd-track-<?= $t['id'] ?>" class="btn sm lime" onclick="document.getElementById('btn-play-layyd').click()">▶ Stream (+1)</button>
                <?php elseif (!empty($t['link_audio'])): ?>
                  <a href="<?= h($t['link_audio']) ?>" target="_blank" rel="noopener" class="stream-pill <?= strpos($t['link_audio'], 'youtube') !== false ? 'youtube' : 'spotify' ?>">
                    Ascolta ↗
                  </a>
                <?php elseif ($a->link_youtube): ?>
                  <a href="<?= h($a->link_youtube) ?>" target="_blank" rel="noopener" class="stream-pill youtube">YouTube ↗</a>
                <?php else: ?>
                  <a href="https://soundcloud.com/search?q=<?= urlencode($a->nome . ' ' . $t['titolo']) ?>" target="_blank" rel="noopener" class="stream-pill soundcloud">SoundCloud ↗</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- DISCUSSIONE DELLA COMMUNITY SULL'ARTISTA -->
<div>
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
    <div>
      <h2 style="font-family: var(--font-display); font-size: 1.45rem; color: #fff; letter-spacing: -0.02em;">
        Discussioni della Community su <?= h($a->nome) ?>
      </h2>
      <p style="color: var(--text-muted); font-size: 13px;">Thread, recensioni e teorie dai membri della community</p>
    </div>
    <a href="nuovo_post.php?artista_id=<?= $a->id ?>" class="btn sm lime">+ Apri Thread</a>
  </div>

  <?php if (empty($postsCollegati)): ?>
    <div class="side-card" style="text-align: center; padding: 2.75rem 1rem;">
      <p style="color: var(--text-muted); margin-bottom: 1rem;">
        Ancora nessuna discussione dedicata a <?= h($a->nome) ?>.
      </p>
      <a href="nuovo_post.php?artista_id=<?= $a->id ?>" class="btn sm lime">Inizia la discussione</a>
    </div>
  <?php else: ?>
    <?php foreach ($postsCollegati as $p): ?>
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
            <span>u/<?= h($p['username']) ?></span>
            <span class="badge-tag"><?= h($p['badge']) ?></span>
            <span>•</span>
            <span><?= tempo_fa($p['creato_il']) ?></span>
          </div>
          <a href="post.php?id=<?= $p['id'] ?>" class="post-title-link"><?= h($p['titolo']) ?></a>
          <p class="post-snippet"><?= nl2br(h(mb_strimwidth($p['contenuto'], 0, 180, '...'))) ?></p>
          <div class="post-footer">
            <a href="post.php?id=<?= $p['id'] ?>" class="comments-count-btn">
              <span>💬</span> <strong><?= $p['commenti_count'] ?></strong> commenti
            </a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php include 'foot.php'; ?>
