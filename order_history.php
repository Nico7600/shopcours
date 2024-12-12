<?php
require_once 'connect.php';
session_start();

$userName = null;
if (isset($_SESSION['id'])) {
    $sql = 'SELECT fname FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userName = $user['fname'];
    }
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

// Fonction pour récupérer les commandes d'un utilisateur
function getOrderHistory($userId)
{
    global $db;

    $sql = '
        SELECT o.id AS order_id, o.order_date, o.total_amount, 
               GROUP_CONCAT(CONCAT(oi.quantity, "x ", l.produit, " (", FORMAT(oi.price, 2), " €)") SEPARATOR ", ") AS items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN liste l ON oi.product_id = l.id
        WHERE o.user_id = :user_id
        GROUP BY o.id
        ORDER BY o.order_date DESC
    ';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    return $query->fetchAll(PDO::FETCH_ASSOC);
}


// Récupérer l'historique des commandes
$orderHistory = getOrderHistory($userId);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }
        .table th, .table td {
            font-size: 1rem;
            font-weight: 400;
        }
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .modal-body p {
            font-size: 1.2rem;
            font-weight: 400;
        }
        .btn {
            font-size: 1rem;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    <main class="container mt-5">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">Fermer</button>
            </div>
        <?php endif; ?>
        <h1 class="mb-4">Historique des commandes</h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Numéro de commande</th>
                        <th scope="col">Date</th>
                        <th scope="col">Produit</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderHistory as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                            <td><?= htmlspecialchars($order['order_date']) ?></td>
                            <td><?= htmlspecialchars($order['items']) ?></td>
                            <td><?= number_format($order['total_amount'], 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Se connecter ou créer un compte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bienvenue sur Valomazone !</p>
                    <div class="d-grid gap-2">
                        <a href="login.php" class="btn btn-primary">Se connecter</a>
                        <a href="register.php" class="btn btn-secondary">Créer un compte</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>