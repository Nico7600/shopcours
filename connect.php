<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=crud;charset=utf8', 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "Erreur de connexion à la base de données.";
    header("Location: register.php");
    exit();
}
?>