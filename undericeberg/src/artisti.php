<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';

$filtro = $_GET['livello'] ?? '';
if (!array_key_exists($filtro, Artista::LIVELLI)) $filtro = '';

$q = trim($_GET['q'] ?? '');
$ordina = $_GET['ordina'] ?? 'iceberg';

$artisti = ArtistaRepo::tutti($filtro ?: null, $q ?: null, $ordina);

$titolo = 'Archivio Artisti';
$attiva = 'artisti';
include 'head.php';
?>

<div style="margin-bottom: 2.25rem;">
  <div class="hero-tagline">
    <span>🗂️ REGISTRO UFFICIALE DEL SOTTOSUOLO</span>
    <span>•</span>
    <span><?= count($artisti) ?> PROFILI CENSITI</span>
    <span>•</span>
    <span>FOTO & LINK VERIFICATI</span>
  </div>
  <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.25rem;">
    <div>
      <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.2rem); text-transform: uppercase; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.03em;">
        Archivio Artisti
      </h1>
      <p style="color: var(--text-secondary); font-size: 15px; max-width: 680px;">
        Sfoglia i profili reali dell'underground italiano: SoundCloud rappers, rage artists, producers e il GOAT assoluto Layyd.
      </p>
    </div>
    <a href="aggiungi.php" class="btn lime">+ Segnala Artista</a>
  </div>
</div>

<!-- BARRA FILTRI E RICERCA -->
<div style="background: var(--bg-card); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid var(--border); border-radius: var(--radius-card); padding: 1.15rem 1.35rem; margin-bottom: 2rem; display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center; box-shadow: var(--card-shadow);">
  <form method="get" action="artisti.php" style="display: flex; gap: 0.5rem; flex: 1; max-width: 480px;">
    <?php if ($filtro): ?><input type="hidden" name="livello" value="<?= h($filtro) ?>"><?php endif; ?>
    <?php if ($ordina): ?><input type="hidden" name="ordina" value="<?= h($ordina) ?>"><?php endif; ?>
    <input type="text" name="q" placeholder="Cerca artista, stile (rage, pluggnb, jerk)..." value="<?= h($q) ?>" 
           style="flex: 1; background: var(--bg-elevated); border: 1px solid var(--border); border-radius: 8px; padding: 0.6rem 0.95rem; color: #fff; font-size: 14px;">
    <button class="btn sm sec">Cerca</button>
    <?php if ($q): ?>
      <a href="artisti.php<?= $filtro ? '?livello='.urlencode($filtro) : '' ?>" class="btn sm sec">Reset</a>
    <?php endif; ?>
  </form>

  <div style="display: flex; gap: 0.45rem; flex-wrap: wrap; align-items: center;">
    <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-muted); margin-right: 0.25rem;">ORDINA:</span>
    <a href="artisti.php?ordina=iceberg<?= $filtro ? '&livello='.urlencode($filtro) : '' ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
       class="btn sm <?= $ordina === 'iceberg' ? 'lime' : 'sec' ?>">Iceberg</a>
    <a href="artisti.php?ordina=plays<?= $filtro ? '&livello='.urlencode($filtro) : '' ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
       class="btn sm <?= $ordina === 'plays' ? 'lime' : 'sec' ?>">Ascolti</a>
    <a href="artisti.php?ordina=nome<?= $filtro ? '&livello='.urlencode($filtro) : '' ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
       class="btn sm <?= $ordina === 'nome' ? 'lime' : 'sec' ?>">A-Z</a>
  </div>
</div>

<!-- FILTRI LIVELLO PILLS -->
<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
  <a href="artisti.php<?= $q ? '?q='.urlencode($q) : '' ?>" 
     class="flair-pill" style="padding: 6px 14px; font-size: 12px; border: 1px solid <?= !$filtro ? 'var(--text-main)' : 'var(--border)' ?>; background: <?= !$filtro ? 'var(--text-main)' : 'var(--bg-card)' ?>; color: <?= !$filtro ? '#000' : 'var(--text-muted)' ?>;">
    TUTTI (<?= count(ArtistaRepo::tutti(null, $q ?: null)) ?>)
  </a>
  <?php foreach (Artista::LIVELLI as $liv => $metri): ?>
    <a href="artisti.php?livello=<?= $liv ?><?= $q ? '&q='.urlencode($q) : '' ?>" 
       class="flair-pill" style="padding: 6px 14px; font-size: 12px; border: 1px solid <?= $filtro === $liv ? 'var(--accent-cyan)' : 'var(--border)' ?>; background: <?= $filtro === $liv ? 'var(--accent-cyan)' : 'var(--bg-card)' ?>; color: <?= $filtro === $liv ? '#000' : 'var(--text-main)' ?>;">
      <?= strtoupper($liv) ?> (<?= $metri ?>)
    </a>
  <?php endforeach; ?>
