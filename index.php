<?php
// On démarre une session
session_start();
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
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

$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
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

require_once('close.php');
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
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Contenu principal -->
    <main class="container mt-5">
    <?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">Fermer</button>
    </div>
    <?php endif; ?>

        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
            <div class="carousel-inner">
                <?php
                $active = 'active';
                foreach($result as $produit){
                    $image_path = 'image_produit/' . $produit['image_produit'];
                    if (!file_exists($image_path)) {
                        echo '<div class="alert alert-danger" role="alert">Image non trouvée : ' . $image_path . '</div>';
                    }
                ?>
                <div class="carousel-item <?= $active ?>">
                    <img src="<?= $image_path ?>" class="d-block w-100" alt="<?= $produit['produit'] ?>" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
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
            foreach($result as $produit){
                $image_path = 'image_produit/' . $produit['image_produit'];
                if (!file_exists($image_path)) {
                    echo '<div class="alert alert-danger" role="alert">Image non trouvée : ' . $image_path . '</div>';
                }
                $badgeClass = '';
                if ($produit['nombre'] == 0) {
                    $quantityText = '<i class="fas fa-exclamation-triangle"></i> Victime de son succès';
                    $quantityClass = 'out-of-stock';
                } elseif ($produit['nombre'] <= 20) {
                    $quantityClass = 'low-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } elseif ($produit['nombre'] <= 50) {
                    $quantityClass = 'medium-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } elseif ($produit['nombre'] <= 100) {
                    $quantityClass = 'high-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } else {
                    $quantityClass = 'very-high-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                }
                $badgeClass = 'badge-primary'; // Default badge class
                if ($produit['badge'] == 'danger') {
                    $badgeClass = 'badge-danger';
                } elseif ($produit['badge'] == 'warning') {
                    $badgeClass = 'badge-warning';
                } elseif ($produit['badge'] == 'success') {
                    $badgeClass = 'badge-success';
                } elseif ($produit['badge'] == 'info') {
                    $badgeClass = 'badge-info';
                } elseif ($produit['badge'] == 'dark') {
                    $badgeClass = 'badge-dark';
                } elseif ($produit['badge'] == 'secondary') {
                    $badgeClass = 'badge-secondary';
                } elseif ($produit['badge'] == 'purple') {
                    $badgeClass = 'badge-purple';
                } elseif ($produit['badge'] == 'yellow') {
                    $badgeClass = 'badge-yellow';
                } elseif ($produit['badge'] == 'peach') {
                    $badgeClass = 'badge-peach';
                } elseif ($produit['badge'] == 'fire') {
                    $badgeClass = 'badge-fire';
                } elseif ($produit['badge'] == 'pink') {
                    $badgeClass = 'badge-pink';
                } elseif ($produit['badge'] == 'light-red') {
                    $badgeClass = 'badge-light-red';
                } elseif ($produit['badge'] == 'dark-green') {
                    $badgeClass = 'badge-dark-green';
                } elseif ($produit['badge'] == 'sea-water') {
                    $badgeClass = 'badge-sea-water';
                } elseif ($produit['badge'] == 'gold') {
                    $badgeClass = 'badge-gold';
                } elseif ($produit['badge'] == 'cyan') {
                    $badgeClass = 'badge-cyan';
                } elseif ($produit['badge'] == 'brown') {
                    $badgeClass = 'badge-brown';
                } elseif ($produit['badge'] == 'silver') {
                    $badgeClass = 'badge-silver';
                } elseif ($produit['badge'] == 'black') {
                    $badgeClass = 'badge-black';
                }

                $description = implode(' ', array_slice(explode(' ', $produit['Description']), 0, 30)) . '...';
                $prix = is_numeric(str_replace(',', '.', $produit['prix'])) ? (float)str_replace(',', '.', $produit['prix']) : 0;
                $promo = is_numeric($produit['Promo']) ? (float)$produit['Promo'] : 0;

                // Appliquer la réduction Prime pour les produits Amazon
                $isAmazon = strtolower($produit['production_company']) === 'amazon';
                $primeDiscount = ($isPrime && $isAmazon) ? 10 : 0;

                // Calculer le prix après réduction
                $totalDiscount = min($promo + $primeDiscount, 100); // Limiter à 100 %
                $finalPrice = $prix * (1 - $totalDiscount / 100);
                
            ?>
            <div class="col-md-4 col-sm-6 mb-4">
            <div class="card" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
                <div class="position-relative">
                    <img src="image_produit/<?= htmlspecialchars($produit['image_produit']); ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['produit']); ?>">
                    <span class="badge badge-primary badge-bottom-right"><?= htmlspecialchars($produit['badge']); ?></span>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($produit['produit']); ?></h5>
                    <p class="card-text"><?= htmlspecialchars($description); ?></p>
                    <p class="card-text"><strong>Produit par :</strong> <?= htmlspecialchars($produit['production_company'] ?? 'Inconnu'); ?></p>

                    <?php if ($promo > 0 || $primeDiscount > 0): ?>
                        <p class="card-price">
                            <span class="card-price-original"><?= htmlspecialchars(number_format($prix, 2)); ?> €</span>
                            <span class="card-price-promo"><?= number_format($finalPrice, 2); ?> € (-<?= $totalDiscount; ?>%)</span>
                        </p>
                    <?php else: ?>
                        <p class="card-price"><?= htmlspecialchars(number_format($prix, 2)); ?> €</p>
                    <?php endif; ?>

                    <p class="card-quantity"><?= htmlspecialchars($produit['nombre'] > 0 ? 'Quantité : ' . $produit['nombre'] : 'En rupture de stock'); ?></p>
                    <a href="add_to_cart.php?product_id=<?= $produit['id']; ?>&quantity=1" class="btn btn-primary add-to-cart">Ajouter au panier</a>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>