<?php
require_once 'connect.php';
require_once 'vendor/autoload.php'; 

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$userId = $_SESSION['id'];

// Fonction pour récupérer le panier
function getCart($userId) {
    global $db;

    $sql = '
        SELECT c.id AS cart_id, c.quantity, l.id AS product_id, l.produit, l.prix, l.Promo
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

// Créer une ligne pour chaque article dans Stripe Checkout
$lineItems = [];
foreach ($cartItems as $item) {
    $price = str_replace(',', '.', $item['prix']); // Convertir en numérique
    $price = (float)$price * (1 - $item['Promo'] / 100); // Appliquer la promotion

    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $item['produit'],
            ],
            'unit_amount' => round($price * 100), // Stripe attend un montant en centimes
        ],
        'quantity' => $item['quantity'],
    ];
}

// Créer une session Stripe Checkout
try {
    $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => 'http://yourdomain.com/checkout_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://yourdomain.com/cart.php',
    ]);

    // Rediriger vers Stripe Checkout
    header('Location: ' . $checkoutSession->url);
    exit();
} catch (Exception $e) {
    $_SESSION['message'] = "Erreur lors de la création de la session Stripe : " . $e->getMessage();
    header('Location: cart.php');
    exit();
}
?>