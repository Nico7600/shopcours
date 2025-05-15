<?php
require_once 'vendor/autoload.php';
$pdo = new PDO($dsn, $username, $password, [...]);

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if ($userId) {
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 0 WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    echo json_encode(['success' => true, 'message' => 'Utilisateur débanni avec succès.']);
} else {
    echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant.']);
}
?>
