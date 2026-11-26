<?php
    //connessioni al sb
    $host = 'db';
    $db = 'comunity';
    $user = 'user';
    $pass = 'password';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $nome_arte = trim($_POST['nome_arte'] ?? '');
        $nome = trim($_POST['nome'] ?? '') ?: null;
        $cognome = trim($_POST['cognome'] ?? '') ?: null;
        $anno_nascita = !empty($_POST['anno_nascita']) ? $_POST['anno_nascita'] : null;
        $anno_debutto = !empty($_POST['anno_debutto']) ? $_POST['anno_debutto'] : null;
        $provenienza = trim($_POST['provenienza'] ?? '') ?: null;
        $bio = trim($_POST['bio'] ?? '') ?: null;
        $link_foto = trim($_POST['link_foto'] ?? '') ?: null;

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            $sql = "UPDATE artista SET nome_arte = :nome_arte, nome = :nome, cognome = :cognome, anno_nascita = :anno_nascita, anno_debutto = :anno_debutto, provenienza = :provenienza, bio = :bio, link_foto = :link_foto WHERE id = :id";
            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':id' => $id,
                ':nome_arte' => $nome_arte,
                ':nome' => $nome,
                ':cognome' => $cognome,
                ':anno_nascita' => $anno_nascita,
                ':anno_debutto' => $anno_debutto,
                ':provenienza' => $provenienza,
                ':bio' => $bio,
                ':link_foto' => $link_foto
            ]);

            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            echo "Errore di connessione: " . $e->getMessage();
            exit;
        }
    } else if (isset($_GET['id'])) {
        // Se il metodo non è POST, recupera i dati dell'artista per il modulo
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $sql = "SELECT * FROM artista WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $_GET['id']]);
            $artista = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$artista) {
                echo "Artista non trovato.";
                exit;
            }
        } catch (Exception $e) {
            echo "Errore di connessione: " . $e->getMessage();
            exit;
        }
    } else {
        echo "ID artista non specificato.";
        exit;
    }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica artista</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="topbar">
            <div>
                <h1>Modifica artista</h1>
                <h2>Aggiorna i dati nel database</h2>
            </div>
            <button type="button" onclick="window.location.href='index.php'">Torna alla lista</button>
        </header>

        <form class="form-box" action="modifica_artista.php" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($artista['id'] ?? ''); ?>">

            <label for="nome_arte">Nome d'arte:</label>
            <input type="text" id="nome_arte" name="nome_arte" value="<?php echo htmlspecialchars($artista['nome_arte'] ?? ''); ?>" required>

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($artista['nome'] ?? ''); ?>">

            <label for="cognome">Cognome:</label>
            <input type="text" id="cognome" name="cognome" value="<?php echo htmlspecialchars($artista['cognome'] ?? ''); ?>">

            <label for="anno_nascita">Anno di nascita:</label>
            <input type="number" id="anno_nascita" name="anno_nascita" value="<?php echo htmlspecialchars($artista['anno_nascita'] ?? ''); ?>">

            <label for="anno_debutto">Anno di debutto:</label>
            <input type="number" id="anno_debutto" name="anno_debutto" value="<?php echo htmlspecialchars($artista['anno_debutto'] ?? ''); ?>">

            <label for="provenienza">Provenienza:</label>
            <input type="text" id="provenienza" name="provenienza" value="<?php echo htmlspecialchars($artista['provenienza'] ?? ''); ?>">

            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($artista['bio'] ?? ''); ?></textarea>

            <label for="link_foto">Link foto:</label>
            <input type="url" id="link_foto" name="link_foto" value="<?php echo htmlspecialchars($artista['link_foto'] ?? ''); ?>">

            <div class="form-actions">
                <button type="submit">Salva modifiche</button>
            </div>
        </form>
    </div>
</body>
</html>