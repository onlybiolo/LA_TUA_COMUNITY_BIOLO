<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Artista.php';
require_once __DIR__ . '/Post.php';

$currUser = utente_corrente();
$currUserId = $currUser ? (int)$currUser['id'] : null;

$sort = $_GET['sort'] ?? 'hot';
$flairFiltro = $_GET['flair'] ?? null;
if (!array_key_exists($flairFiltro, PostRepo::FLAIRS)) $flairFiltro = null;

$posts = PostRepo::tutti($flairFiltro, null, $sort, $currUserId);
$layyd = ArtistaRepo::trovaGoat();
$totArtisti = count(ArtistaRepo::tutti());

$titolo = 'Feed & Community';
$attiva = 'feed';
include 'head.php';
?>

<section class="hero">
  <div class="hero-tagline">
    <span>● ARCHIVIO UNDERGROUND ATTIVO</span>
    <span>•</span>
    <span>ZERO ALGORITMI MAINSTREAM</span>
    <span>•</span>
    <span>SOUNDCLOUD CORE</span>
  </div>
  <h1>UNDERGROUND</h1>
  <p class="lead">
    La community per chi sa spiegare perché WhyTrevxr è sparito per 8 mesi, perché nessuno parla di quello snippet illegale, 
    e perché un artista con 35 ascoltatori mensili è oggettivamente superiore al 90% delle classifiche italiane.
  </p>

  <div class="hero-stats">
    <div class="stat-pill">
      <span>ARTISTI:</span>
      <strong><?= $totArtisti ?> SCHEDATI</strong>
    </div>
    <div class="stat-pill">
      <span>PROFONDITÀ:</span>
      <strong>DA 0M A -100M</strong>
    </div>
    <div class="stat-pill goat">
      <span>👑 GOAT STATUS:</span>
      <strong>LAYYD (<?= $layyd ? $layyd->plays : 113 ?> PLAYS)</strong>
    </div>
    <div class="stat-pill">
      <span>INDUSTRY PLANTS:</span>
      <strong>0% RIGOROSAMENTE RAW</strong>
    </div>
  </div>
</section>

