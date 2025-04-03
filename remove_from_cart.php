<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$cartId = $_GET['cart_id'] ?? null;

if ($cartId) {
    removeFromCart($cartId);
    header('Location: cart.php');
    exit();
} else {
    $_SESSION['error'] = 'Aucun article à supprimer.';
    header('Location: cart.php');
    exit();
}

function removeFromCart($cartId) {
    global $db;

    $sql = 'DELETE FROM cart WHERE id = :cart_id';
    $query = $db->prepare($sql);
    $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $query->execute();
}
?>