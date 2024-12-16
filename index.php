<?php
if (file_exists('maintenance.flag')) {
    header('Location: maintenance.php');
    exit;
}
// On démarre une session
session_start();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'] ?? 'success'; // Ensure message type is set
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

require_once 'connect.php';

$userName = null;
$isPrime = false;
if (isset($_SESSION['id'])) {
    $sql = 'SELECT fname, is_prime FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userName = $user['fname'];
        $isPrime = (bool)$user['is_prime'];
    }
}

// Check for ban record
if (isset($_SESSION['id'])) {
    $banSql = 'SELECT id FROM bans WHERE user_id = :id';
    $banQuery = $db->prepare($banSql);
    $banQuery->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $banQuery->execute();
    $banRecord = $banQuery->fetch(PDO::FETCH_ASSOC);
    if ($banRecord) {
        header('Location: ban.php');
        exit;
    }
}

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

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
} else {
    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
    ';
    $query = $db->prepare($sql);
}

// On exécute la requête
$query->execute();

// On stocke le résultat dans un tableau associatif
$result = $query->fetchAll(PDO::FETCH_ASSOC);

// Mélanger les résultats pour un affichage aléatoire
shuffle($result);

// Fetch average ratings for each product
$ratingsSql = '
    SELECT product_id, AVG(rating) as average_rating
    FROM comments
    GROUP BY product_id
';
$ratingsQuery = $db->prepare($ratingsSql);
$ratingsQuery->execute();
$ratings = $ratingsQuery->fetchAll(PDO::FETCH_KEY_PAIR);

// Remove chat message handling

// Remove chat message handling

require_once('close.php');

$message = isset($_GET['message']) ? $_GET['message'] : '';
if ($message === 'T') {
    echo '<div class="alert alert-success">Votre adhésion Prime a été réussie.</div>';
} elseif ($message === 'prime_cancel') {
    echo '<div class="alert alert-danger">Votre adhésion Prime a été annulée.</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        #notification {
            top: 20px; /* Ajuster si nécessaire */
            right: 20px; /* Ajuster si nécessaire */
            position: fixed;
            z-index: 1050;
            width: 300px;
        }
        .fixed-height {
            height: 200px; /* Ajuster la hauteur selon vos besoins */
            object-fit: cover;
        }
        .star-rating .fa-star,
        .star-rating .fa-star-half-alt,
        .star-rating .fa-star-quarter {
            color: #FFD700; /* Yellow color for stars */
        }
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        h5 {
            font-size: 1.5rem;
            font-weight: 700;
        }
        p {
            font-size: 1rem;
            font-weight: 400;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .card-text {
            font-size: 1rem;
            font-weight: 300;
        }
        .card-price-original {
            font-size: 1rem;
            font-weight: 400;
            text-decoration: line-through;
        }
        .card-price-promo {
            font-size: 1.25rem;
            font-weight: 700;
            color: red;
        }
        .card-quantity {
            font-size: 0.875rem;
            font-weight: 400;
        }
        .btn {
            font-size: 1rem;
            font-weight: 700;
        }
        .alert-custom {
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
            width: 80%;
            text-align: center;
        }
        .chatbox {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            z-index: 1050;
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: none; /* Initially hidden */
        }
        .chatbox-header {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .chatbox-body {
            max-height: 200px;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        .chatbox-footer {
            display: flex;
            align-items: center;
        }
        .chatbox-footer textarea {
            flex: 1;
            resize: none;
        }
        .chatbox-footer button {
            margin-left: 10px;
        }
        .success-message {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .success-message.show {
            display: block;
            animation: fadeInOut 4s forwards;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    
    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="alert alert-danger alert-custom" role="alert">
            <?= htmlspecialchars($_SESSION['erreur']) ?>
        </div>
        <?php unset($_SESSION['erreur']); ?>
    <?php endif; ?>
    <!-- Contenu principal -->
    <main class="container mt-5">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
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
                        <img src="<?= $image_path ?>" class="d-block w-100 fixed-height img-fluid" alt="<?= $produit['produit'] ?>" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
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
            <a class="carousel-control-prev" href="#productCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#productCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
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
    </main>
<!-- Modal -->
<?php include 'includes/modal.php'; ?>

<script>
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            this.textContent = 'Light Mode';
        } else {
            this.textContent = 'Dark Mode';
        }
    });

    // Automatically hide the notification after 5 seconds
    setTimeout(function() {
        var notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }, 5000);

    // Dismiss notification on close button click
    function dismissNotification() {
        var notification = document.getElementById('notification');
        if (notification) {
            notification.style.display = 'none';
        }
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>