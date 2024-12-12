
<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = (int) $_SESSION['id'];
$cart_id = (int) $_POST['cart_id'];

$sql = 'DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id';
$query = $db->prepare($sql);
$query->execute([':cart_id' => $cart_id, ':user_id' => $user_id]);

echo json_encode(['status' => 'success']);
?>