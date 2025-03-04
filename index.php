<?php
require_once 'bootstrap.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';

$tagCountsSql = '
    SELECT badge, COUNT(*) as count
    FROM liste
    GROUP BY badge
';
$tagCountsQuery = $db->prepare($tagCountsSql);
$tagCountsQuery->execute();
$tagCounts = $tagCountsQuery->fetchAll(PDO::FETCH_KEY_PAIR);

if ($search) {
    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE l.produit LIKE :search OR l.Description LIKE :search
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
} else if ($category) {
    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE l.badge = :category
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
} else if ($tag) {
    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE l.badge = :tag
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':tag', $tag, PDO::PARAM_STR);
} else {
    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
    ';
    $query = $db->prepare($sql);
}

$query->execute();

$result = $query->fetchAll(PDO::FETCH_ASSOC);

shuffle($result);

$ratingsSql = '
    SELECT product_id, AVG(rating) as average_rating
    FROM comments
    GROUP BY product_id
';
$ratingsQuery = $db->prepare($ratingsSql);
$ratingsQuery->execute();
$ratings = $ratingsQuery->fetchAll(PDO::FETCH_KEY_PAIR);

require_once('close.php');

$message = isset($_GET['message']) ? $_GET['message'] : '';
if ($message === 'T') {
    echo '<div class="alert alert-success">Votre adhésion Prime a été réussie.</div>';
} elseif ($message === 'prime_cancel') {
    echo '<div class="alert alert-danger">Votre adhésion Prime a été annulée.</div>';
}

