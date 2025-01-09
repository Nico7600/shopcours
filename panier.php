<?php
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produit = $_POST['id_produit'];
    $quantite = $_POST['quantite'];

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Add or update the product in the cart
    if (isset($_SESSION['panier'][$id_produit])) {
        $_SESSION['panier'][$id_produit] += $quantite;
    } else {
        $_SESSION['panier'][$id_produit] = $quantite;
    }

    // Redirect back to the product details page or cart page
    header('Location: panier_view.php');
    exit;
} else {
    // Redirect to index if accessed directly
    header('Location: index.php');
    exit;
}
?>