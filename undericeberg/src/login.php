<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

if (utente_corrente()) {
    header('Location: index.php');
    exit;
}

$errore = $_SESSION['flash_err'] ?? '';
unset($_SESSION['flash_err']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['demo_user'])) {
        if (login_rapido(trim($_POST['demo_user']))) {
            header('Location: index.php');
            exit;
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (login_utente($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $errore = 'Credenziali non valide per il sottosuolo.';
        }
    }
}

$titolo = 'Accedi';
include 'head.php';
?>

<div class="form-card">
  <div style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-cyan); margin-bottom: 0.5rem; text-transform: uppercase;">
    AUTENTICAZIONE • UNDERICEBERG
  </div>
  <h1 class="form-title">Accedi al Sottosuolo</h1>
  <p class="form-sub">
    Inserisci le tue credenziali o usa uno dei profili demo per testare subito l'esperienza.
  </p>

  <?php if (!empty($errore)): ?>
    <div class="alert-err"><?= h($errore) ?></div>
  <?php endif; ?>

  <form method="post" style="margin-bottom: 2rem;">
    <div class="form-group">
      <label for="username">Username o Email</label>
      <input type="text" id="username" name="username" required placeholder="es. layyd_believer" value="<?= h($_POST['username'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required placeholder="••••••••">
    </div>

    <button type="submit" class="btn lime" style="width: 100%; justify-content: center;">Entra nell'Archivio</button>
  </form>

  <div style="border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
    <div style="font-family: var(--font-mono); font-size: 11px; color: var(--accent-gold); text-transform: uppercase; margin-bottom: 0.75rem;">
      ⚡ ACCESSO RAPIDO DEMO (1 CLICK)
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
      <form method="post" style="margin: 0;">
        <input type="hidden" name="demo_user" value="layyd_believer">
        <button class="btn sec sm" style="width: 100%; text-align: left; justify-content: space-between;">
          <span>@layyd_believer</span>
          <span class="badge-tag">113 Believer</span>
        </button>
      </form>

      <form method="post" style="margin: 0;">
        <input type="hidden" name="demo_user" value="preset_merchant">
        <button class="btn sec sm" style="width: 100%; text-align: left; justify-content: space-between;">
          <span>@preset_merchant</span>
          <span class="badge-tag">FL Warrior</span>
        </button>
      </form>

      <form method="post" style="margin: 0;">
        <input type="hidden" name="demo_user" value="underground_curator">
        <button class="btn sec sm" style="width: 100%; text-align: left; justify-content: space-between;">
          <span>@underground_curator</span>
          <span class="badge-tag">Gatekeeper</span>
        </button>
      </form>

      <form method="post" style="margin: 0;">
        <input type="hidden" name="demo_user" value="whytrevxr_stan">
        <button class="btn sec sm" style="width: 100%; text-align: left; justify-content: space-between;">
          <span>@whytrevxr_stan</span>
          <span class="badge-tag">OG Listener</span>
        </button>
      </form>
    </div>
  </div>

  <div style="text-align: center; margin-top: 2rem; font-size: 13.5px; color: var(--text-muted);">
    Non hai ancora un account? <a href="registrati.php" style="color: #fff; text-decoration: underline;">Registrati qui</a>.
  </div>
</div>

<?php include 'foot.php'; ?>
