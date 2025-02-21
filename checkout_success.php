<?php
require_once 'bootstrap.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

if (empty($_ENV['STRIPE_API_KEY'])) {
    throw new Exception('La clé API Stripe n\'est pas configurée dans le fichier .env');
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$sessionId = $_GET['session_id'] ?? null;
if (!$sessionId) {
    $_SESSION['message'] = "Session de paiement introuvable.";
    header('Location: cart.php');
    exit();
}

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    error_log("Session ID: $sessionId, Payment Status: " . $session->payment_status);
    if ($session->payment_status !== 'paid') {
        throw new Exception("Le paiement n'a pas été validé.");
    }

    if (!$db) {
        throw new Exception("Connexion à la base de données échouée.");
    }

    $sql = '
        SELECT c.id AS cart_id, c.quantity, l.id AS product_id, l.produit, l.prix, l.Promo, l.nombre AS stock_disponible
        FROM cart c
        JOIN liste l ON c.product_id = l.id
        WHERE c.user_id = :user_id
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();
    $cartItems = $query->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;

    $db->beginTransaction();

    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $price = (float)str_replace(',', '.', $item['prix']) * (1 - $item['Promo'] / 100);
        $subtotal = $price * $quantity;
        $total += $subtotal;

        $stockDisponible = $item['stock_disponible'];

        if ($quantity > $stockDisponible) {
            throw new Exception("Le stock du produit '{$item['produit']}' est insuffisant.");
        }

        $sql = 'UPDATE liste SET nombre = nombre - :quantity WHERE id = :product_id';
        $query = $db->prepare($sql);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->execute();
    }

    $sql = 'INSERT INTO orders (user_id, total_amount, stripe_session_id) VALUES (:user_id, :total_amount, :stripe_session_id)';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':total_amount', $total, PDO::PARAM_STR);
    $query->bindValue(':stripe_session_id', $sessionId, PDO::PARAM_STR);
    $query->execute();
    $orderId = $db->lastInsertId();

    foreach ($cartItems as $item) {
        $productId = $item['product_id'];
        $quantity = $item['quantity'];
        $price = (float)str_replace(',', '.', $item['prix']) * (1 - $item['Promo'] / 100);

        $sql = 'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)';
        $query = $db->prepare($sql);
        $query->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindValue(':price', $price, PDO::PARAM_STR);
        $query->execute();
    }

    $sql = 'DELETE FROM cart WHERE user_id = :user_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    $db->commit();

    $_SESSION['message'] = "Votre commande a été validée avec succès.";
    header('Location: cart.php');
    exit();
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    error_log("Erreur de validation de la commande : " . $e->getMessage());

    $_SESSION['message'] = "Erreur lors de la validation de la commande : " . htmlspecialchars($e->getMessage());
    header('Location: cart.php');
    exit();
}
