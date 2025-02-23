<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (file_exists('maintenance.flag')) {
    if (isset($_SESSION['id'])) {
        require_once 'connect.php';
        $sql = 'SELECT admin FROM users WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!($user && $user['admin'] == 1)) {
            header('Location: maintenance.php');
            exit();
        }
    } else {
        header('Location: maintenance.php');
        exit();
    }
}

$message = null;
$messageType = 'success'; 
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'] ?? 'success';
    unset($_SESSION['message'], $_SESSION['message_type']);
}

$userName = null;
$isPrime = false;
if (isset($_SESSION['id'])) {
    require_once 'connect.php';

    $sql = 'SELECT fname, is_prime FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userName = $user['fname'];
        $isPrime = (bool)$user['is_prime'];
    }

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