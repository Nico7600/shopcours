<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['id'])) {
    $_SESSION['erreur'] = "Vous devez être connecté pour accéder à cette page";
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['id'];
$sql = 'SELECT admin FROM users WHERE id = :id';
$query = $db->prepare($sql);
$query->bindValue(':id', $userId, PDO::PARAM_INT);
$query->execute();
$user = $query->fetch();

if (!$user || $user['admin'] != 1) {
    $_SESSION['erreur'] = "Vous n'avez pas les accès à cette page";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_maintenance'])) {
    $maintenance = file_exists('maintenance.flag');
    if ($maintenance) {
        unlink('maintenance.flag');
    } else {
        file_put_contents('maintenance.flag', '1');
    }
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_prime'])) {
    $userId = $_POST['user_id'];
    $isPrime = $_POST['is_prime'] ? 0 : 1;
    $sql = 'UPDATE users SET is_prime = :is_prime WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':is_prime', $isPrime, PDO::PARAM_INT);
    $query->bindValue(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_admin'])) {
    $userId = $_POST['user_id'];
    $isAdmin = $_POST['is_admin'] ? 0 : 1;
    $sql = 'UPDATE users SET admin = :admin WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':admin', $isAdmin, PDO::PARAM_INT);
    $query->bindValue(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    header('Location: admin.php');
    exit;
}

// Fetch recent registered users
$sql = 'SELECT id, fname, username, is_prime, admin, date FROM users ORDER BY id DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentUsers = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent sales
$sql = 'SELECT id, order_date, total_amount FROM orders ORDER BY order_date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentSales = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: 'Ubuntu', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .navbar {
            width: 100%;
            background-color: #343a40;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar a, .navbar form {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 0 10px;
            text-align: center;
        }
        .admin-container {
            text-align: center;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 100%;
            margin-top: 80px; /* Adjust for navbar height */
            border-radius: 10px;
        }
        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        .btn-maintenance-on {
            background-color: #dc3545;
            color: #ffffff;
        }
        .btn-maintenance-on:hover {
            background-color: #c82333;
            color: #ffffff;
        }
        .btn-maintenance-off {
            background-color: #28a745;
            color: #ffffff;
        }
        .btn-maintenance-off:hover {
            background-color: #218838;
            color: #ffffff;
        }
        .table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .table th {
            background-color: #343a40;
            color: #ffffff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-title {
            text-align: center;
            margin-top: 40px;
            font-size: 1.5rem;
            font-weight: 600;
        }
        .btn-toggle-on {
            background-color: #28a745;
            color: #ffffff;
            margin: 5px;
        }
        .btn-toggle-off {
            background-color: #dc3545;
            color: #ffffff;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="admin.php">Valomazone Admin</a>
        <a href="add.php">Ajouter produit</a>
        <form method="post" style="display:inline;">
            <button type="submit" name="toggle_maintenance" class="btn <?php echo file_exists('maintenance.flag') ? 'btn-maintenance-on' : 'btn-maintenance-off'; ?>">
                <?php echo file_exists('maintenance.flag') ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
            </button>
        </form>
    </div>
    <h1>Bienvenue sur la page admin</h1>
    <div class="admin-container">
        <div class="user-list">
            <h2 class="table-title">Derniers inscrits</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Nom d'utilisateur</th>
                        <th>Membre Prime</th>
                        <th>Staff</th>
                        <th>Date d'inscription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['fname']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['is_prime'] ? 'Oui' : 'Non'; ?></td>
                            <td><?php echo $user['admin'] ? 'Oui' : 'Non'; ?></td>
                            <td class="date"><?php echo htmlspecialchars(date('Le d/m/Y à H:i:s', strtotime($user['date']))); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="is_prime" value="<?php echo $user['is_prime']; ?>">
                                    <button type="submit" name="toggle_prime" class="btn <?php echo $user['is_prime'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>">
                                        <?php echo $user['is_prime'] ? 'Prime On' : 'Prime Off'; ?>
                                    </button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="is_admin" value="<?php echo $user['admin']; ?>">
                                    <button type="submit" name="toggle_admin" class="btn <?php echo $user['admin'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>">
                                        <?php echo $user['admin'] ? 'Admin On' : 'Admin Off'; ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="sales-list">
            <h2 class="table-title">Dernières ventes</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Montant total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['id']); ?></td>
                            <td class="date"><?php echo htmlspecialchars(date('Le d/m/Y à H:i:s', strtotime($sale['order_date']))); ?></td>
                            <td><?php echo htmlspecialchars($sale['total_amount']); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- ...existing code for admin page content... -->
    </div>
</body>
</html>