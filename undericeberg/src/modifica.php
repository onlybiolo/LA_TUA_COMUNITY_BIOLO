<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';

$id = (int)($_GET['id'] ?? 0);
$a = ArtistaRepo::trova($id);
if (!$a) {
    header('Location: gestisci.php');
    exit;
}

$errore = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = Artista::daPost($_POST, $id);
    if ($a->nome === '') {
        $errore = 'Serve almeno il nome d\'arte.';
    } else {
        ArtistaRepo::aggiorna($a);
        header('Location: artista.php?id=' . $id);
        exit;
    }
}

$titolo = 'Modifica ' . $a->nome;
$attiva = 'artisti';
$bottone = 'Salva Modifiche';
include 'head.php';
?>

<div style="margin-bottom: 2rem; text-align: center;">
  <div class="hero-tagline" style="justify-content: center;">
    <span>✏️ AGGIORNAMENTO SCHEDA</span>
  </div>
  <h1 style="font-family: var(--font-display); font-size: 2.2rem; color: #fff; text-transform: uppercase;">
    Modifica: <?= h($a->nome) ?>
  </h1>
  <p style="color: var(--text-muted); font-size: 14px;">
    Aggiorna lo stato, i numeri di streaming o le note d'archivio.
  </p>
</div>

<?php include 'form.php'; ?>

<?php include 'foot.php'; ?>
