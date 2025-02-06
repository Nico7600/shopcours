<?php
require_once('bootstrap.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID du panier depuis l'URL
$cartId = $_GET['cart_id'] ?? null;

if ($cartId) {
    // Appeler la fonction pour supprimer l'article du panier
    removeFromCart($cartId);
    header('Location: cart.php');
    exit();
} else {
    $_SESSION['error'] = 'Aucun article à supprimer.';
    header('Location: cart.php');
    exit();
}

// Fonction pour supprimer un article du panier
function removeFromCart($cartId) {
    global $db;

    $sql = 'DELETE FROM cart WHERE id = :cart_id';
    $query = $db->prepare($sql);
    $query->bindValue(':cart_id', $cartId, PDO::PARAM_INT);
    $query->execute();
}
?>