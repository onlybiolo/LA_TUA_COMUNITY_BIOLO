<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';

$tutti = ArtistaRepo::tutti();
$perLivello = [
    'punta' => [],
    'superficie' => [],
    'profondo' => [],
    'abisso' => []
];

foreach ($tutti as $a) {
    if (isset($perLivello[$a->livello])) {
        $perLivello[$a->livello][] = $a;
    }
}

$titolo = 'Mappa dell\'Iceberg';
$attiva = 'iceberg';
include 'head.php';
?>

<div style="margin-bottom: 2.25rem;">
  <div class="hero-tagline">
    <span>🧭 TOPOGRAFIA DEL SOTTOSUOLO</span>
    <span>•</span>
    <span>QUATTRO LIVELLI DI PROFONDITÀ</span>
    <span>•</span>
    <span>CENSIMENTO VISIVO</span>
  </div>
  <h1 style="font-family: var(--font-display); font-size: clamp(2rem, 5vw, 3.4rem); text-transform: uppercase; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.03em;">
    L'Iceberg Underground
  </h1>
  <p style="color: var(--text-secondary); max-width: 720px; font-size: 1.05rem; line-height: 1.6;">
    Più scendi in basso, più i bassi 808 si saturano e il mastering diventa un vago ricordo teorico. 
    Dalla superficie riconosciuta (Glocky, 18K) fino al sanctum del GOAT Layyd (-40m) e all'abisso inesplorato dei 20 ascolti mensili.
  </p>
</div>

<!-- CONTAINER ICEBERG TIERS -->
<div class="iceberg-container">
  
  <!-- TIER 1: PUNTA -->
  <div class="iceberg-tier tier-punta">
    <div class="tier-title-row">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <span class="tier-depth-badge">0 METRI</span>
        <strong style="font-size: 1.2rem; color: #fff; letter-spacing: -0.01em;">Punta dell'Iceberg (Riconosciuti / Apex)</strong>
      </div>
      <div style="font-family: var(--font-mono); font-size: 11.5px; color: var(--text-muted);">
        Streaming solido • Pogo continuo • Conosciuti anche fuori da SoundCloud
      </div>
    </div>
    <div class="artist-tag-grid">
      <?php foreach ($perLivello['punta'] as $a): ?>
        <?php $av = $a->avatar(); ?>
        <a href="artista.php?id=<?= $a->id ?>" class="artist-tag-pill">
          <?php if ($av): ?>
            <img src="<?= h($av) ?>" alt="<?= h($a->nome) ?>" class="mini-avatar">
          <?php else: ?>
            <span class="mini-avatar-letter"><?= strtoupper(substr($a->nome, 0, 1)) ?></span>
          <?php endif; ?>
          <span><?= h($a->nome) ?></span>
          <span style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-cyan);"><?= $a->stile ? h($a->stile) : 'Trap' ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- TIER 2: SUPERFICIE -->
  <div class="iceberg-tier tier-superficie">
    <div class="tier-title-row">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <span class="tier-depth-badge">-10 METRI</span>
        <strong style="font-size: 1.2rem; color: #fff; letter-spacing: -0.01em;">Superficie (SoundCloud Core / Rage & Jerk)</strong>
      </div>
      <div style="font-family: var(--font-mono); font-size: 11.5px; color: var(--text-muted);">
        Snippet notturni • Sparizioni misteriose • Preset combat FL Studio
      </div>
    </div>
    <div class="artist-tag-grid">
      <?php foreach ($perLivello['superficie'] as $a): ?>
        <?php $av = $a->avatar(); ?>
        <a href="artista.php?id=<?= $a->id ?>" class="artist-tag-pill">
          <?php if ($av): ?>
            <img src="<?= h($av) ?>" alt="<?= h($a->nome) ?>" class="mini-avatar">
          <?php else: ?>
            <span class="mini-avatar-letter"><?= strtoupper(substr($a->nome, 0, 1)) ?></span>
          <?php endif; ?>
          <span><?= h($a->nome) ?></span>
          <span style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-cyan);"><?= $a->stile ? h($a->stile) : 'Plugg/Rage' ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- TIER 3: PROFONDO (GOAT TIER) -->
  <div class="iceberg-tier tier-profondo">
    <div class="tier-title-row">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <span class="tier-depth-badge">-40 METRI</span>
        <strong style="font-size: 1.2rem; color: #fff; letter-spacing: -0.01em;">Profondo (The GOAT Sanctuary / Adepti Veri)</strong>
      </div>
      <div style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-gold); font-weight: 600;">
        👑 Territorio di Layyd • Progetto camelCase • Zero piante industriali
      </div>
    </div>
    <div class="artist-tag-grid">
      <?php foreach ($perLivello['profondo'] as $a): ?>
        <?php $av = $a->avatar(); ?>
        <a href="artista.php?id=<?= $a->id ?>" class="artist-tag-pill <?= $a->is_goat ? 'goat' : '' ?>">
          <?php if ($av): ?>
            <img src="<?= h($av) ?>" alt="<?= h($a->nome) ?>" class="mini-avatar" style="<?= $a->is_goat ? 'border-color: var(--accent-gold);' : '' ?>">
          <?php else: ?>
            <span class="mini-avatar-letter"><?= strtoupper(substr($a->nome, 0, 1)) ?></span>
          <?php endif; ?>
          <span><?= h($a->nome) ?></span>
          <?php if ($a->is_goat): ?>
            <span style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-gold); font-weight: bold;">(113 PLAYS 👑)</span>
          <?php else: ?>
            <span style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-cyan);"><?= h($a->stile) ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- TIER 4: ABISSO -->
  <div class="iceberg-tier tier-abisso">
    <div class="tier-title-row">
      <div style="display: flex; align-items: center; gap: 0.65rem;">
        <span class="tier-depth-badge">-100 METRI</span>
        <strong style="font-size: 1.2rem; color: #fff; letter-spacing: -0.01em;">Abisso (Nessuna Luce / &lt;50 Ascoltatori)</strong>
      </div>
      <div style="font-family: var(--font-mono); font-size: 11.5px; color: var(--text-dim);">
        Account SoundCloud fantasma • Ego smisurato • Purezza nichilista
      </div>
    </div>
    <div class="artist-tag-grid">
      <?php foreach ($perLivello['abisso'] as $a): ?>
        <?php $av = $a->avatar(); ?>
        <a href="artista.php?id=<?= $a->id ?>" class="artist-tag-pill">
          <?php if ($av): ?>
            <img src="<?= h($av) ?>" alt="<?= h($a->nome) ?>" class="mini-avatar">
          <?php else: ?>
            <span class="mini-avatar-letter"><?= strtoupper(substr($a->nome, 0, 1)) ?></span>
          <?php endif; ?>
          <span><?= h($a->nome) ?></span>
          <span style="font-family: var(--font-mono); font-size: 11px; color: var(--text-dim);"><?= $a->monthly_listeners ? $a->monthly_listeners . ' ascolti' : 'Mistero' ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- INFOGRAFICHE E MEME ORIGINALI -->
