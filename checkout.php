<?php
require_once 'bootstrap.php';

use Stripe\Stripe;

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$userId = (int)$_SESSION['id'];

function getCartItems($userId, $db)
{
    try {
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
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du panier : " . $e->getMessage());
        return [];
    }
}

function isPrimeUser($userId, $db)
{
    try {
        $sql = 'SELECT is_prime FROM users WHERE id = :user_id';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return !empty($result['is_prime']) && $result['is_prime'] == 1;
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du statut Prime : " . $e->getMessage());
        return false;
    }
}

$isPrime = isPrimeUser($userId, $db);

$cartItems = getCartItems($userId, $db);

$lineItems = [];
foreach ($cartItems as $item) {
    $price = (float)str_replace(',', '.', $item['prix']);

    $promoDiscount = $item['Promo'] ?? 0;
    $priceAfterPromo = $price * (1 - $promoDiscount / 100);

    if ($isPrime && strtolower(trim($item['production_company'])) === 'amazon') {
        $priceAfterPromo *= 0.9;
    }

    $priceAfterPromo = max($priceAfterPromo, 0);

    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $item['produit'],
            ],
            'unit_amount' => round($priceAfterPromo * 100),
        ],
        'quantity' => $item['quantity'],
    ];
}

try {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

    $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $baseUrl . '/checkout_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/cart.php',
    ]);

    header('Location: ' . $checkoutSession->url);
    exit();
} catch (Exception $e) {
    error_log("Erreur Stripe : " . $e->getMessage());
    $_SESSION['message'] = "Une erreur est survenue lors de la création de la session Stripe.";
    header('Location: cart.php');
    exit();
}
?>