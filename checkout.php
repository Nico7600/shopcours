<?php
require_once 'bootstrap.php'; // Charge les sessions, les variables d’environnement et la connexion

use Stripe\Stripe;

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Configurer Stripe avec la clé API
Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$userId = (int)$_SESSION['id'];

// Fonction pour récupérer les produits du panier
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

// Fonction pour vérifier si l'utilisateur est Prime
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

// Vérifier si l'utilisateur est Prime
$isPrime = isPrimeUser($userId, $db);

// Récupérer les produits du panier
$cartItems = getCartItems($userId, $db);

// Préparer les articles pour Stripe Checkout
$lineItems = [];
foreach ($cartItems as $item) {
    $price = (float)str_replace(',', '.', $item['prix']);

    // Appliquer la promotion
    $promoDiscount = $item['Promo'] ?? 0;
    $priceAfterPromo = $price * (1 - $promoDiscount / 100);

    // Réduction Prime pour les produits Amazon
    if ($isPrime && strtolower(trim($item['production_company'])) === 'amazon') {
        $priceAfterPromo *= 0.9; // Réduction supplémentaire de 10 %
    }

    // Vérifier le prix final
    $priceAfterPromo = max($priceAfterPromo, 0);

    // Ajouter l'article à la liste Stripe Checkout
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
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $baseUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

    $checkoutSession = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $baseUrl . '/checkout_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $baseUrl . '/cart.php',
    ]);

    // Rediriger l'utilisateur vers Stripe Checkout
    header('Location: ' . $checkoutSession->url);
    exit();
} catch (Exception $e) {
    error_log("Erreur Stripe : " . $e->getMessage());
    $_SESSION['message'] = "Une erreur est survenue lors de la création de la session Stripe.";
    header('Location: cart.php');
    exit();
}
?>