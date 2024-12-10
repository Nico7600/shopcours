<?php
require_once 'connect.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

// Fonction pour récupérer le panier
function getCart($userId) {
    global $db;

    $sql = '
        SELECT c.id AS cart_id, c.quantity, l.id AS product_id, l.produit, l.prix, l.nombre AS stock_disponible, l.Promo
        FROM cart c
        JOIN liste l ON c.product_id = l.id
        WHERE c.user_id = :user_id
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les produits du panier
$cartItems = getCart($userId);
$total = 0;

try {
    // Démarrer une transaction
    $db->beginTransaction();

    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $price = str_replace(',', '.', $item['prix']); // Convertir le prix en numérique
        $price = (float)$price * (1 - $item['Promo'] / 100); // Appliquer la promotion
        $subtotal = $price * $quantity;
        $total += $subtotal;

        $stockDisponible = $item['stock_disponible'];

        // Vérifier si le stock est suffisant
        if ($quantity > $stockDisponible) {
            throw new Exception("Le stock du produit '{$item['produit']}' est insuffisant.");
        }

        // Réduire le stock du produit
        $sql = 'UPDATE liste SET nombre = nombre - :quantity WHERE id = :product_id';
        $query = $db->prepare($sql);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->execute();
    }

    // Insérer la commande dans la table `orders`
    $sql = 'INSERT INTO orders (user_id, total_amount) VALUES (:user_id, :total_amount)';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':total_amount', $total, PDO::PARAM_STR);
    $query->execute();
    $orderId = $db->lastInsertId();

    // Insérer les produits dans la table `order_items`
    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $price = str_replace(',', '.', $item['prix']); // Prix unitaire
        $price = (float)$price * (1 - $item['Promo'] / 100); // Appliquer la promotion

        $sql = 'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)';
        $query = $db->prepare($sql);
        $query->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':price', $price, PDO::PARAM_STR);
        $query->execute();
    }

    // Vider le panier
    $sql = 'DELETE FROM cart WHERE user_id = :user_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    // Confirmer la transaction
    $db->commit();

    // Ajouter un message de succès
    $_SESSION['message'] = "Votre commande a été validée avec succès.";
    header('Location: cart.php');
    exit();

} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $db->rollBack();
    $_SESSION['message'] = "Erreur lors de la validation de la commande : " . $e->getMessage();
    header('Location: cart.php');
    exit();
}
