<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Artista.php';

$currUser = utente_corrente();
$titoloPagina = isset($titolo) ? $titolo . ' — UNDERGROUND' : 'UNDERGROUND — The Internet\'s Worst Kept Secret';
$layydGoat = ArtistaRepo::trovaGoat();
?>
<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($titoloPagina) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=JetBrains+Mono:wght@400;500;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="ticker-wrap">
  <div class="ticker-move">
    <span class="ticker-item">👑 LAYYD JUST HIT 113 PLAYS ON NONSOCOSASIA! THE INDUSTRY IS FINISHED</span>
    <span class="ticker-item">🔥 WHY THE FUCK DID WHY TREVXR DROP THIS HARD</span>
    <span class="ticker-item">🎧 26 MONTHLY LISTENERS IS OBJECTIVELY BETTER THAN 90% OF THE CHARTS</span>
    <span class="ticker-item">🎛️ PRESET MERCHANTS CATCHING STRAYS IN FL STUDIO</span>
    <span class="ticker-item">🧊 ICEBERG DEPTH: -100M REACHED IN THE ABYSS</span>
    <span class="ticker-item">👑 113 PLAYS. LAYYD. THE GOAT. END OF DISCUSSION.</span>
  </div>
</div>

<header class="nav-wrap">
  <nav class="nav">
    <div class="logo-group">
      <a class="logo" href="index.php">
        UNDERGROUND<span class="slash">/</span>ICEBERG
      </a>
      <span class="logo-tag">113 PLAYS ERA</span>
    </div>

    <div class="nav-links">
      <a href="index.php" class="<?= ($attiva ?? '') === 'feed' ? 'on' : '' ?>">Feed</a>
      <a href="iceberg.php" class="<?= ($attiva ?? '') === 'iceberg' ? 'on' : '' ?>">Iceberg</a>
      <a href="artisti.php" class="<?= ($attiva ?? '') === 'artisti' ? 'on' : '' ?>">Artisti</a>
      <a href="community.php" class="<?= ($attiva ?? '') === 'community' ? 'on' : '' ?>">Community</a>
      <?php if ($layydGoat): ?>
        <a href="artista.php?id=<?= $layydGoat->id ?>" class="goat-nav <?= ($attiva ?? '') === 'layyd' ? 'on' : '' ?>">
          👑 LAYYD (<?= $layydGoat->plays ?>)
        </a>
      <?php endif; ?>
      <a href="nuovo_post.php" class="btn sm lime" style="margin-left: 0.5rem;">+ Post</a>
    </div>

    <div class="nav-user">
      <?php if ($currUser): ?>
        <a href="profilo.php" class="user-pill">
          <span class="user-dot" style="background: <?= h($currUser['avatar_color']) ?>"></span>
          <span>@<?= h($currUser['username']) ?></span>
          <span class="badge-tag"><?= h($currUser['badge']) ?></span>
        </a>
        <a href="logout.php" class="btn sm sec">Esci</a>
      <?php else: ?>
        <a href="login.php" class="btn sm sec">Accedi</a>
        <a href="registrati.php" class="btn sm lime">Registrati</a>
      <?php endif; ?>
    </div>
  </nav>
</header>

<main>
