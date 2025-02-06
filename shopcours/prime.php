<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];
\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$primeOptions = [
    '30_days' => ['duration' => 30, 'price' => 9.99],
    '365_days' => ['duration' => 365, 'price' => 99.99]
];

$error = '';

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $error = "Erreur de connexion à la base de données: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prime_option'])) {
    $option = $_POST['prime_option'];
    if (isset($primeOptions[$option])) {
        $price = $primeOptions[$option]['price'];
        $description = $option === '30_days' ? "Adhésion Prime 1 mois" : "Adhésion Prime 1 an";

        // Add the selected prime option to the cart
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $_SESSION['cart'][] = [
            'product' => $option,
            'price' => $price
        ];

        // Create a Stripe payment session
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $description,
                        'description' => "Profitez des avantages Prime pour $description.",
                    ],
                    'unit_amount' => $price * 100, 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/prime_success.php?session_id={CHECKOUT_SESSION_ID}&option=' . $option,
            'cancel_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/index.php?message=prime_cancel',
        ]);

        // Set the Prime subscription expiration date
        $duration = $primeOptions[$option]['duration'];
        $expirationDate = date('Y-m-d H:i:s', strtotime("+$duration days"));
        $stmt = $pdo->prepare("INSERT INTO crud (user_id, expiration_date) VALUES (?, ?) ON DUPLICATE KEY UPDATE expiration_date = VALUES(expiration_date)");
        $stmt->execute([$userId, $expirationDate]);

        // Redirect to the Stripe payment page
        header('Location: ' . $session->url);
        exit();
    } else {
        $error = 'Requête invalide.';
    }
}

$result = []; 

