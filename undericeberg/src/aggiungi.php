<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Artista.php';

$a = new Artista();
$errore = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $a = Artista::daPost($_POST);
    if ($a->nome === '') {
        $errore = 'Serve almeno il nome d\'arte dell\'artista.';
    } else {
        $newId = ArtistaRepo::inserisci($a);
        header('Location: artista.php?id=' . $newId);
        exit;
    }
}

$titolo = 'Segnala Artista';
$attiva = 'artisti';
$bottone = 'Salva Artista nell\'Archivio';
include 'head.php';
?>

<div style="margin-bottom: 2rem; text-align: center;">
  <div class="hero-tagline" style="justify-content: center;">
    <span>➕ REGISTRAZIONE PROFILO SOTTOSUOLO</span>
  </div>
  <h1 style="font-family: var(--font-display); font-size: 2.2rem; color: #fff; text-transform: uppercase;">
    Aggiungi Artista all'Archivio
  </h1>
  <p style="color: var(--text-muted); font-size: 14px; max-width: 500px; margin: 0 auto;">
    Hai scoperto un nuovo fenomeno su SoundCloud con 40 ascoltatori? Censiscilo prima che diventi famoso.
  </p>
</div>

<?php include 'form.php'; ?>

<div style="text-align: center; margin-top: 2rem;">
  <a class="btn sm sec" href="gestisci.php">Gestisci Archivio Esistente (Modifica / Elimina)</a>
</div>

<?php include 'foot.php'; ?>
