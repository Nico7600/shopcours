<?php
require_once('bootstrap.php');

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

// Log user access to admin page
logAction($userId, 'Accès à la page admin');

// Bypass maintenance mode for admin users
if (file_exists('maintenance.flag') && basename($_SERVER['PHP_SELF']) != 'admin.php') {
    $endTime = file_get_contents('maintenance.flag');
    if (time() > $endTime) {
        unlink('maintenance.flag');
    } elseif (!$user['admin']) {
        $_SESSION['erreur'] = "Le site est en maintenance";
        header('Location: index.php');
        exit;
    }
}

function logAction($userId, $action) {
    global $db;
    $sql = 'INSERT INTO logs (user_id, action, created_at) VALUES (:user_id, :action, NOW())';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':action', $action, PDO::PARAM_STR);
    $query->execute();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        $_SESSION['erreur'] = "Invalid CSRF token.";
        header('Location: admin.php');
        exit;
    }

    if (isset($_POST['toggle_maintenance'])) {
        $maintenance = file_exists('maintenance.flag');
        if ($maintenance) {
            unlink('maintenance.flag');
        } else {
            $duration = filter_input(INPUT_POST, 'maintenance_duration', FILTER_SANITIZE_NUMBER_INT);
            $endTime = time() + ($duration * 60);
            file_put_contents('maintenance.flag', $endTime);
        }
        logAction($userId, $maintenance ? 'Maintenance désactivée' : 'Maintenance activée');
        header('Location: admin.php');
        exit;
    }

    if (isset($_POST['toggle_prime'])) {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
        $isPrime = filter_input(INPUT_POST, 'is_prime', FILTER_SANITIZE_NUMBER_INT) ? 0 : 1;
        $sql = 'UPDATE users SET is_prime = :is_prime WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':is_prime', $isPrime, PDO::PARAM_INT);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        logAction($_SESSION['id'], $isPrime ? 'Prime activé pour l\'utilisateur ' . $userId : 'Prime désactivé pour l\'utilisateur ' . $userId);
        header('Location: admin.php');
        exit;
    }

    if (isset($_POST['toggle_admin'])) {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
        $isAdmin = filter_input(INPUT_POST, 'is_admin', FILTER_SANITIZE_NUMBER_INT) ? 0 : 1;
        $sql = 'UPDATE users SET admin = :admin WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':admin', $isAdmin, PDO::PARAM_INT);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();
        logAction($_SESSION['id'], $isAdmin ? 'Admin désactivé pour l\'utilisateur ' . $userId : 'Admin activé pour l\'utilisateur ' . $userId);
        header('Location: admin.php');
        exit;
    }

    if (isset($_POST['ban_user'])) {
        if (!empty($_POST['reason']) && !empty($_POST['duration'])) {
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $reason = filter_input(INPUT_POST, 'reason', FILTER_SANITIZE_STRING);
            $duration = filter_input(INPUT_POST, 'duration', FILTER_SANITIZE_NUMBER_INT);
            $banEndDate = date('Y-m-d H:i:s', strtotime("+$duration days"));

            $sql = 'INSERT INTO bans (user_id, reason, ban_end_date, banned_by) VALUES (:user_id, :reason, :ban_end_date, :banned_by)';
            $query = $db->prepare($sql);
            $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $query->bindValue(':reason', $reason, PDO::PARAM_STR);
            $query->bindValue(':ban_end_date', $banEndDate, PDO::PARAM_STR);
            $query->bindValue(':banned_by', $_SESSION['id'], PDO::PARAM_INT);
            $query->execute();

            $sql = 'UPDATE users SET banned = 1 WHERE id = :id';
            $query = $db->prepare($sql);
            $query->bindValue(':id', $userId, PDO::PARAM_INT);
            $query->execute();

            foreach ($recentUsers as &$user) {
                if ($user['id'] == $userId) {
                    $user['banned'] = 1;
                    break;
                }
            }

            logAction($_SESSION['id'], 'Utilisateur ' . $userId . ' banni pour ' . $duration . ' jours. Raison: ' . $reason);
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['erreur'] = "Reason and duration are required.";
            header('Location: admin.php');
            exit;
        }
    }

    if (isset($_POST['unban_user'])) {
        $banId = filter_input(INPUT_POST, 'ban_id', FILTER_SANITIZE_NUMBER_INT);

        $sql = 'INSERT INTO ban_history (user_id, reason, ban_end_date, banned_by)
                SELECT user_id, reason, ban_end_date, banned_by FROM bans WHERE id = :ban_id';
        $query = $db->prepare($sql);
        $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
        $query->execute();

        $sql = 'SELECT user_id FROM bans WHERE id = :ban_id';
        $query = $db->prepare($sql);
        $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
        $query->execute();
        $userId = $query->fetchColumn();

        $sql = 'DELETE FROM bans WHERE id = :ban_id';
        $query = $db->prepare($sql);
        $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
        $query->execute();

        $sql = 'UPDATE users SET banned = 0 WHERE id = :user_id';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();

        foreach ($bannedUsers as $key => $ban) {
            if ($ban['user_id'] == $userId) {
                unset($bannedUsers[$key]);
                break;
            }
        }

        foreach ($recentUsers as &$user) {
            if ($user['id'] == $userId) {
                $user['banned'] = 0;
                break;
            }
        }

        logAction($_SESSION['id'], 'Utilisateur ' . $userId . ' débanni');
        header('Location: admin.php');
        exit;
    }
}

