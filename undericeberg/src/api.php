<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Post.php';
require_once __DIR__ . '/Artista.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

function apiResponse(array $data, ?string $redirect = null) {
    global $isJson;
    if ($isJson || isset($_GET['json'])) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    if ($redirect) {
        header("Location: $redirect");
        exit;
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if ($action === 'play_layyd' || $action === 'stream_track') {
    $artistaId = (int)($_POST['artista_id'] ?? $_GET['artista_id'] ?? 0);
    if ($artistaId <= 0) {
        $layyd = ArtistaRepo::trovaGoat();
        $artistaId = $layyd ? $layyd->id : 0;
    }
    if ($artistaId > 0) {
        $nuoviPlays = ArtistaRepo::incrementaPlays($artistaId);
        apiResponse([
            'success' => true,
            'plays' => $nuoviPlays,
            'msg' => 'Stream registrato! L\'impero di Layyd cresce.'
        ]);
    }
    apiResponse(['success' => false, 'error' => 'Artista non trovato']);
}

if ($action === 'upvote_post') {
    $u = utente_corrente();
    if (!$u) {
        // Se non autenticato, per agevolare l'esperienza usiamo un utente demo anonimo
        // oppure chiediamo login
        if ($isJson) {
            http_response_code(401);
            echo json_encode(['error' => 'Devi effettuare il login per votare']);
            exit;
        }
        header('Location: login.php');
        exit;
    }
    $postId = (int)($_POST['post_id'] ?? 0);
    if ($postId > 0) {
        $res = PostRepo::toggleUpvote($postId, (int)$u['id']);
        apiResponse([
            'success' => true,
            'upvoted' => $res['upvoted'],
            'count' => $res['count']
        ]);
    }
    apiResponse(['success' => false, 'error' => 'ID post non valido']);
}

if ($action === 'upvote_comment') {
    $u = utente_corrente();
    if (!$u) {
        if ($isJson) {
            http_response_code(401);
            echo json_encode(['error' => 'Devi effettuare il login per votare']);
            exit;
        }
        header('Location: login.php');
        exit;
    }
    $commentId = (int)($_POST['comment_id'] ?? 0);
    if ($commentId > 0) {
        $res = PostRepo::toggleUpvoteCommento($commentId, (int)$u['id']);
        apiResponse([
            'success' => true,
            'upvoted' => $res['upvoted'],
            'count' => $res['count']
        ]);
    }
    apiResponse(['success' => false, 'error' => 'ID commento non valido']);
}

apiResponse(['error' => 'Azione non riconosciuta']);
