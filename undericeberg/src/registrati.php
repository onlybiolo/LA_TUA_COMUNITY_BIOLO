<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (utente_corrente()) {
    header('Location: index.php');
    exit;
}

$errore = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $badge = trim($_POST['badge'] ?? 'SoundCloud Lurker');

    $res = registra_utente($username, $email, $password, $badge);
    if ($res === null) {
        header('Location: index.php');
        exit;
    } else {
        $errore = $res;
    }
}

$titolo = 'Registrati';
include 'head.php';
?>

<div class="form-card">
  <div style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-lime); margin-bottom: 0.5rem; text-transform: uppercase;">
    CREA IDENTITÀ • UNDERICEBERG
  </div>
  <h1 class="form-title">Entra nel Sottosuolo</h1>
  <p class="form-sub">
    Crea il tuo profilo, scegli il tuo badge di appartenenza e inizia a upvotare o a gatekeepare.
  </p>

  <?php if (!empty($errore)): ?>
    <div class="alert-err"><?= h($errore) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label for="username">Username *</label>
      <input type="text" id="username" name="username" required placeholder="es. spinz_808_enjoyer" minlength="3" maxlength="40" value="<?= h($_POST['username'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" required placeholder="tuamail@sottosuolo.it" value="<?= h($_POST['email'] ?? '') ?>">
      <span class="hint">Nessuna newsletter corporate, promesso.</span>
    </div>

    <div class="form-group">
      <label for="password">Password (minimo 6 caratteri) *</label>
      <input type="password" id="password" name="password" required minlength="6" placeholder="••••••••">
    </div>

    <div class="form-group">
      <label for="badge">Badge di Appartenenza</label>
      <select id="badge" name="badge">
        <option value="113 Believer">👑 113 Believer (Fedele all'Impero di Layyd)</option>
        <option value="OG Listener">🎧 OG Listener (Presente prima del drop)</option>
        <option value="FL Studio Warrior">🎛️ FL Studio Warrior (Preset distorti a +14dB)</option>
        <option value="Gatekeeper Emeritus">🔒 Gatekeeper Emeritus (Niente link a nessuno)</option>
        <option value="Unemployed Critic">📝 Unemployed Critic (Saggi da 3000 parole sui leak)</option>
        <option value="Rage Scientist">⚡ Rage Scientist (Analizzatore di synth impazziti)</option>
        <option value="SoundCloud Lurker" selected>🌙 SoundCloud Lurker (Insonnia e ricerca brani)</option>
      </select>
    </div>

    <button type="submit" class="btn lime" style="width: 100%; justify-content: center; margin-top: 1rem;">Registra Profilo</button>
  </form>

  <div style="text-align: center; margin-top: 2rem; font-size: 13.5px; color: var(--text-muted);">
    Hai già un profilo? <a href="login.php" style="color: #fff; text-decoration: underline;">Accedi qui</a>.
  </div>
</div>

<?php include 'foot.php'; ?>