$sql = 'SELECT id, user_id FROM bans WHERE ban_end_date <= NOW()';
$query = $db->prepare($sql);
$query->execute();
$expiredBans = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($expiredBans as $ban) {
    $banId = $ban['id'];
    $userId = $ban['user_id'];

    $sql = 'INSERT INTO ban_history (user_id, reason, ban_end_date, banned_by)
            SELECT user_id, reason, ban_end_date, banned_by FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    $sql = 'DELETE FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    $sql = 'UPDATE users SET banned = 0 WHERE id = :user_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();
}

$sql = 'SELECT bans.id AS ban_id, bans.user_id, users.username, bans.reason, bans.ban_end_date, admin.username AS banned_by_username 
        FROM bans 
        JOIN users ON bans.user_id = users.id 
        JOIN users AS admin ON bans.banned_by = admin.id';
$query = $db->prepare($sql);
$query->execute();
$bannedUsers = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT ban_history.id AS ban_id, ban_history.user_id, users.username, ban_history.reason, ban_history.ban_end_date, admin.username AS banned_by_username 
        FROM ban_history 
        JOIN users ON ban_history.user_id = users.id 
        JOIN users AS admin ON ban_history.banned_by = admin.id';
$query = $db->prepare($sql);
$query->execute();
$banHistory = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT users.id, users.fname, users.username, users.date, users.last_ip, orders.total_amount 
        FROM users 
        JOIN orders ON users.id = orders.user_id 
        WHERE users.is_prime = 1 
        ORDER BY orders.order_date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentPrimeMembers = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT id, produit, prix, nombre, description, badge, promo FROM liste ORDER BY id DESC';
$query = $db->prepare($sql);
$query->execute();
$listeItems = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT DATE_FORMAT(date, "%d/%m/%Y") as date, COUNT(*) as count FROM users GROUP BY DATE(date) ORDER BY date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$registrationsData = $query->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT DATE_FORMAT(order_date, "%d/%m/%Y") as date, SUM(total_amount) as total FROM orders GROUP BY DATE(order_date) ORDER BY date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$salesData = $query->fetchAll(PDO::FETCH_ASSOC);

