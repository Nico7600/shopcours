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
function getCart($userId)
{
    global $db;

    $sql = '
        SELECT c.id AS cart_id, c.quantity, l.id AS product_id, l.produit, l.prix, l.Promo, p.name AS production_company
        FROM cart c
        JOIN liste l ON c.product_id = l.id
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE c.user_id = :user_id
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour vérifier si l'utilisateur est Prime
function isPrimeUser($userId)
{
    global $db;

    $sql = 'SELECT is_prime FROM users WHERE id = :user_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    return !empty($result['is_prime']) && $result['is_prime'] == 1;
}

// Vérifier si l'utilisateur est Prime
$isPrime = isPrimeUser($userId);

// Récupérer les produits du panier
$cartItems = getCart($userId);

// Créer une ligne pour chaque article dans Stripe Checkout
$lineItems = [];
foreach ($cartItems as $item) {
    $price = str_replace(',', '.', $item['prix']); // Convertir en numérique
    $price = (float)$price;

    // Appliquer la promotion
    $promoDiscount = $item['Promo'] ?? 0;
    $priceAfterPromo = $price * (1 - $promoDiscount / 100);

    // Appliquer la réduction Prime pour les produits Amazon
    if ($isPrime && strtolower(trim($item['production_company'])) === 'amazon') {
        $priceAfterPromo *= 0.9; // Réduction supplémentaire de 10 %
    }

    // Ajouter la ligne à Stripe Checkout
    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $item['produit'],
            ],
            'unit_amount' => round($priceAfterPromo * 100), // Stripe attend un montant en centimes
        ],
        'quantity' => $item['quantity'],
    ];
}

// Créer une session Stripe Checkout
try {
    // Récupérer automatiquement l'URL de base
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

    $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $baseUrl . '/checkout_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/cart.php',
    ]);

    // Rediriger vers Stripe Checkout
    header('Location: ' . $checkoutSession->url);
    exit();
} catch (Exception $e) {
    $_SESSION['message'] = "Erreur lors de la création de la session Stripe : " . $e->getMessage();
    header('Location: cart.php');
    exit();
}
