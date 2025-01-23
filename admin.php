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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_maintenance'])) {
    $maintenance = file_exists('maintenance.flag');
    if ($maintenance) {
        unlink('maintenance.flag');
    } else {
        $duration = $_POST['maintenance_duration'];
        $endTime = time() + ($duration * 1);
        file_put_contents('maintenance.flag', $endTime);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ban_user'])) {
    if (!empty($_POST['reason']) && !empty($_POST['duration'])) {
        $userId = $_POST['user_id'];
        $reason = $_POST['reason'];
        $duration = $_POST['duration'];
        $banEndDate = date('Y-m-d H:i:s', strtotime("+$duration days"));

        $sql = 'INSERT INTO bans (user_id, reason, ban_end_date, banned_by) VALUES (:user_id, :reason, :ban_end_date, :banned_by)';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->bindValue(':reason', $reason, PDO::PARAM_STR);
        $query->bindValue(':ban_end_date', $banEndDate, PDO::PARAM_STR);
        $query->bindValue(':banned_by', $_SESSION['id'], PDO::PARAM_INT);
        $query->execute();

        // Update the users table to set banned to 1
        $sql = 'UPDATE users SET banned = 1 WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();

        // Update the $recentUsers array to reflect the change
        foreach ($recentUsers as &$user) {
            if ($user['id'] == $userId) {
                $user['banned'] = 1;
                break;
            }
        }

        header('Location: admin.php');
        exit;
    } else {
        $_SESSION['erreur'] = "Reason and duration are required.";
        header('Location: admin.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unban_user'])) {
    $banId = $_POST['ban_id'];

    // Move the ban to the ban_history table before deleting
    $sql = 'INSERT INTO ban_history (user_id, reason, ban_end_date, banned_by)
            SELECT user_id, reason, ban_end_date, banned_by FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    // Get the user_id before deleting the ban
    $sql = 'SELECT user_id FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();
    $userId = $query->fetchColumn();

    $sql = 'DELETE FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    // Update the users table to set banned to 0
    $sql = 'UPDATE users SET banned = 0 WHERE id = :user_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->execute();

    // Update the $recentUsers array to reflect the change
    foreach ($recentUsers as &$user) {
        if ($user['id'] == $userId) {
            $user['banned'] = 0;
            break;
        }
    }

    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $message = $_POST['update'];
    $userId = $_SESSION['id'];
    $sql = 'INSERT INTO updates (user_id, message, created_at) VALUES (:user_id, :message, NOW())';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':message', $message, PDO::PARAM_STR);
    $query->execute();
    header('Location: admin.php');
    exit;
}

// Fetch recent registered users
$sql = 'SELECT id, fname, username, is_prime, admin, date, banned, last_ip FROM users ORDER BY id DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentUsers = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent sales
$sql = 'SELECT id, order_date, total_amount FROM orders ORDER BY order_date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentSales = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch banned users with admin usernames
$sql = 'SELECT bans.id AS ban_id, bans.user_id, users.username, bans.reason, bans.ban_end_date, admin.username AS banned_by_username 
        FROM bans 
        JOIN users ON bans.user_id = users.id 
        JOIN users AS admin ON bans.banned_by = admin.id';
$query = $db->prepare($sql);
$query->execute();
$bannedUsers = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch ban history
$sql = 'SELECT ban_history.id AS ban_id, ban_history.user_id, users.username, ban_history.reason, ban_history.ban_end_date, admin.username AS banned_by_username 
        FROM ban_history 
        JOIN users ON ban_history.user_id = users.id 
        JOIN users AS admin ON ban_history.banned_by = admin.id';
$query = $db->prepare($sql);
$query->execute();
$banHistory = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent prime members based on purchases
$sql = 'SELECT users.id, users.fname, users.username, users.date, users.last_ip, orders.total_amount 
        FROM users 
        JOIN orders ON users.id = orders.user_id 
        WHERE users.is_prime = 1 
        ORDER BY orders.order_date DESC LIMIT 10';
