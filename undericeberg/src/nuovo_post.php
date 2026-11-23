<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Artista.php';
require_once __DIR__ . '/Post.php';

$currUser = utente_corrente();

$artistaIdPreselezionato = (int)($_GET['artista_id'] ?? 0);
$artisti = ArtistaRepo::tutti(null, null, 'nome');

$errore = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$currUser) {
        // Se non autenticato, per agevolare l'esperienza usiamo un utente demo automatico
        login_rapido('layyd_believer');
        $currUser = utente_corrente();
    }

    $titoloPost = trim($_POST['titolo'] ?? '');
    $contenuto = trim($_POST['contenuto'] ?? '');
    $flair = $_POST['flair'] ?? 'DISCUSSIONE';
    $artId = (int)($_POST['artista_id'] ?? 0) ?: null;
    $link = trim($_POST['link_esterno'] ?? '') ?: null;

    if (strlen($titoloPost) < 4) {
        $errore = 'Il titolo deve avere almeno 4 caratteri.';
    } elseif (strlen($contenuto) < 10) {
        $errore = 'Il contenuto deve essere di almeno 10 caratteri per giustificare la tua opinione.';
    } else {
        $newId = PostRepo::crea((int)$currUser['id'], $titoloPost, $contenuto, $flair, $artId, $link);
        header('Location: post.php?id=' . $newId);
        exit;
    }
}

$titolo = 'Crea Nuova Discussione';
$attiva = 'community';
include 'head.php';
?>

<div class="form-card">
  <div style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-lime); margin-bottom: 0.5rem; text-transform: uppercase;">
    COMMUNITY BOARD • NUOVO THREAD
  </div>
  <h1 class="form-title">Droppa una Discussione</h1>
  <p class="form-sub">
    Condividi uno snippet, un'opinione controversa o spiega perché una melodia registrata con un microfono da 10€ ha salvato l'underground.
  </p>

  <?php if (!empty($errore)): ?>
    <div class="alert-err"><?= h($errore) ?></div>
  <?php endif; ?>

  <?php if (!$currUser): ?>
    <div style="background: rgba(250,204,21,0.08); border: 1px solid rgba(250,204,21,0.3); border-radius: 8px; padding: 0.85rem; margin-bottom: 1.5rem; font-size: 13px; color: var(--accent-gold);">
      💡 Non sei autenticato: pubblicando il post accederai automaticamente con l'account demo <strong>@layyd_believer</strong>. 
      Oppure <a href="login.php" style="text-decoration: underline; color: #fff;">accedi con il tuo profilo</a>.
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label for="titolo">Titolo della discussione *</label>
      <input type="text" id="titolo" name="titolo" required placeholder="es. PERCHÉ NESSUNO PARLA DI QUESTO SNIPPET?" maxlength="255" value="<?= h($_POST['titolo'] ?? '') ?>">
      <span class="hint">Fallo sembrare serio oppure completamente fuori di testa.</span>
    </div>

    <div class="form-group">
      <label for="flair">Flair / Categoria *</label>
      <select id="flair" name="flair">
        <?php foreach (PostRepo::FLAIRS as $fKey => $fVal): ?>
          <option value="<?= $fKey ?>" <?= (($_POST['flair'] ?? '') === $fKey) ? 'selected' : '' ?>>
            <?= $fVal['icon'] ?> <?= $fKey ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="artista_id">Artista di Riferimento (opzionale)</label>
      <select id="artista_id" name="artista_id">
        <option value="">-- Nessun artista specifico / Scena Generale --</option>
        <?php foreach ($artisti as $art): ?>
          <option value="<?= $art->id ?>" <?= ($artistaIdPreselezionato === $art->id || ($_POST['artista_id'] ?? 0) == $art->id) ? 'selected' : '' ?>>
            <?= h($art->nome) ?> (<?= $art->profondita() ?>) <?= $art->is_goat ? '👑' : '' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="contenuto">Contenuto / Rant / Spiegazione *</label>
      <textarea id="contenuto" name="contenuto" rows="6" required placeholder="Argomenta perché questo pezzo ha cambiato la storia della musica..."><?= h($_POST['contenuto'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label for="link_esterno">Link esterno (SoundCloud / Snippet / YouTube opzionale)</label>
      <input type="url" id="link_esterno" name="link_esterno" placeholder="https://soundcloud.com/..." value="<?= h($_POST['link_esterno'] ?? '') ?>">
    </div>

    <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
      <a href="community.php" class="btn sec">Annulla</a>
      <button type="submit" class="btn lime">Pubblica nel Sottosuolo</button>
    </div>
  </form>
</div>

<?php include 'foot.php'; ?>