</div>

<!-- GRID ARTISTI -->
<?php if (empty($artisti)): ?>
  <div class="side-card" style="text-align: center; padding: 4rem 1rem;">
    <p style="color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1.25rem;">
      Nessun artista trovato nell'archivio con i criteri specificati.
    </p>
    <a href="aggiungi.php" class="btn lime">+ Aggiungilo tu all'archivio</a>
  </div>
<?php else: ?>
  <div class="artists-grid">
    <?php foreach ($artisti as $a): ?>
      <?php $avatar = $a->avatar(); ?>
      <article class="artist-card <?= $a->is_goat ? 'goat' : '' ?>">
        
        <!-- COVER / AVATAR WRAPPER -->
        <a href="artista.php?id=<?= $a->id ?>" class="artist-card-cover-wrap">
          <?php if ($avatar): ?>
            <img src="<?= h($avatar) ?>" alt="<?= h($a->nome) ?>" class="artist-card-cover" loading="lazy">
          <?php else: ?>
            <div class="artist-card-cover-fallback">
              <?= strtoupper(substr($a->nome, 0, 1)) ?>
            </div>
          <?php endif; ?>
          
          <div class="artist-card-gradient-overlay"></div>
          
          <!-- DEPTH BADGE -->
          <div class="artist-card-depth-badge">
            <?= $a->profondita() ?> • <?= strtoupper($a->livello) ?>
          </div>

          <!-- GOAT BADGE -->
          <?php if ($a->is_goat): ?>
            <div class="artist-card-goat-badge">
              👑 THE GOAT
            </div>
          <?php endif; ?>
        </a>

        <!-- CARD BODY -->
        <div class="artist-card-body">
          <h3 class="artist-card-title">
            <a href="artista.php?id=<?= $a->id ?>">
              <?= h($a->nome) ?>
            </a>
          </h3>

          <?php if ($a->stile): ?>
            <div class="artist-card-genre">
              <?= h($a->stile) ?>
            </div>
          <?php endif; ?>

          <?php if ($a->is_goat): ?>
            <div style="font-family: var(--font-mono); font-size: 11.5px; color: var(--accent-gold); margin-bottom: 0.5rem; font-weight: 600;">
              Progetto: camelCase • nonSoCosaSia!
            </div>
          <?php elseif ($a->progetto || $a->brano): ?>
            <div style="font-family: var(--font-mono); font-size: 11px; color: var(--text-dim); margin-bottom: 0.5rem;">
              <?= h($a->progetto ? 'Album: ' . $a->progetto : 'Track: ' . $a->brano) ?>
            </div>
          <?php endif; ?>

          <?php if ($a->bio): ?>
            <p class="artist-card-bio">
              <?= h($a->bio) ?>
            </p>
          <?php endif; ?>

          <div class="artist-card-footer">
            <!-- ASCOLTI & PROFILO LINK -->
            <div class="artist-card-stats-row">
              <div>
                <span style="color: var(--text-dim);">Ascolti:</span>
                <strong style="color: <?= $a->is_goat ? 'var(--accent-gold)' : '#fff' ?>; font-family: var(--font-mono);">
                  <?= $a->plays ? format_plays($a->plays) : ($a->monthly_listeners ? number_format($a->monthly_listeners) . ' ML' : 'Underground') ?>
                </strong>
              </div>
              <a href="artista.php?id=<?= $a->id ?>" class="btn sm sec">Profilo →</a>
            </div>

            <!-- STREAMING BADGES LINKS -->
            <div class="stream-pills-row">
              <?php if ($a->link_spotify): ?>
                <a href="<?= h($a->link_spotify) ?>" target="_blank" rel="noopener" class="stream-pill spotify" title="Ascolta su Spotify">
                  <span>🟢</span> Spotify
                </a>
              <?php endif; ?>

              <?php if ($a->link_youtube): ?>
                <a href="<?= h($a->link_youtube) ?>" target="_blank" rel="noopener" class="stream-pill youtube" title="Guarda su YouTube">
                  <span>▶</span> YouTube
                </a>
              <?php endif; ?>

              <?php if ($a->link_soundcloud): ?>
                <a href="<?= h($a->link_soundcloud) ?>" target="_blank" rel="noopener" class="stream-pill soundcloud" title="SoundCloud">
                  <span>☁</span> SC
                </a>
              <?php endif; ?>

              <?php if ($a->link_instagram): ?>
                <a href="<?= h($a->link_instagram) ?>" target="_blank" rel="noopener" class="stream-pill instagram" title="Instagram">
                  <span>📷</span> IG
                </a>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include 'foot.php'; ?>
