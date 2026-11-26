<?php
    // Database connection parameters
    $host = 'db';
    $db = 'comunity';
    $user = 'user';
    $pass = 'password';

    // Check if the 'id' parameter is present in the URL
    if (isset($_GET['id']))
        $id = $_GET['id'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $sql = "DELETE FROM artista WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

?>