<?php
require_once('bootstrap.php');

$sql = 'SELECT m.message, u.username FROM messages m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC';
$query = $db->prepare($sql);
$query->execute();
$messages = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>