$isPrime = false;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            background-color: #343a40;
            color: white;
        }

        .carousel-caption,
        .card-body,
        .alert,
        .cookie-consent-popup {
            color: white; /* Ensure text is visible on these elements */
        }

        .carousel-caption {
            background-color: rgba(255, 255, 255, 0.7); /* Light background for better contrast */
        }

        .card-body {
            background-color: #343a40; /* Changed to match the new background color */
        }

        .alert {
            background-color: #343a40; /* Changed to match the new background color */
        }

        .cookie-consent-popup {
            background-color: #343a40; /* Changed to match the new background color */
        }

        .carousel-item img {
            height: 50vh;
            object-fit: cover;
        }

        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-price-original {
            text-decoration: line-through;
            color: #ff5733;
        }

        .card-price-promo {
            color: #28a745;
            font-weight: bold;
        }

        .card-quantity {
            font-weight: bold;
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
            color: #007bff;
        }

        .badge-bottom-right {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            background-color: #ff5733;
            color: #ffffff;
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .star-rating i {
            color: #ff5733;
        }

        @media (max-width: 768px) {
            .carousel-item img {
                height: 30vh;
            }

            .card {
                margin-bottom: 1rem;
            }
        }

        .progress-bar {
            height: 10px;
            background-color: #007bff; /* Same blue as the "Acheter" button */
            transition: width 0.1s linear;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    
    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="alert alert-danger alert-custom" role="alert">
            <?= htmlspecialchars($_SESSION['erreur']) ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>
    <main class="container mt-5">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                $active = 'active';
                foreach ($result as $produit) {
                    $image_path = 'image_produit/' . $produit['image_produit'];
                    $default_image = 'image_produit/test.png';
                    
                    if (!file_exists($image_path) || empty($produit['image_produit'])) {
                        $image_path = $default_image;
                    }
                ?>
                    <div class="carousel-item <?= $active ?>">
                        <img src="<?= htmlspecialchars($image_path); ?>" class="d-block w-100 img-fluid" alt="<?= htmlspecialchars($produit['produit']); ?>" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
                        <div class="carousel-caption d-md-block">
                            <h5><?= $produit['produit'] ?></h5>
                            <p>A seulement : <?= $produit['prix'] ?> €</p>
                        </div>
                    </div>
                <?php
                    $active = '';
                }
                ?>
            </div>
        </div>
        

        <div class="row mt-4">
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

                $isAmazon = strtolower($produit['production_company']) === 'amazon';
                $primeDiscount = ($isPrime && $isAmazon) ? 10 : 0;

                $totalDiscount = min($promo + $primeDiscount, 100);
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
                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card" onclick="window.location.href='details.php?id=<?= htmlspecialchars($produit['id']); ?>'">
                        <div class="position-relative">
                        <img src="<?= htmlspecialchars($image_path); ?>" class="card-img-top fixed-height img-fluid" alt="<?= htmlspecialchars($produit['produit']); ?>">
                        <span class="badge <?= htmlspecialchars($badgeClass); ?> badge-bottom-right"><?= htmlspecialchars($produit['badge']); ?></span>
                        </div>
                        <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produit['produit']); ?></h5>
                        <p class="card-text"><?= htmlspecialchars($description); ?></p>
                            <p class="card-text"><strong>Produit par :</strong> <?= htmlspecialchars($produit['production_company'] ?? 'Inconnu'); ?></p>
                            <p class="card-text star-rating"><strong>Note moyenne :</strong> <?= $stars; ?> (<?= number_format((float)$averageRating, 1); ?>)</p>

                            <?php if ($promo > 0 || $primeDiscount > 0): ?>
                                <p class="card-price">
                                    <span class="card-price-original"><?= number_format($prix, 2, ',', ' '); ?> €</span>
                                    <span class="card-price-promo"><?= number_format($finalPrice, 2, ',', ' '); ?> € (-<?= $totalDiscount; ?>%)</span>
                                </p>
                            <?php else: ?>
                                <p class="card-price"><?= number_format($prix, 2, ',', ' '); ?> €</p>
                            <?php endif; ?>

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
    </main>

<?php include 'includes/modal.php'; ?>

<div id="cookieConsent" class="cookie-consent-popup">
    <p><i class="fas fa-cookie-bite"></i> Ce site utilise des cookies pour améliorer votre expérience. <a href="privacy_policy.php">En savoir plus</a></p>
    <button id="acceptCookies" class="btn btn-primary"><i class="fas fa-check"></i> Accepter</button>
    <button id="rejectCookies" class="btn btn-secondary"><i class="fas fa-times"></i> Refuser</button>
</div>

<script>
    setTimeout(function() {
        var notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }, 5000);

    function dismissNotification() {
        var notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }

    document.getElementById('acceptCookies').addEventListener('click', function() {
        document.cookie = "cookies_accepted=true; path=/; max-age=" + (60 * 60 * 24 * 365);
        document.getElementById('cookieConsent').style.display = 'none';
    });

    document.getElementById('rejectCookies').addEventListener('click', function() {
        document.cookie = "cookies_accepted=false; path=/; max-age=" + (60 * 60 * 24 * 365);
        document.getElementById('cookieConsent').style.display = 'none';
    });

    if (document.cookie.indexOf('cookies_accepted=true') === -1) {
        document.getElementById('cookieConsent').style.display = 'block';
    } else {
        document.getElementById('cookieConsent').style.display = 'none';
    }

    var carousel = document.getElementById('productCarousel');
    var interval = 10000; // 10 seconds

    function getRandomSlide() {
        var items = carousel.querySelectorAll('.carousel-item');
        var randomIndex = Math.floor(Math.random() * items.length);
        return randomIndex;
    }

    setInterval(function () {
        var randomIndex = getRandomSlide();
        $('#productCarousel').carousel(randomIndex);
    }, interval);
</script>

<style>
    .cookie-consent-popup {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: #fff;
        padding: 15px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: block;
        z-index: 1000;
    }
    .cookie-consent-popup p {
        margin: 0 0 10px;
    }
    .cookie-consent-popup button {
        margin-right: 10px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

<footer style="text-align: center;">
    <p>&copy; 2024-2025 Valomazone. Tous droits réservés.</p>
</footer>

</body>
</html>