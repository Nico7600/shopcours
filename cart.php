<?php
require_once 'connect.php';
session_start();
// Vérification de l'utilisateur connecté
if (!isset($_SESSION['id'])) {
    $_SESSION['erreur'] = "Vous devez être connecté pour ajouter des articles au panier.";
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int) $_SESSION['id']; // ID de l'utilisateur connecté
    $product_id = (int) $_POST['id_produit'];
    $quantity = (int) $_POST['quantite'];

    try {
        // Vérifier si le produit est déjà dans le panier
        $sql = 'SELECT quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id';
        $query = $db->prepare($sql);
        $query->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id,
        ]);

        $existingCartItem = $query->fetch(PDO::FETCH_ASSOC);

        if ($existingCartItem) {
            // Si le produit est déjà dans le panier, augmenter la quantité
            $newQuantity = $existingCartItem['quantity'] + $quantity;
            $sql = 'UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id';
            $query = $db->prepare($sql);
            $query->execute([
                ':quantity' => $newQuantity,
                ':user_id' => $user_id,
                ':product_id' => $product_id,
            ]);
        } else {
            // Sinon, insérer le produit dans le panier
            $sql = 'INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)';
            $query = $db->prepare($sql);
            $query->execute([
                ':user_id' => $user_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity,
            ]);
        }

        // Rediriger vers la vue du panier
        header('Location: cart_view.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['erreur'] = "Erreur lors de l'ajout au panier : " . $e->getMessage();
        header('Location: details.php?id=' . $product_id);
        exit();
    }
} else {
    $_SESSION['erreur'] = "Requête invalide.";
    header('Location: index.php');
    exit();
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userName = null; 

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Fetch user name if logged in
if (isset($_SESSION['id'])) {
    $sql = 'SELECT fname FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userName = $user['fname'];
    }
}

$userId = $_SESSION['id'];
$cartItems = getCart($userId);
$total = 0;

function getCart($userId) {
    global $db;

    $sql = '
        SELECT c.id AS cart_id, l.produit, l.prix, c.quantity, l.image_produit, l.Promo
        FROM cart c
        JOIN liste l ON c.product_id = l.id
        WHERE c.user_id = :user_id
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

?>