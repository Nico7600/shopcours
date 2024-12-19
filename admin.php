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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ban_user'])) {
    if (isset($_POST['reason']) && isset($_POST['duration'])) {
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

    $sql = 'DELETE FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    // Update the users table to set banned to 0
    $sql = 'UPDATE users SET banned = 0 WHERE id = (SELECT user_id FROM bans WHERE id = :ban_id)';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    header('Location: admin.php');
    exit;
}

// Remove chat message handling

// Remove chat message handling

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
            justify-content: flex-start;
            align-items: center;
            min-height: 100vh; /* Change from height to min-height */
            margin: 0;
            padding-top: 80px;
        }
        .navbar {
            width: 100%;
            background-color: #343a40;
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
            background-color: #ffffff;
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
            background-color: #343a40;
            color: #ffffff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .table-title {
            text-align: center;
            margin-top: 40px;
            font-size: 2rem;
            font-weight: 600;
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
            background-color: #fefefe;
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
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="index.php">Valomazone Admin</a>
        <div class="menu">
            <a href="add.php">Ajouter produit</a>
            <form method="post" style="display:inline;">
                <button type="submit" name="toggle_maintenance" class="btn <?php echo file_exists('maintenance.flag') ? 'btn-maintenance-on' : 'btn-maintenance-off'; ?>">
                    <?php echo file_exists('maintenance.flag') ? 'Désactiver la maintenance' : 'Activer la maintenance'; ?>
                </button>
            </form>
        </div>
    </div>
    <h1>Bienvenue sur la page admin</h1>
    <div class="admin-container">
        <div class="user-list">
            <h2 class="table-title">Derniers inscrits </h2>
            <table class="table">
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
                                            <button class="btn btn-success" onclick="openUnbanPage(<?php echo $ban['ban_id']; ?>)">
                                                <i class="fa-solid fa-gavel" style="color: white;"></i> Unban
                                            </button>
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
                            <td class="date"><?php echo htmlspecialchars(date('d/m/Y à H:i:s', strtotime($sale['order_date']))); ?></td>
                            <td><?php echo htmlspecialchars($sale['total_amount']); ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="prime-members">
            <h2 class="table-title">Derniers membres Prime</h2>
            <table class="table">
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
        <div class="ban-user">
            <h2 class="table-title">Liste des ban en cours</h2>
            <table class="table">
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
        <div class="ban-history">
            <h2 class="table-title">Historique des bans</h2>
            <table class="table">
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
        <!-- Ban Modal -->
        <div id="banModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeBanModal()">&times;</span>
                <h2>Ban User</h2>
                <form method="post" name="ban_user">
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
    <script>
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

        // Prevent form submission from scrolling to the top
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                const formData = new FormData(form);
                fetch(form.action, {
                    method: form.method,
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Une erreur est survenue.');
                    }
                }).catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue.');
                });
            });
        });

        // Handle ban form submission separately
        document.querySelector('form[name="ban_user"]').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: this.method,
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Une erreur est survenue.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Une erreur est survenue.');
            });
        });
    </script>
</body>
</html>