$query = $db->prepare($sql);
$query->execute();
$recentPrimeMembers = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch liste data
$sql = 'SELECT id, produit, prix, nombre, description, badge, promo FROM liste ORDER BY id DESC';
$query = $db->prepare($sql);
$query->execute();
$listeItems = $query->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.css">
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
        }
        .navbar a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2rem;
            margin-right: 20px;
        }
        .navbar .menu {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .navbar .menu a, .navbar .menu form {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 0 10px;
            text-align: center;
        }
        .admin-container {
            text-align: center;
            padding: 30px;
            background-color: #495057;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            width: 100%;
            margin-top: 80px;
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
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Valomazone Admin</a>
        <div class="menu">
            <a href="add.php">Ajouter produit</a>
            <form method="post" style="display:inline;">
                <button type="button" class="btn <?php echo file_exists('maintenance.flag') ? 'btn-maintenance-on' : 'btn-maintenance-off'; ?>" onclick="openMaintenanceModal()">
                    <?php echo file_exists('maintenance.flag') ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
                </button>
            </form>
            <button class="btn btn-primary" onclick="openPatchNoteModal()">Ajouter Patch Note</button>
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
        </div>
    </div>
    <div class="admin-container">
        <div class="user-list">
            <div class="table-title">
                <h2>Derniers inscrits</h2>
            </div>
            <div id="recentUsersTableContainer">
                <table id="recentUsersTable" class="table table-striped table-dark" style="width:100%">
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
                                <td><?php echo htmlspecialchars($user['last_ip']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="is_prime" value="<?php echo $user['is_prime']; ?>">
                                        <button type="submit" name="toggle_prime" class="btn <?php echo $user['is_prime'] ? 'btn-toggle-on' : 'btn-toggle-off'; ?>">
                                            <?php echo $user['is_prime'] ? '<i class="fa fa-crown" style="color: gold;"></i> Prime On' : '<i class="fa fa-crown" style="color: white;"></i> Prime Off'; ?>
                                        </button>
                                    </form>
                                    <form method="post" style="display:inline;">
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
            <div id="recentSalesTableContainer">
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
            <div id="recentPrimeMembersTableContainer">
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
                                <td><?php echo htmlspecialchars($primeMember['last_ip']); ?></td>
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
            <div id="bannedUsersTableContainer">
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
            <div id="banHistoryTableContainer">
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
            <div id="listeTableContainer">
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
                <textarea name="update" required class="form-control mb-3"></textarea>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize tables based on cookie visibility
        restoreTableVisibility('recentUsersTable');
        restoreTableVisibility('recentSalesTable');
        restoreTableVisibility('recentPrimeMembersTable');
        restoreTableVisibility('bannedUsersTable');
        restoreTableVisibility('banHistoryTable');
        restoreTableVisibility('listeTable');
    });

    function initializeDataTable(tableId) {
        if (!$.fn.dataTable.isDataTable('#' + tableId)) {
            $('#' + tableId).DataTable({
                language: {
                    lengthMenu: "Afficher _MENU_ entrées par page",
                    search: "Rechercher:",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées (filtré de _MAX_ entrées au total)",
                    emptyTable: "Pas de résultat trouver",
                    zeroRecords: "Pas de résultat trouver"
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
    }

    function updateToggleButton(tableId, isActive) {
        var button = $('#toggle' + capitalizeFirstLetter(tableId));
        if (isActive) {
            button.removeClass('btn-danger').addClass('btn-success').text('Désactiver ' + button.data('label'));
        } else {
            button.removeClass('btn-success').addClass('btn-danger').text('Activer ' + button.data('label'));
        }
    }

    function restoreTableVisibility(tableId) {
        var isVisible = Cookies.get(tableId + 'Visible') === 'true';
        if (isVisible) {
            $('#' + tableId + 'Container').show();
            $('#' + tableId + 'Container').prev('.table-title').show();
            initializeDataTable(tableId);
        } else {
            $('#' + tableId + 'Container').hide();
            $('#' + tableId + 'Container').prev('.table-title').hide();
            updateToggleButton(tableId, false);
        }
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
        window.location.href = 'confirm_unban.php?ban_id=' + banId;
    }

    function openMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'block';
    }

    function closeMaintenanceModal() {
        document.getElementById('maintenanceModal').style.display = 'none';
    }
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>