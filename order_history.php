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
function getOrderHistory($userId) {
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #343a40;
            padding: 5px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            color: #ffffff;
            font-size: 1.5rem;
            font-weight: bold;
            transition: color 0.3s;
        }
        .navbar-brand:hover {
            color: #ff9900;
        }
        .nav-link {
            color: #ffffff;
            font-size: 1rem;
            margin-right: 10px;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #ff9900;
        }
        .btn-outline-success, .btn-outline-light {
            color: #ffffff;
            border-color: #ff9900;
            padding: 5px 10px;
            cursor: pointer;
            transition: background-color 0.4s, color 0.4s;
            font-size: 1rem;
        }
        .btn-outline-success:hover, .btn-outline-light:hover {
            background-color: #ff9900;
            border-color: #ff9900;
            color: #343a40;
        }
        .form-control {
            border-radius: 0;
            width: 100%;
            max-width: 500px;
            transition: width 0.4s;
        }
        .form-control:focus {
            max-width: 550px;
        }
        .navbar-center {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .navbar-center .nav-item {
            margin-left: 10px;
        }
        .navbar-center .nav-link {
            padding-top: 5px;
        }
        .navbar-center .dropdown-menu {
            font-size: 1rem;
        }
        .navbar-center .form-control, .navbar-center .btn-outline-success {
            height: 30px;
        }
        .table {
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #ff9900;
            border-color: #ff9900;
        }
        .btn-primary:hover {
            background-color: #e68a00;
            border-color: #e68a00;
        }
        .alert {
            margin-top: 20px;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .navbar {
            background-color: #1f1f1f;
        }
        .dark-mode .table {
            background-color: #1f1f1f;
            color: #ffffff;
        }
        .dark-mode .card {
            background-color: #1f1f1f;
            color: #ffffff;
        }
        .dark-mode .card-title,
        .dark-mode .card-text,
        .dark-mode .card-price,
        .dark-mode .card-quantity {
            color: #ffffff;
        }
        .dark-mode .carousel-caption {
            background-color: rgba(0, 0, 0, 0.7);
        }
        .dark-mode-toggle {
            display: flex;
            align-items: center;
            margin-left: 15px;
        }
        .dark-mode-toggle button {
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .navbar-center {
                flex-direction: column;
            }
            .navbar-center .nav-item {
                margin-left: 0;
                margin-bottom: 10px;
            }
            .navbar-center .form-control {
                width: 100%;
                max-width: none;
            }
            .navbar-center .btn-outline-success {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Valomazone</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="#">Promotions</a></li>
                <li class="nav-item"><a class="nav-link" href="cart_view.php">Panier</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Support</a></li>
            </ul>
            <form class="d-flex me-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Chercher un produit" aria-label="Search">
                <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="ms-auto">
                <div class="dropdown">
                    <?php if ($userName): ?>
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            Bonjour, <?= htmlspecialchars($userName); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                            <li><a class="dropdown-item" href="order_history.php">Historique d'achat</a></li>
                            <li><a class="dropdown-item" href="logout.php">Se déconnecter</a></li>
                        </ul>
                    <?php else: ?>
                        <button type="button" class="btn btn-outline-light ms-3" data-bs-toggle="modal" data-bs-target="#loginModal">Connecter</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</nav>
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
