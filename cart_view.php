<?php
require_once 'bootstrap.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = (int)$_SESSION['id'];

try {
    $sqlUser = 'SELECT is_prime FROM users WHERE id = :id';
    $queryUser = $db->prepare($sqlUser);
    $queryUser->execute([':id' => $userId]);
    $user = $queryUser->fetch();
    $isPrime = $user ? (bool)$user['is_prime'] : false;
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des informations utilisateur : " . $e->getMessage());
    $isPrime = false;
}

try {
    $sql = '
        SELECT c.id AS cart_id, l.produit, l.prix, l.Promo, c.quantity, l.image_produit, p.name AS production_company
        FROM cart c
        JOIN liste l ON c.product_id = l.id
        LEFT JOIN production_companies p ON l.production_company_id = p.id
        WHERE c.user_id = :user_id
    ';
    $query = $db->prepare($sql);
    $query->execute([':user_id' => $userId]);
    $cartItems = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des articles du panier : " . $e->getMessage());
    $cartItems = [];
}

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
            color: #000000;
        }
        .card-text {
            font-size: 1rem;
            color: #000000;
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
        .adhesion-prime-title {
            color: #ff5733;
        }
        .adhesion-prime-subtitle {
            color: #33c1ff;
        }
        .table th, .table td {
            color: #ffffff;
        }
        .btn-modifier {
            background-color: #007bff !important;
            color: white;
            font-weight: bold;
            border: 1px solid #007bff;
        }
        .btn-modifier:hover {
            background-color: #28a745 !important;
            border: 1px solid #28a745;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <main class="container mt-5">
        <h1 style="text-align: center;">
            <span style="color: #ff5733;">Votre</span><span style="color: #33c1ff;"> Panier</span>
        </h1>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th style="color: #ff5733;"><i class="fas fa-box" style="color: #33c1ff;"></i> Produit</th>
                    <th style="color: #ff5733;"><i class="fas fa-euro-sign" style="color: #33c1ff;"></i> Prix unitaire</th>
                    <th style="color: #ff5733;"><i class="fas fa-sort-numeric-up" style="color: #33c1ff;"></i> Quantité</th>
                    <th style="color: #ff5733;"><i class="fas fa-calculator" style="color: #33c1ff;"></i> Sous-total</th>
                    <th style="color: #ff5733;"><i class="fas fa-cogs" style="color: #33c1ff;"></i> Actions</th>
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
                        $prixOriginal = is_numeric(str_replace(',', '.', $item['prix'])) 
                            ? (float)str_replace(',', '.', $item['prix']) 
                            : 0;
                        $prixFinal = $prixOriginal;

                        if (is_numeric($item['Promo']) && $item['Promo'] > 0) {
                            $prixFinal *= (1 - $item['Promo'] / 100);
                        }

                        $companyName = strtolower(trim($item['production_company']));
                        if ($isPrime && $companyName === 'amazon') {
                            $prixFinal *= 0.9;
                        }

                        $prixFinal = max($prixFinal, 0);
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

        <a href="checkout.php" 
           class="btn btn-primary <?= empty($cartItems) ? 'btn-disabled' : '' ?>" 
           <?= empty($cartItems) ? 'disabled' : '' ?> 
           onclick="<?= empty($cartItems) ? 'return false;' : '' ?>">
            Continuer vos achats
        </a>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
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
