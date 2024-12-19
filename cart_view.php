<?php
require_once 'connect.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int) $_SESSION['id'];

// Récupérer le statut Prime de l'utilisateur
$sqlUser = 'SELECT is_prime FROM users WHERE id = :id';
$queryUser = $db->prepare($sqlUser);
$queryUser->execute([':id' => $user_id]);
$user = $queryUser->fetch();
$isPrime = $user ? (bool) $user['is_prime'] : false;

// Récupérer les articles du panier
$sql = '
    SELECT c.id AS cart_id, l.produit, l.prix, l.Promo, c.quantity, l.image_produit, p.name AS production_company
    FROM cart c
    JOIN liste l ON c.product_id = l.id
    LEFT JOIN production_companies p ON l.production_company_id = p.id
    WHERE c.user_id = :user_id
';
$query = $db->prepare($sql);
$query->execute([':user_id' => $user_id]);
$cartItems = $query->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total
$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Panier</title>
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
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .table th, .table td {
            vertical-align: middle;
            font-size: 1rem;
        }
        .thead-dark {
            background-color: #343a40;
            color: #fff;
            font-weight: 700;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .btn-modifier {
            background-color: green;
            color: white;
            font-weight: 700;
        }
        .btn-primary {
            font-size: 1.25rem;
            font-weight: 700;
        }
        .btn-danger {
            font-weight: 700;
        }
        .btn-disabled {
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
            background-color: gray !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    <main class="container mt-5">
        <h1>Votre Panier</h1>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cartItems)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Votre panier est vide.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <?php
                        // Calculer le prix avec promo produit et Amazon Prime
                        $prixOriginal = is_numeric(str_replace(',', '.', $item['prix'])) 
                            ? (float)str_replace(',', '.', $item['prix']) 
                            : 0;
                        $prixFinal = $prixOriginal;

                        // Appliquer promo produit
                        if (is_numeric($item['Promo']) && $item['Promo'] > 0) {
                            $prixFinal *= (1 - $item['Promo'] / 100);
                        }

                        // Appliquer réduction Prime pour les produits Amazon
                        $companyName = strtolower(trim($item['production_company']));
                        if ($isPrime && $companyName === 'amazon') {
                            $prixFinal *= 0.9;
                        }

                        $prixFinal = max($prixFinal, 0); // Assurez-vous que le prix reste positif
                        $subtotal = $prixFinal * $item['quantity'];
                        $total += $subtotal;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['produit']) ?></td>
                            <td>
                                <?php if ($prixFinal < $prixOriginal): ?>
                                    <span class="text-muted"><del><?= number_format($prixOriginal, 2, ',', ' ') ?> €</del></span>
                                    <span><?= number_format($prixFinal, 2, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <?= number_format($prixOriginal, 2, ',', ' ') ?> €
                                <?php endif; ?>
                            </td>
                            <td>
                                <form class="update-quantity-form form-inline">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" class="form-control mr-2" min="1">
                                    <button type="submit" class="btn btn-modifier">Modifier</button>
                                </form>
                            </td>
                            <td><?= number_format($subtotal, 2, ',', ' ') ?> €</td>
                            <td>
                                <form class="delete-item-form">
                                    <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4"><strong>Total</strong></td>
                        <td><strong><?= number_format($total, 2, ',', ' ') ?> €</strong></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Bouton continuer -->
        <a href="checkout.php" 
           class="btn btn-primary <?= empty($cartItems) ? 'btn-disabled' : '' ?>" 
           <?= empty($cartItems) ? 'disabled' : '' ?> 
           onclick="<?= empty($cartItems) ? 'return false;' : '' ?>">
            Continuer vos achats
        </a>
    </main>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.update-quantity-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('update_quantity.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.delete-item-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch('delete_item.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
    </script>
</body>
</html>
