<?php
require_once __DIR__ . '/config.php';

function utente_corrente(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['utente_id'])) return null;
    
    static $utente = null;
    if ($utente !== null) return $utente;

    $st = db()->prepare("SELECT id, username, email, bio, badge, avatar_color, ruolo, creato_il FROM utenti WHERE id = ?");
    $st->execute([$_SESSION['utente_id']]);
    $utente = $st->fetch();
    if (!$utente) {
        unset($_SESSION['utente_id']);
        return null;
    }
    return $utente;
}

function richiedi_login(): array {
    $u = utente_corrente();
    if (!$u) {
        $_SESSION['flash_err'] = 'Devi accedere per compiere questa azione.';
        header('Location: login.php');
        exit;
    }
    return $u;
}

function login_utente(string $username, string $password): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $st = db()->prepare("SELECT id, password_hash FROM utenti WHERE username = ? OR email = ?");
    $st->execute([$username, $username]);
    $u = $st->fetch();
    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['utente_id'] = (int)$u['id'];
        return true;
    }
    return false;
}

function login_rapido(string $username): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $st = db()->prepare("SELECT id FROM utenti WHERE username = ?");
    $st->execute([$username]);
    $u = $st->fetch();
    if ($u) {
        $_SESSION['utente_id'] = (int)$u['id'];
        return true;
    }
    return false;
}

function registra_utente(string $username, string $email, string $password, string $badge = 'SoundCloud Lurker'): ?string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $username = trim($username);
    $email = trim($email);

    if (strlen($username) < 3) return 'Lo username deve avere almeno 3 caratteri.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Inserisci un indirizzo email valido.';
    if (strlen($password) < 6) return 'La password deve avere almeno 6 caratteri.';

    $pdo = db();
    $check = $pdo->prepare("SELECT id FROM utenti WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    if ($check->fetch()) return 'Username o email già in uso nel sottosuolo.';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $colori = ['#a3e635', '#38bdf8', '#facc15', '#fb7185', '#c084fc', '#f97316'];
    $colore = $colori[array_rand($colori)];

    $st = $pdo->prepare("INSERT INTO utenti (username, email, password_hash, badge, avatar_color) VALUES (?, ?, ?, ?, ?)");
    $st->execute([$username, $email, $hash, $badge, $colore]);

    $_SESSION['utente_id'] = (int)$pdo->lastInsertId();
    return null;
}

function logout_utente(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    unset($_SESSION['utente_id']);
    session_destroy();
}
