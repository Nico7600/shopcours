<?php
require_once('bootstrap.php');


if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour envoyer un message.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $userId = $_SESSION['id'];
    $message = $_POST['message'];

    $sql = 'INSERT INTO messages (user_id, message, created_at) VALUES (:user_id, :message, NOW())';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':message', $message, PDO::PARAM_STR);
    $query->execute();

    echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide.']);
}
?>
