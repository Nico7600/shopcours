<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$cartId = $_GET['cart_id'];
removeFromCart($cartId);
header('Location: cart.php');

function removeFromCart($cartId) {
    global $db;

    $sql = 'DELETE FROM cart WHERE id = :cart_id';
    $query = $db->prepare($sql);
    $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $query->execute();
}

exit();
?>
