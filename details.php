<?php
// On démarre une session
session_start();
require_once('connect.php');

if(isset($_GET['id']) && !empty($_GET['id'])){
    $db->exec("SET NAMES 'utf8mb4'");

    $id = strip_tags($_GET['id']);

    $sql = '
        SELECT l.*, p.name AS production_company
        FROM liste l
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE l.id = :id
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    $produit = $query->fetch();

    if(!$produit){
        $_SESSION['erreur'] = "Cet id n'existe pas";
        header('Location: index.php');
        exit;
    }

     // Vérifier si l'utilisateur est connecté et récupérer son statut Prime
    $isPrime = false;
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $sqlUser = 'SELECT is_prime FROM users WHERE id = :id';
        $queryUser = $db->prepare($sqlUser);
        $queryUser->bindValue(':id', $userId, PDO::PARAM_INT);
        $queryUser->execute();
        $user = $queryUser->fetch();

        if ($user) {
            $isPrime = (bool)$user['is_prime'];
        }
    }
    
    // Determine badge color based on product name
    $badgeClass = '';
    $emoji = '';
    switch ($produit['produit']) {
        case 'couteau':
            $badgeClass = 'badge badge-danger';
            $emoji = '🔪';
            break;
        case 'classic':
            $badgeClass = 'badge badge-primary';
            $emoji = '🔫';
            break;
        case 'shorty':
            $badgeClass = 'badge badge-warning';
            $emoji = '🔫';
            break;
        case 'Frenzy':
            $badgeClass = 'badge badge-purple';
            $emoji = '🔫';
            break;
        case 'Ghost':
            $badgeClass = 'badge badge-yellow';
            $emoji = '👻';
            break;
        case 'sherif':
            $badgeClass = 'badge badge-success';
            $emoji = '🤠';
            break;
        case 'Stinger':
            $badgeClass = 'badge badge-peach';
            $emoji = '🐝';
            break;
        case 'spectre':
            $badgeClass = 'badge badge-fire';
            $emoji = '🔥';
            break;
        case 'Bucky':
            $badgeClass = 'badge badge-pink';
            $emoji = '🐶';
            break;
        case 'Bouldog':
            $badgeClass = 'badge badge-light-red';
            $emoji = '🐕';
            break;
        case 'guardian':
            $badgeClass = 'badge badge-dark-green';
            $emoji = '🛡️';
            break;
        case 'Phantom':
            $badgeClass = 'badge badge-sea-water';
            $emoji = '👻';
            break;
        case 'Vandal':
            $badgeClass = 'badge badge-gold';
            $emoji = '💣';
            break;
        case 'marchal':
            $badgeClass = 'badge badge-cyan';
            $emoji = '👮';
            break;
        case 'opérator':
            $badgeClass = 'badge badge-brown';
            $emoji = '👨‍✈️';
            break;
        case 'ares':
            $badgeClass = 'badge badge-silver';
            $emoji = '🛡️';
            break;
        case 'odin':
            $badgeClass = 'badge badge-black';
            $emoji = '⚔️';
            break;
    }

    $prixOriginal = is_numeric(str_replace(',', '.', $produit['prix'])) 
        ? (float)str_replace(',', '.', $produit['prix']) 
        : 0;
    $prixPromo = $prixOriginal;

    if (is_numeric($produit['Promo']) && $produit['Promo'] > 0) {
        $prixPromo *= (1 - $produit['Promo'] / 100);
    }

    $companyName = strtolower(trim($produit['production_company']));
    if ($isPrime && $companyName === 'amazon') {
        $prixPromo *= 0.9; // Réduction supplémentaire de 10%
        $prixPromo = max($prixPromo, 0); // S'assurer que le prix reste positif
    }
    
    require_once('close.php');
} else{
    $_SESSION['erreur'] = "URL invalide";
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        .product-details {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .product-image {
            flex: 1;
            max-width: 500px;
            margin-right: 20px;
            position: relative;
            border-radius: 10px;
            overflow: hidden;
        }
        .product-image img {
            border-radius: 10px;
        }
        .product-info {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .product-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #343a40;
        }
        .product-description {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #6c757d;
            white-space: pre-wrap; /* Ensure all line breaks and spaces are preserved */
        }
        .product-price {
            font-size: 1.8rem;
            color: #ff9900;
            margin-bottom: 20px;
        }
        .product-quantity {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 20px;
        }
        .btn-back {
            margin-top: 20px;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
            margin-left: 0.5em;
        }
        .badge-danger { background-color: #dc3545; }
        .badge-primary { background-color: #007bff; }
        .badge-warning { background-color: #ffc107; }
        .badge-purple { background-color: #6f42c1; }
        .badge-yellow { background-color: #ffc107; }
        .badge-success { background-color: #28a745; }
        .badge-peach { background-color: #ffcccb; }
        .badge-fire { background-color: #fd7e14; }
        .badge-pink { background-color: #e83e8c; }
        .badge-light-red { background-color: #f8d7da; }
        .badge-dark-green { background-color: #155724; }
        .badge-sea-water { background-color: #17a2b8; }
        .badge-gold { background-color: #ffd700; }
        .badge-cyan { background-color: #17a2b8; }
        .badge-brown { background-color: #795548; }
        .badge-silver { background-color: #c0c0c0; }
        .badge-black { background-color: #343a40; }
        .badge-bottom-right {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 0.5em 0.75em;
            background-color: rgba(0, 0, 0, 0.7); /* Ensure visibility */
            color: #ffffff;
        }
        .card-price-original {
            text-decoration: line-through;
            color: #6c757d;
            margin-right: 10px;
        }
        .card-price-promo {
            color: #ff9900;
            font-weight: bold;
        }
        .btn-container {
            display: flex;
            gap: 10px; /* Add spacing between buttons */
            margin-top: 20px; /* Add margin to the top of the button container */
        }
        .btn-custom {
            flex: 1; /* Ensure buttons take equal space */
            height: 50px; /* Ensure both buttons have the same height */
            line-height: 50px; /* Ensure text is vertically centered */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-add-to-cart {
            font-size: 1.2rem; /* Adjust the size of the "Ajouter au panier" button */
        }
        .btn-back {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
<main class="container">
        <div class="row">
            <section class="col-12">
                <div class="product-details">
                    <div class="product-image">
                        <img src="image_produit/<?= htmlspecialchars($produit['image_produit']) ?>" alt="<?= htmlspecialchars($produit['produit']) ?>" class="img-fluid">
                        <span class="badge <?= $badgeClass ?> badge-bottom-right"><?= $emoji ?></span>
                    </div>
                    <div class="product-info">
                        <h1 class="product-title"><?= htmlspecialchars($produit['produit']) ?></h1>
                        <p class="product-description"><?= nl2br(htmlspecialchars($produit['Description'])) ?></p>
                        <p><strong>Société de production :</strong> <?= htmlspecialchars($produit['production_company'] ?? 'Inconnu') ?></p>

                        <!-- Affichage des prix -->
                        <?php if ($prixPromo < $prixOriginal): ?>
                            <p class="product-price">
                                <span class="card-price-original"><?= number_format($prixOriginal, 2) ?> €</span>
                                <span class="card-price-promo"><?= number_format($prixPromo, 2) ?> €</span>
                            </p>
                        <?php else: ?>
                            <p class="product-price"><?= number_format($prixOriginal, 2) ?> €</p>
                        <?php endif; ?>

                        <p class="product-quantity">Quantité restante : <?= htmlspecialchars($produit['nombre']) ?></p>

                        <!-- Boutons -->
                        <div class="btn-container">
                            <form method="post" action="cart.php">
                                <input type="hidden" name="id_produit" value="<?= $produit['id'] ?>">
                                <input type="hidden" name="quantite" value="1"> <!-- Quantité par défaut -->
                                <button type="submit" class="btn btn-success btn-custom btn-add-to-cart">🛒 Ajouter au panier</button>
                            </form>
                            <a href="index.php" class="btn btn-primary btn-custom btn-back">Retour</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>