<?php
require_once 'bootstrap.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['message'] = 'Vous devez être connecté pour ajouter un produit au panier.';
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['id'];
$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

if ($productId <= 0 || $quantity <= 0) {
    $_SESSION['message'] = 'Données invalides pour le produit ou la quantité.';
    header('Location: index.php');
    exit();
}

try {
    $sql = 'SELECT id, nombre FROM liste WHERE id = :product_id AND actif = 1';
    $query = $db->prepare($sql);
    $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $query->execute();
    $product = $query->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $_SESSION['message'] = 'Produit introuvable ou inactif.';
        header('Location: index.php');
        exit();
    }

    if ($product['nombre'] < $quantity) {
        $_SESSION['message'] = 'Stock insuffisant pour ce produit.';
        header('Location: index.php');
        exit();
    }

    $sql = 'SELECT id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
    $query->execute();
    $cartItem = $query->fetch(PDO::FETCH_ASSOC);

    if ($cartItem) {
        $newQuantity = $cartItem['quantity'] + $quantity;
        if ($product['nombre'] < $newQuantity) {
            $_SESSION['message'] = 'Stock insuffisant pour la quantité totale demandée.';
            header('Location: index.php');
            exit();
        }

        $sql = 'UPDATE cart SET quantity = :quantity WHERE id = :cart_id';
        $query = $db->prepare($sql);
        $query->bindValue(':quantity', $newQuantity, PDO::PARAM_INT);
        $query->bindValue(':cart_id', $cartItem['id'], PDO::PARAM_INT);
        $query->execute();
    } else {
        $sql = 'INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->execute();
    }

    $_SESSION['message'] = 'Produit ajouté au panier avec succès.';
    header('Location: index.php');
    exit();

} catch (PDOException $e) {
    error_log("Erreur lors de l'ajout au panier : " . $e->getMessage());
    $_SESSION['message'] = 'Une erreur est survenue lors de l’ajout au panier.';
    header('Location: index.php');
    exit();
}