<div style="margin-top: 3.5rem;">
  <div style="margin-bottom: 1.25rem;">
    <h2 style="font-family: var(--font-display); font-size: 1.6rem; color: #fff; letter-spacing: -0.02em;">
      Documenti Visivi dell'Iceberg
    </h2>
    <p style="color: var(--text-secondary); font-size: 14.5px;">
      Le infografiche originali della scena underground italiana e internazionale tramandate nei canali Telegram e Reddit.
    </p>
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.75rem;">
    <div class="side-card">
      <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 0.75rem; color: var(--accent-lime); font-family: var(--font-mono);">
        L'Iceberg della Scena Italiana
      </h3>
      <div style="border-radius: 10px; overflow: hidden; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        <img src="assets/italian_iceberg.png" alt="Iceberg Trap Italiana" style="width: 100%; display: block; filter: contrast(1.05);">
      </div>
      <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 0.85rem; line-height: 1.45;">
        Da Glocky, Drefgold, 18K e Aira fino a Tom Orrow e Nikkicarrera nelle profondità marine.
      </p>
    </div>

    <div class="side-card">
      <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 0.75rem; color: var(--accent-cyan); font-family: var(--font-mono);">
        Gerarchia dei Livelli Rap
      </h3>
      <div style="border-radius: 10px; overflow: hidden; border: 1px solid var(--border); box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
        <img src="assets/rap_tiers.jpg" alt="Rap Iceberg Tiers" style="width: 100%; display: block;">
      </div>
      <p style="font-size: 12.5px; color: var(--text-muted); margin-top: 0.85rem; line-height: 1.45;">
        Da Mainstream (Ye, Carti) fino al Bottom of the Underground (Frankslastday, Brennan Jones).
      </p>
    </div>
  </div>
</div>

<?php include 'foot.php'; ?>