$user = []; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhésion Prime</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
            color: #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 50px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .badge-bottom-right {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        .fixed-height {
            height: 200px;
            object-fit: cover;
        }
        .star-rating i {
            color: #ffc107;
        }
        .card-price-original {
            text-decoration: line-through;
            color: #dc3545;
        }
        .card-price-promo {
            color: #28a745;
            font-weight: bold;
        }
        .card-quantity {
            font-size: 0.9rem;
        }
        .out-of-stock {
            color: #dc3545;
        }
        .low-quantity {
            color: #ffc107;
        }
        .medium-quantity {
            color: #17a2b8;
        }
        .high-quantity {
            color: #28a745;
        }
        .very-high-quantity {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Adhésion Prime</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="prime.php">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">1 mois de Prime</h5>
                            <p class="card-text">Obtenez l'adhésion Prime 1 mois pour seulement 9.99 euros.</p>
                            <button type="submit" name="prime_option" value="30_days" class="btn btn-primary btn-block">Acheter pour 9.99€</button>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">1 an de Prime</h5>
                            <p class="card-text">Obtenez l'adhésion Prime 1 an a seulement 99.99 euros.</p>
                            <button type="submit" name="prime_option" value="365_days" class="btn btn-primary btn-block">Acheter pour 99.99€</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <?php
            foreach ($result as $produit) {
                $image_path = 'image_produit/' . $produit['image_produit'];
                $default_image = 'image_produit/test.png';
                
                if (!file_exists($image_path) || empty($produit['image_produit'])) {
                    $image_path = $default_image; 
                }

                $quantities = [
                    0 => ['class' => 'out-of-stock', 'text' => '<i class="fas fa-exclamation-triangle"></i> Victime de son succès'],
                    20 => ['class' => 'low-quantity', 'text' => 'Quantité restante: '],
                    50 => ['class' => 'medium-quantity', 'text' => 'Quantité restante: '],
                    100 => ['class' => 'high-quantity', 'text' => 'Quantité restante: '],
                    PHP_INT_MAX => ['class' => 'very-high-quantity', 'text' => 'Quantité restante: '],
                ];

                foreach ($quantities as $limit => $data) {
                    if ($produit['nombre'] <= $limit) {
                        $quantityClass = $data['class'];
                        $quantityText = $data['text'] . $produit['nombre'];
                        break;
                    }
                }

                $badges = [
                    'danger' => 'badge-danger',
                    'warning' => 'badge-warning',
                    'success' => 'badge-success',
                    'info' => 'badge-info',
                    'dark' => 'badge-dark',
                    'secondary' => 'badge-secondary',
                    'purple' => 'badge-purple',
                    'yellow' => 'badge-yellow',
                    'peach' => 'badge-peach',
                    'fire' => 'badge-fire',
                    'pink' => 'badge-pink',
                    'light-red' => 'badge-light-red',
                    'dark-green' => 'badge-dark-green',
                    'sea-water' => 'badge-sea-water',
                    'gold' => 'badge-gold',
                    'cyan' => 'badge-cyan',
                    'brown' => 'badge-brown',
                    'silver' => 'badge-silver',
                    'black' => 'badge-black',
                    'nico' => 'badge-nico',
                ];

                $badgeClass = $badges[$produit['badge']] ?? 'badge-primary';

                $description = implode(' ', array_slice(explode(' ', $produit['Description']), 0, 30)) . '...';
                $prix = is_numeric(str_replace(',', '.', $produit['prix'])) ? (float)str_replace(',', '.', $produit['prix']) : 0;
                $promo = is_numeric($produit['Promo']) ? (float)$produit['Promo'] : 0;

                // Appliquer la réduction Prime pour les produits Amazon
                $isAmazon = strtolower($produit['production_company']) === 'amazon';
                $primeDiscount = ($isPrime && $isAmazon) ? 10 : 0;

                // Calculer le prix après réduction
                $totalDiscount = min($promo + $primeDiscount, 100); // Limiter à 100 %
                $finalPrice = $prix * (1 - $totalDiscount / 100);

                $averageRating = $ratings[$produit['id']] ?? 0;
                $fullStars = floor($averageRating);
                $halfStar = ($averageRating - $fullStars >= 0.5) ? 1 : 0;
                $quarterStar = ($averageRating - $fullStars >= 0.25 && $averageRating - $fullStars < 0.5) ? 1 : 0;
                $emptyStars = 5 - $fullStars - $halfStar - $quarterStar;
                $stars = str_repeat('<i class="fas fa-star"></i>', $fullStars);
                $stars .= str_repeat('<i class="fas fa-star-half-alt"></i>', $halfStar);
                $stars .= str_repeat('<i class="fas fa-star-quarter"></i>', $quarterStar);
                $stars .= str_repeat('<i class="far fa-star"></i>', $emptyStars);
            ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card" onclick="window.location.href='details.php?id=<?= htmlspecialchars($produit['id']); ?>'">
                        <div class="position-relative">
                        <img src="<?= htmlspecialchars($image_path); ?>" class="card-img-top fixed-height img-fluid" alt="<?= htmlspecialchars($produit['produit']); ?>">
                        <span class="badge <?= htmlspecialchars($badgeClass); ?> badge-bottom-right"><?= htmlspecialchars($produit['badge']); ?></span>
                        </div>
                        <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produit['produit']); ?></h5>
                        <p class="card-text"><?= htmlspecialchars($description); ?></p>
                            <p class="card-text"><strong>Produit par :</strong> <?= htmlspecialchars($produit['production_company'] ?? 'Inconnu'); ?></p>
                            <p class="card-text star-rating"><strong>Note moyenne :</strong> <?= $stars; ?> (<?= number_format($averageRating, 1); ?>)</p>

                            <!-- Affichage des prix avec réduction -->
                            <?php if ($promo > 0 || $primeDiscount > 0): ?>
                                <p class="card-price">
                                    <span class="card-price-original"><?= number_format($prix, 2, ',', ' '); ?> €</span>
                                    <span class="card-price-promo"><?= number_format($finalPrice, 2, ',', ' '); ?> € (-<?= $totalDiscount; ?>%)</span>
                                </p>
                            <?php else: ?>
                                <p class="card-price"><?= number_format($prix, 2, ',', ' '); ?> €</p>
                            <?php endif; ?>

                            <!-- Affichage de la quantité -->
                            <p class="card-quantity <?= htmlspecialchars($quantityClass); ?>">
                                <?= htmlspecialchars($produit['nombre'] > 0 ? 'Quantité : ' . $produit['nombre'] : 'En rupture de stock'); ?>
                            </p>

                            <div class="text-center">
                                <?php if ($produit['nombre'] > 0): ?>
                                    <a href="add_to_cart.php?product_id=<?= htmlspecialchars($produit['id']); ?>&quantity=1" class="btn btn-primary w-100">Ajouter au panier</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>Plus de stock pour le moment</button>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>