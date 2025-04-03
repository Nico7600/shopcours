<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
    foreach ($_POST['quantities'] as $cartId => $quantity) {
        $quantity = (int)$quantity;

        $sql = '
            SELECT c.product_id, l.nombre AS stock_disponible 
            FROM cart c 
            JOIN liste l ON c.product_id = l.id 
            WHERE c.id = :cart_id AND c.user_id = :user_id
        ';
        $query = $db->prepare($sql);
        $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        $cartItem = $query->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem) {
            continue;
        }

        $stockDisponible = (int)$cartItem['stock_disponible'];

        if ($quantity > $stockDisponible) {
            $_SESSION['message'] = "La quantité demandée pour un produit dépasse le stock disponible ({$stockDisponible} unités).";
            header('Location: cart.php');
            exit();
        }

        if ($quantity > 0) {
            $sql = 'UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id';
            $query = $db->prepare($sql);
            $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
            $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $query->execute();
        } else {
            $sql = 'DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id';
            $query = $db->prepare($sql);
            $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
            $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $query->execute();
        }
    }
}

header('Location: cart.php');
exit();
?>
