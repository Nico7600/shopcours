<?php
// Vérifiez si une session n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifiez si le site est en maintenance
if (file_exists('maintenance.flag')) {
    header('Location: maintenance.php');
    exit();
}

// Gérer les messages flash de session
$message = null;
$messageType = 'success'; 
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

// Vérifiez si l'utilisateur est connecté
$userName = null;
$isPrime = false;
if (isset($_SESSION['id'])) {
    require_once 'connect.php'; // Assurez-vous que la connexion à la base est disponible

    // Récupérer les infos utilisateur
    $sql = 'SELECT fname, is_prime FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userName = $user['fname'];
        $isPrime = (bool)$user['is_prime'];
    }

    // Vérifiez si l'utilisateur est banni
    $banSql = 'SELECT id FROM bans WHERE user_id = :id';
    $banQuery = $db->prepare($banSql);
    $banQuery->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $banQuery->execute();
    $banRecord = $banQuery->fetch(PDO::FETCH_ASSOC);

    if ($banRecord) {
        header('Location: ban.php');
        exit();
    }
}
?>