<?php if ($layyd): ?>
<!-- THE GOAT: LAYYD SHRINE -->
<div class="goat-card">
  <div class="goat-inner-grid">
    <!-- FOTO PROVA UFFICIALE CON CORNICE ORO -->
    <a href="artista.php?id=<?= $layyd->id ?>" class="goat-photo-frame" title="Visualizza la prova storica certificata">
      <img src="assets/layyd_proof.png" alt="Layyd Official Proof">
      <div class="goat-photo-badge">👑 OFFICIAL LORE</div>
    </a>

    <!-- CONTENUTO DEL SANTUARIO -->
    <div>
      <div class="goat-header">
        <div>
          <div class="goat-badge">👑 THE GOAT OF THE PLATFORM</div>
          <h2 class="goat-title">LAYYD</h2>
          <div class="goat-subtitle">Progetto: <strong>camelCase</strong> • Traccia: <strong>nonSoCosaSia!</strong></div>
        </div>
        <a href="artista.php?id=<?= $layyd->id ?>" class="btn sm sec">Profilo Completo & Lore →</a>
      </div>

      <p class="goat-desc">
        Mentre l'industria discografica spende budget a sei zeri per fabbricare superstar sintetiche, Layyd sta silenziosamente 
        costruendo un impero sonoro a colpi di 808 scivolati e flow sghembo. 113 ascolti certificati. OGs remember.
      </p>

      <div class="goat-metric-row">
        <div class="goat-stream-box">
          <div class="goat-stream-count" id="layyd-play-count"><?= $layyd->plays ?></div>
          <div class="goat-stream-label">Ascolti Certificati</div>
        </div>

        <button id="btn-play-layyd" data-artista-id="<?= $layyd->id ?>" class="btn-play-goat">
          <span>▶</span> Ascolta nonSoCosaSia! (+1 Play)
        </button>

        <div style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-gold);">
          Target: Road to 114 plays (99.1% completato)
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="layout-cols">
  <!-- COLONNA PRINCIPALE: THREADS / COMMUNITY POSTS -->
  <div class="feed-main">
    <div class="feed-header">
      <div class="feed-title">Discussioni dal Sottosuolo</div>
      <div class="feed-sort">
        <a href="index.php?sort=hot<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'hot' ? 'on' : '' ?>">🔥 Hot</a>
        <a href="index.php?sort=new<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'new' ? 'on' : '' ?>">⚡ Recenti</a>
        <a href="index.php?sort=top<?= $flairFiltro ? '&flair='.urlencode($flairFiltro) : '' ?>" class="<?= $sort === 'top' ? 'on' : '' ?>">🏆 Top</a>
      </div>
    </div>

    <!-- FLAIR BAR -->
    <div style="display: flex; gap: 0.4rem; overflow-x: auto; padding-bottom: 0.75rem; margin-bottom: 1.25rem;">
      <a href="index.php?sort=<?= $sort ?>" class="flair-pill" style="background: <?= !$flairFiltro ? 'var(--text-main)' : 'var(--bg-card)' ?>; color: <?= !$flairFiltro ? '#000' : 'var(--text-muted)' ?>; border: 1px solid var(--border);">
        TUTTI
      </a>
      <?php foreach (PostRepo::FLAIRS as $flairName => $fData): ?>
        <a href="index.php?sort=<?= $sort ?>&flair=<?= urlencode($flairName) ?>" class="flair-pill" 
           style="background: <?= $flairFiltro === $flairName ? $fData['colore'] : $fData['bg'] ?>; 
                  color: <?= $flairFiltro === $flairName ? '#000' : $fData['colore'] ?>; 
                  border: 1px solid <?= $fData['colore'] ?>;">
          <?= $fData['icon'] ?> <?= $flairName ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- LISTA DEI POST -->
    <?php if (empty($posts)): ?>
      <div class="side-card" style="text-align: center; padding: 3rem 1rem;">
        <p style="color: var(--text-muted); margin-bottom: 1rem;">Nessuna discussione trovata con questi filtri.</p>
        <a href="nuovo_post.php" class="btn lime">+ Crea il primo post</a>
      </div>
    <?php else: ?>
      <?php foreach ($posts as $p): ?>
        <?php $flairData = PostRepo::FLAIRS[$p['flair']] ?? ['colore' => '#a3e635', 'bg' => 'rgba(163,230,53,0.15)', 'icon' => '💬']; ?>
        <article class="post-card">
          <!-- COLONNA UPVOTE REDDIT STYLE -->
          <div class="upvote-col">
            <button class="btn-upvote <?= !empty($p['ha_votato']) ? 'voted' : '' ?>" data-post-id="<?= $p['id'] ?>" title="Upvota">
              ▲
            </button>
            <span class="upvote-num <?= !empty($p['ha_votato']) ? 'voted' : '' ?>">
              <?= $p['upvotes'] ?>
            </span>
          </div>

          <!-- CONTENUTO DEL POST -->
          <div class="post-main">
            <div class="post-meta">
              <span class="flair-pill" style="background: <?= $flairData['bg'] ?>; color: <?= $flairData['colore'] ?>; border: 1px solid <?= $flairData['colore'] ?>;">
                <?= $flairData['icon'] ?> <?= h($p['flair']) ?>
              </span>
              <span>u/<?= h($p['username']) ?></span>
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
              <?= nl2br(h(mb_strimwidth($p['contenuto'], 0, 260, '...'))) ?>
            </p>

            <div class="post-footer">
              <a href="post.php?id=<?= $p['id'] ?>" class="comments-count-btn">
                <span>💬</span>
                <strong><?= $p['commenti_count'] ?></strong> commenti
              </a>
              <a href="post.php?id=<?= $p['id'] ?>">Condividi</a>
              <a href="post.php?id=<?= $p['id'] ?>">Salva</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <!-- COLONNA LATERALE (SIDEBAR) -->
  <aside class="sidebar">
    <!-- WIDGET AZIONI RAPIDE -->
    <div class="side-card" style="border-color: rgba(163,230,53,0.3); box-shadow: 0 0 20px -5px rgba(163,230,53,0.15);">
      <div class="side-title">
        <span>Community Sottosuolo</span>
      </div>
      <p style="font-size: 13.5px; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
        Unisciti alla conversazione, droppa uno snippet o spiega perché un beat senza mastering suona meglio.
      </p>
      <div style="display: grid; gap: 0.5rem;">
        <a href="nuovo_post.php" class="btn lime" style="justify-content: center;">+ Crea Discussione</a>
        <a href="aggiungi.php" class="btn sec" style="justify-content: center;">+ Segnala Nuovo Artista</a>
      </div>
    </div>

    <!-- WIDGET LIVELLI ICEBERG -->
    <div class="side-card">
      <div class="side-title">
        <span>Livelli Iceberg</span>
        <a href="iceberg.php" style="font-size: 12px; color: var(--accent-cyan);">Vedi Tutto →</a>
      </div>
      <div class="depth-levels">
        <a href="artisti.php?livello=punta" class="depth-row punta">
          <div>
            <strong>Punta</strong>
            <div style="font-size: 11px; color: var(--text-muted);">Glocky, 18K, Aira, Waze RRX</div>
          </div>
          <span class="depth-meter-tag">0 m</span>
        </a>
        <a href="artisti.php?livello=superficie" class="depth-row superficie">
          <div>
            <strong>Superficie</strong>
            <div style="font-size: 11px; color: var(--text-muted);">WhyTrevxr, Rooc Lee, Trenchmafia</div>
          </div>
          <span class="depth-meter-tag">-10 m</span>
        </a>
        <a href="artisti.php?livello=profondo" class="depth-row profondo">
          <div>
            <strong style="color: var(--accent-gold);">Profondo (GOAT Tier)</strong>
            <div style="font-size: 11px; color: rgba(250, 204, 21, 0.7);">Layyd (camelCase), 19cola</div>
          </div>
          <span class="depth-meter-tag" style="color: var(--accent-gold); font-weight: bold;">-40 m</span>
        </a>
        <a href="artisti.php?livello=abisso" class="depth-row abisso">
          <div>
            <strong>Abisso</strong>
            <div style="font-size: 11px; color: var(--text-muted);">KiloSb, Bruce Laren, Ermy</div>
          </div>
          <span class="depth-meter-tag">-100 m</span>
        </a>
      </div>
    </div>

    <!-- WIDGET LORE & SLANG -->
    <div class="side-card">
      <div class="side-title">
        <span>Dizionario del Sottosuolo</span>
      </div>
      <div class="lore-list">
        <div class="lore-item">
          <strong>113 PLAYS</strong>
          <p>Il sacro traguardo di Layyd su "nonSoCosaSia!". Più raro di un disco di platino FIMI.</p>
        </div>
        <div class="lore-item">
          <strong>PRESET MERCHANT</strong>
          <p>Produttore che usa lo stesso identico synth di Serum su ogni pezzo senza toccare una manopola.</p>
        </div>
        <div class="lore-item">
          <strong>GATEKEEPING</strong>
          <p>Dovere civico: se un artista supera 100 ascolti mensili sei moralmente obbligato a non parlarne più.</p>
        </div>
        <div class="lore-item">
          <strong>SPARIZIONE TELEGRAM</strong>
          <p>Quando un rapper cancella tutte le foto su IG per 8 mesi prima di droppare un capolavoro.</p>
        </div>
      </div>
    </div>
  </aside>
</div>

<?php include 'foot.php'; ?>
