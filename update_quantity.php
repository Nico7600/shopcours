
<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$user_id = (int) $_SESSION['id'];
$cart_id = (int) $_POST['cart_id'];
$quantity = (int) $_POST['quantity'];

if ($quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
    exit();
}

$sql = 'UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id';
$query = $db->prepare($sql);
$query->execute([':quantity' => $quantity, ':cart_id' => $cart_id, ':user_id' => $user_id]);

echo json_encode(['status' => 'success']);
?>