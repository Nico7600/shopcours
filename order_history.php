<?php
require_once 'bootstrap.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];
$userName = null;

try {
    $sql = 'SELECT fname FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userName = $user['fname'];
    } else {
        session_destroy();
        header('Location: login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération des informations utilisateur : ' . $e->getMessage());
    header('Location: error.php');
    exit();
}

function getOrderHistory($userId, $db)
{
    try {
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
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des commandes : ' . $e->getMessage());
        return [];
    }
}

$orderHistory = getOrderHistory($userId, $db);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des commandes</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            color: white; /* Ensure all text is white */
            text-align: center; /* Center all text */
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center; /* Center the title */
        }
        .table th, .table td {
            font-size: 1rem;
            font-weight: 400;
            text-align: center; /* Center text in the table */
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
        .half-orange-blue {
            background: linear-gradient(to right, #ff5733 50%, #007bff 50%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .orange-text {
            color: #ff5733;
        }
        .blue-text {
            color: #007bff;
        }
        .orange-icon {
            color: #ff5733;
        }
        .blue-icon {
            color: #007bff;
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <main class="container mt-5">
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">Fermer</button>
            </div>
        <?php endif; ?>
        <h1 class="mb-4">
            <i class="fas fa-shopping-cart orange-icon"></i> <span class="orange-text">Historique</span> <span class="blue-text">des commandes</span> <i class="fas fa-shopping-cart blue-icon"></i>
        </h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="color: #ff5733;"><i class="fas fa-box" style="color: #33c1ff;"></i> Numéro de commande</th>
                        <th scope="col" style="color: #ff5733;"><i class="fas fa-calendar-alt" style="color: #33c1ff;"></i> Date</th>
                        <th scope="col" style="color: #ff5733;"><i class="fas fa-box" style="color: #33c1ff;"></i> Produit</th>
                        <th scope="col" style="color: #ff5733;"><i class="fas fa-euro-sign" style="color: #33c1ff;"></i> Total</th>
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