try {
    $pdo = new PDO('mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT users.id, users.username, crud.expiration_date FROM users JOIN crud ON users.id = crud.user_id ORDER BY crud.expiration_date DESC LIMIT 10");
    $stmt->execute();
    $primeMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur de connexion à la base de données: " . $e->getMessage();
}

$sql = 'SELECT logs.id, logs.action, logs.created_at, users.username FROM logs JOIN users ON logs.user_id = users.id ORDER BY logs.created_at DESC';
$query = $db->prepare($sql);
$query->execute();
$logs = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #343a40;
            color: #ffffff;
            font-family: 'Ubuntu', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding-top: 80px;
        }
        .navbar {
            width: 100%;
            background-color: #212529;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            margin-bottom: 20px;
        }
        .navbar a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2rem;
            margin-right: 20px;
        }
        .navbar .menu {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-grow: 1;
        }
        .navbar .menu a, .navbar .menu form {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 0 5px;
            text-align: center;
        }
        .navbar .menu button, .navbar .menu form button {
            margin: 0 5px;
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .admin-container {
            text-align: center;
            padding: 30px;
            background-color: #495057;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
            margin-top: 100px;
            border-radius: 10px;
            overflow-x: auto;
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
            background-color: #212529;
            color: #ffffff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #6c757d;
        }
        .table-title {
            text-align: center;
            margin-top: 40px;
            font-size: 2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .user-list .table-title {
            margin-top: 80px;
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
        .btn-toggle-on:hover {
            background-color: #218838;
        }
        .btn-toggle-off:hover {
            background-color: #c82333;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #343a40;
            color: #ffffff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: white;
            text-decoration: none;
            cursor: pointer;
        }
        .form-container {
            margin-top: 50px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #495057;
        }
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .secondary-navbar {
            width: 100%;
            background-color: #212529;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 60px;
            left: 0;
            z-index: 1000;
        }
        .secondary-navbar .menu {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .secondary-navbar.collapsed .menu {
            display: none;
        }
        .secondary-navbar.collapsed {
            height: 40px;
        }
        .liste-items .table td, .liste-items .table th {
            text-align: center;
            vertical-align: middle;
        }
        .charts-container {
            display: none; /* Hide charts container */
        }
        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        .btn-primary {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-success {
            background-color: #28a745;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .btn-danger {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        @media (max-width: 1200px) {
            .admin-container {
                padding: 20px;
            }
            .table th, .table td {
                padding: 10px;
            }
            .navbar a, .navbar .menu a, .navbar .menu form {
                font-size: 1rem;
            }
        }

        @media (max-width: 992px) {
            .admin-container {
                padding: 15px;
            }
            .table th, .table td {
                padding: 8px;
            }
            .navbar a, .navbar .menu a, .navbar .menu form {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .menu {
                flex-direction: column;
                align-items: flex-start;
            }
            .navbar .menu a, .navbar .menu form {
                margin: 5px 0;
            }
            .admin-container {
                padding: 10px;
            }
            .table th, .table td {
                padding: 6px;
            }
            .table-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 5px;
            }
            .navbar a {
                font-size: 1rem;
            }
            .navbar .menu a, .navbar .menu form {
                font-size: 0.8rem;
            }
            .admin-container {
                padding: 5px;
            }
            .table th, .table td {
                padding: 4px;
            }
            .table-title {
                font-size: 1.2rem;
            }
        }
        .table-container {
            margin-bottom: 20px;
            overflow-x: auto;
        }
        .table {
            width: 100%;
            min-width: 1000px; /* Adjust this value based on your table content */
            margin-top: 20px;
            border-collapse: collapse;
        }
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        .logs-container {
            display: flex; /* Change from display: none; to display: flex; */
            flex-direction: column;
            align-items: center;
            margin-top: 40px;
            background-color: #495057;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
        }
        .logs-table {
            width: 100%;
            max-width: 1000px;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .logs-table th, .logs-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .logs-table th {
            background-color: #212529;
            color: #ffffff;
        }
        .logs-table tbody tr:nth-child(even) {
            background-color: #6c757d;
        }
        .logs-table tbody tr:hover {
            background-color: #343a40;
            color: #ffffff;
        }
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            width: 100%;
            max-width: 1000px;
        }
        .filter-container select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #495057;
            color: #ffffff;
            width: 100%;
            max-width: 300px;
        }
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .btn-group .btn {
            flex: 1;
        }
        .copy-ip {
            cursor: pointer;
            color: #007bff;
            text-decoration: underline;
        }
        .copy-ip:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Valomazone Admin</a>
        <div class="menu">
            <button class="btn btn-primary" onclick="window.location.href='add.php'">Ajouter produit</button>
            <form method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="button" class="btn <?php echo file_exists('maintenance.flag') ? 'btn-maintenance-on' : 'btn-maintenance-off'; ?>" onclick="openMaintenanceModal()">
                    <?php echo file_exists('maintenance.flag') ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
                </button>
            </form>
            <button class="btn btn-primary" onclick="openPatchNoteModal()">Ajouter Patch Note</button>
            <button id="optionsButton" class="btn btn-secondary" onclick="toggleSecondaryNavbar()">Options &#9881;</button>
        </div>
    </div>
    <div class="secondary-navbar">
        <div class="menu">
            <button id="toggleRecentUsersTable" class="btn btn-secondary" data-label="Derniers inscrits" onclick="toggleDataTable('recentUsersTable')">Activer Derniers inscrits</button>
            <button id="toggleRecentSalesTable" class="btn btn-secondary" data-label="Dernières ventes" onclick="toggleDataTable('recentSalesTable')">Activer Dernières ventes</button>
            <button id="toggleRecentPrimeMembersTable" class="btn btn-secondary" onclick="toggleDataTable('recentPrimeMembersTable')" data-label="Derniers membres Prime">Activer/Désactiver Derniers membres Prime</button>
            <button id="toggleBannedUsersTable" class="btn btn-secondary" onclick="toggleDataTable('bannedUsersTable')" data-label="Liste des ban en cours">Activer/Désactiver Liste des ban en cours</button>
            <button id="toggleBanHistoryTable" class="btn btn-secondary" onclick="toggleDataTable('banHistoryTable')" data-label="Historique des bans">Activer/Désactiver Historique des bans</button>
            <button id="toggleListeTable" class="btn btn-secondary" data-label="Liste des produits" onclick="toggleDataTable('listeTable')">Activer Liste des produits</button>
            <button id="toggleLogsTable" class="btn btn-secondary" data-label="Logs des actions" onclick="toggleDataTable('logsTable')">Activer Logs des actions</button>
        </div>
    </div>
    <div id="adminContainer" class="admin-container" style="display: none;">
        <div class="user-list">
            <div class="table-title">
                <h2>Derniers inscrits</h2>
            </div>
            <div id="recentUsersTableContainer" class="table-container">
                <table id="recentUsersTable" class="table table-striped table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Nom d'utilisateur</th>
                            <th>Membre Prime</th>
                            <th>Staff</th>
                            <th>Date d'inscription</th>
                            <th>Last IP</th>
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
                                <td class="date"><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($user['date']))); ?></td>
                                <td class="copy-ip" onclick="copyToClipboard('<?php echo htmlspecialchars($user['last_ip']); ?>')">Copier IP</td>
                                <td>
                                    <div class="btn-group">
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="is_prime" value="<?php echo $user['is_prime']; ?>">
                                            <button type="submit" name="toggle_prime" class="btn <?php echo $user['is_prime'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>">
                                                <?php echo $user['is_prime'] ? '<i class="fa fa-crown" style="color: gold;"></i> Prime On' : '<i class="fa fa-crown" style="color: white;"></i> Prime Off'; ?>
                                            </button>
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="is_admin" value="<?php echo $user['admin']; ?>">
                                            <button type="submit" name="toggle_admin" class="btn <?php echo $user['admin'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>">
                                                <?php echo $user['admin'] ? '<i class="fa fa-user-shield" style="color: blue;"></i> Admin On' : '<i class="fa fa-user-shield" style="color: white;"></i> Admin Off'; ?>
                                            </button>
                                        </form>
                                        <?php if ($user['banned']): ?>
                                            <?php foreach ($bannedUsers as $ban): ?>
                                                <?php if ($ban['user_id'] == $user['id']): ?>
                                                    <form method="post" style="display:inline;">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                        <input type="hidden" name="ban_id" value="<?php echo $ban['ban_id']; ?>">
                                                        <button type="submit" name="unban_user" class="btn btn-success">
                                                            <i class="fa-solid fa-gavel" style="color: white;"></i> Unban
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <button class="btn btn-danger" onclick="openBanModal(<?php echo $user['id']; ?>)">
                                                <i class="fa-solid fa-gavel" style="color: red;"></i> Ban
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="sales-list">
            <div class="table-title">
                <h2>Dernières ventes</h2>
            </div>
            <div id="recentSalesTableContainer" class="table-container">
                <table id="recentSalesTable" class="table table-striped table-dark" style="width:100%">
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
                                <td class="date"><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($sale['order_date']))); ?></td>
                                <td><?php echo htmlspecialchars($sale['total_amount']); ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="prime-members">
            <div class="table-title">
                <h2>Derniers membres Prime</h2>
            </div>
            <div id="recentPrimeMembersTableContainer" class="table-container">
                <table id="recentPrimeMembersTable" class="table table-striped table-dark" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Nom d'utilisateur</th>
                            <th>Date d'inscription</th>
                            <th>Last IP</th>
                            <th>Type d'abonnement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPrimeMembers as $primeMember): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($primeMember['id']); ?></td>
                                <td><?php echo htmlspecialchars($primeMember['fname']); ?></td>
                                <td><?php echo htmlspecialchars($primeMember['username']); ?></td>
                                <td class="date"><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($primeMember['date']))); ?></td>
                                <td class="copy-ip" onclick="copyToClipboard('<?php echo htmlspecialchars($primeMember['last_ip']); ?>')">Copier IP</td>
                                <td><?php echo $primeMember['total_amount'] == 9.99 ? '1 mois' : '1 an'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ban-user">
            <div class="table-title">
                <h2>Liste des ban en cours</h2>
            </div>
            <div id="bannedUsersTableContainer" class="table-container">
                <table id="bannedUsersTable" class="table table-striped table-dark" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID du ban</th>
                            <th>Nom d'utilisateur</th>
                            <th>Raison</th>
                            <th>Date de fin</th>
                            <th>Banni par</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bannedUsers as $ban): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ban['ban_id']); ?></td>
                                <td><?php echo htmlspecialchars($ban['username']); ?></td>
                                <td><?php echo htmlspecialchars($ban['reason']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($ban['ban_end_date']))); ?></td>
                                <td><?php echo htmlspecialchars($ban['banned_by_username']); ?></td>
                                <td>
                                    <?php if (strtotime($ban['ban_end_date']) > time()): ?>
                                        <button class="btn btn-success" onclick="openUnbanPage(<?php echo $ban['ban_id']; ?>)">
                                            Unban
                                        </button>
                                    <?php else: ?>
                                        <span class="text-success">L'utilisateur n'est plus banni</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="ban-history">
            <div class="table-title">
                <h2>Historique des bans</h2>
            </div>
            <div id="banHistoryTableContainer" class="table-container">
                <table id="banHistoryTable" class="table table-striped table-dark" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID du ban</th>
                            <th>Nom d'utilisateur</th>
                            <th>Raison</th>
                            <th>Date de fin</th>
                            <th>Banni par</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banHistory as $ban): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ban['ban_id']); ?></td>
                                <td><?php echo htmlspecialchars($ban['username']); ?></td>
                                <td><?php echo htmlspecialchars($ban['reason']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($ban['ban_end_date']))); ?></td>
                                <td><?php echo htmlspecialchars($ban['banned_by_username']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="liste-items">
            <div class="table-title">
                <h2>Liste des produits</h2>
            </div>
            <div id="listeTableContainer" class="table-container">
                <table id="listeTable" class="table table-striped table-dark" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produit</th>
                            <th>Prix</th>
                            <th>Nombre</th>
                            <th>Description</th>
                            <th>Badge</th>
                            <th>Promo</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listeItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['id']); ?></td>
                                <td><?php echo htmlspecialchars($item['produit']); ?></td>
                                <td><?php echo htmlspecialchars($item['prix']); ?> €</td>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td><?php echo htmlspecialchars($item['badge']); ?></td>
                                <td><?php echo htmlspecialchars($item['promo']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-primary" onclick="editProduct(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <button class="btn btn-danger" onclick="deleteProduct(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Ban Modal -->
        <div id="banModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeBanModal()">&times;</span>
                <h2>Ban User</h2>
                <form method="post" action="admin.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="user_id" id="banUserId">
                    <div class="form-group">
                        <label for="reason">Raison</label>
                        <input type="text" name="reason" id="reason" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="duration">Durée (jours)</label>
                        <input type="number" name="duration" id="duration" class="form-control" required>
                    </div>
                    <button type="submit" name="ban_user" class="btn btn-danger">Ban</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Maintenance Modal -->
    <div id="maintenanceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeMaintenanceModal()">&times;</span>
            <h2>Activer la maintenance</h2>
            <form method="post" action="admin.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="form-group">
                    <label for="maintenance_duration">Durée (minutes)</label>
                    <input type="number" name="maintenance_duration" id="maintenance_duration" class="form-control" required>
                </div>
                <button type="submit" name="toggle_maintenance" class="btn btn-danger">Activer</button>
            </form>
        </div>
    </div>
    <!-- Patch Note Modal -->
    <div id="patchNoteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePatchNoteModal()">&times;</span>
            <h2>Ajouter Patch Note</h2>
            <form method="post" action="patchnote.php">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <textarea name="update" required class="form-control mb-3"></textarea>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>
    </div>
    <div class="logs-container" id="logsTableContainer">
        <div class="table-title">
            <h2>Logs des actions</h2>
        </div>
        <div class="filter-container">
            <label for="logFilter">Filtrer par action:</label>
            <select id="logFilter" onchange="filterLogs()">
                <option value="">Tous</option>
                <option value="Maintenance activée">Maintenance activée</option>
                <option value="Maintenance désactivée">Maintenance désactivée</option>
                <option value="Prime activé">Prime activé</option>
                <option value="Prime désactivé">Prime désactivé</option>
                <option value="Admin activé">Admin activé</option>
                <option value="Admin désactivé">Admin désactivé</option>
                <option value="Utilisateur banni">Utilisateur banni</option>
                <option value="Utilisateur débanni">Utilisateur débanni</option>
                <option value="Patch note ajouté">Patch note ajouté</option>
            </select>
        </div>
        <div class="table-container">
            <table id="logsTable" class="logs-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <td><?php echo htmlspecialchars($log['username']); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        initializeAllTables();
        $('#adminContainer').show();
        updateAdminContainerVisibility();
        restoreOptionsMenuState();
        $('#logsTableContainer').show(); // Ensure logs container is displayed by default
    });

    function initializeAllTables() {
        initializeDataTable('recentUsersTable');
        initializeDataTable('recentSalesTable');
        initializeDataTable('recentPrimeMembersTable');
        initializeDataTable('bannedUsersTable');
        initializeDataTable('banHistoryTable');
        initializeDataTable('listeTable');
        initializeDataTable('logsTable');
        updateToggleButton('recentUsersTable', true);
        updateToggleButton('recentSalesTable', true);
        updateToggleButton('recentPrimeMembersTable', true);
        updateToggleButton('bannedUsersTable', true);
        updateToggleButton('banHistoryTable', true);
        updateToggleButton('listeTable', true);
        updateToggleButton('logsTable', true);
    }

    function filterLogs() {
        var filterValue = $('#logFilter').val().toLowerCase();
        $('#logsTableBody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(filterValue) > -1)
        });
    }

    function initializeDataTable(tableId) {
        if (!$.fn.dataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable({
                language: {
                    lengthMenu: "Afficher _MENU_ entrées par page",
                    search: "Rechercher:",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées (filtré de _MAX_ entrées au total)",
                    emptyTable: "Pas de résultat trouver",
                    zeroRecords: "Pas de résultat trouver",
                    infoEmpty: "Affichage de 0 à 0 sur 0 entrées"
                }
            });
            updateToggleButton(tableId, true);
        }
    }

    function toggleDataTable(tableId) {
        var container = $('#' + tableId + 'Container');
        var title = container.prev('.table-title');
        if ($.fn.dataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable().destroy();
            container.hide();
            title.hide();
            updateToggleButton(tableId, false);
            Cookies.set(tableId + 'Visible', 'false');
        } else {
            container.show();
            title.show();
            initializeDataTable(tableId);
            Cookies.set(tableId + 'Visible', 'true');
        }
        $('#adminContainer').show();
        updateAdminContainerVisibility();
    }

    function updateToggleButton(tableId, isActive) {
        var button = $('#toggle' + capitalizeFirstLetter(tableId));
        if (isActive) {
            button.removeClass('btn-danger').addClass('btn-success').text('Désactiver ' + button.data('label'));
        } else {
            button.removeClass('btn-success').addClass('btn-danger').text('Activer ' + button.data('label'));
        }
    }

    function updateAdminContainerVisibility() {
        var isVisible = $('#recentUsersTableContainer').is(':visible') ||
                        $('#recentSalesTableContainer').is(':visible') ||
                        $('#recentPrimeMembersTableContainer').is(':visible') ||
                        $('#bannedUsersTableContainer').is(':visible') ||
                        $('#banHistoryTableContainer').is(':visible') ||
                        $('#listeTableContainer').is(':visible') ||
                        $('#logsTableContainer').is(':visible');
        $('#adminContainer').toggle(isVisible);
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function openPatchNoteModal() {
        document.getElementById('patchNoteModal').style.display = 'block';
    }

    function closePatchNoteModal() {
        document.getElementById('patchNoteModal').style.display = 'none';
    }

    function openBanModal(userId) {
        document.getElementById('banUserId').value = userId;
        document.getElementById('banModal').style.display = 'block';
    }

    function closeBanModal() {
        document.getElementById('banModal').style.display = 'none';
    }

    function openUnbanPage(banId) {
        document.getElementById('unbanBanId').value = banId;
        document.getElementById('unbanForm').submit();
    }

    function openMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'block';
    }

    function closeMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'none';
    }

    function toggleSecondaryNavbar() {
        var navbar = document.querySelector('.secondary-navbar');
        navbar.classList.toggle('collapsed');
        Cookies.set('secondaryNavbarCollapsed', navbar.classList.contains('collapsed'));
    }

    function restoreOptionsMenuState() {
        var isCollapsed = Cookies.get('secondaryNavbarCollapsed') === 'true';
        var navbar = document.querySelector('.secondary-navbar');
        if (isCollapsed) {
            navbar.classList.add('collapsed');
        } else {
            navbar.classList.remove('collapsed');
        }
    }

    function editProduct(productId) {
        window.location.href = 'edit.php?id=' + productId;
    }

    function deleteProduct(productId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
            window.location.href = 'delete_product.php?id=' + productId;
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('IP copiée dans le presse-papiers: ' + text);
        }, function(err) {
            console.error('Could not copy text: ', err);
        });
    }
</script>
<form id="unbanForm" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="hidden" name="ban_id" id="unbanBanId">
    <button type="submit" name="unban_user"></button>
</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <footer>
        <p>&copy; 2024-2025 Valomazone. Tous droits réservés.</p>
    </footer>
</body>
</html>