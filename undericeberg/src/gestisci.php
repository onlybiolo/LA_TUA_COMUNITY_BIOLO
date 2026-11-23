<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina'])) {
    ArtistaRepo::elimina((int)$_POST['elimina']);
    header('Location: gestisci.php');
    exit;
}

$lista = ArtistaRepo::tutti();
$titolo = 'Gestisci Archivio';
$attiva = 'artisti';
include 'head.php';
?>

<div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
  <div>
    <div class="hero-tagline">
      <span>🛠️ PANNELLO DI CONTROLLO</span>
    </div>
    <h1 style="font-family: var(--font-display); font-size: 2.2rem; color: #fff; text-transform: uppercase;">
      Gestisci Archivio
    </h1>
    <p style="color: var(--text-muted); font-size: 14px;">
      <?= count($lista) ?> artisti registrati nel database. Modifica i parametri o rimuovi voci obsolete.
    </p>
  </div>
  <a href="aggiungi.php" class="btn lime">+ Nuovo Artista</a>
</div>

<table class="tracks-table">
  <thead>
    <tr>
      <th>Profondità</th>
      <th>Nome Artista</th>
      <th>Genere / Stile</th>
      <th>Ascolti</th>
      <th style="text-align: right;">Azioni</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($lista as $a): ?>
      <tr>
        <td style="font-family: var(--font-mono); font-size: 12px; color: var(--accent-cyan); white-space: nowrap;">
          <?= $a->profondita() ?> • <?= strtoupper($a->livello) ?>
        </td>
        <td>
          <a href="artista.php?id=<?= $a->id ?>" style="font-weight: 700; color: #fff;">
            <?= h($a->nome) ?>
          </a>
          <?php if ($a->is_goat): ?>
            <span class="badge-tag" style="background: var(--accent-gold); color: #000; font-weight: bold; margin-left: 0.35rem;">👑 GOAT</span>
          <?php endif; ?>
        </td>
        <td style="font-size: 13px; color: var(--text-muted);"><?= h($a->stile) ?: '—' ?></td>
        <td style="font-family: var(--font-mono); font-weight: bold; color: <?= $a->is_goat ? 'var(--accent-gold)' : '#fff' ?>;">
          <?= $a->plays ? format_plays($a->plays) : '—' ?>
        </td>
        <td style="text-align: right;">
          <div style="display: inline-flex; gap: 0.4rem;">
            <a class="btn sm sec" href="modifica.php?id=<?= $a->id ?>">Modifica</a>
            <form method="post" style="display: inline; margin: 0;" onsubmit="return confirm('Eliminare <?= h(addslashes($a->nome)) ?> dall\'archivio?')">
              <button class="btn sm danger" name="elimina" value="<?= $a->id ?>">Elimina</button>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php include 'foot.php'; ?>
