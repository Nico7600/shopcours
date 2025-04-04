<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

require_once 'connect.php';

$userName = null;
$isPrime = false;

$sql = 'SELECT fname, username, is_prime FROM users WHERE id = :id';
$query = $db->prepare($sql);
$query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $userName = $user['fname'];
    $isPrime = (bool)$user['is_prime'];
} else {
    header('Location: logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    if (!empty($newUsername)) {
        $sql = 'UPDATE users SET username = :username WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':username', $newUsername, PDO::PARAM_STR);
        $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
        $query->execute();
        $user['username'] = $newUsername;
    }
}

require_once('close.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #6c757d;
            color: #6c757d;
        }
        .card-header {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #ffffff;
            text-align: center;
        }
        .card-body {
            color: #343a40;
        }
        h3 {
            font-size: 1.75rem;
            font-weight: 700;
        }
        p, label {
            font-size: 1rem;
            font-weight: 400;
        }
        .badge {
            font-size: 0.875rem;
            font-weight: 700;
        }
        .btn {
            font-size: 1rem;
            font-weight: 700;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Profil utilisateur</h3>
                    </div>
                    <div class="card-body">
                        <p class="mb-4">
                            <i class="fas fa-user text-primary"></i> <strong>Nom complet :</strong> <?= htmlspecialchars($userName); ?>
                        </p>
                        <form method="post">
                            <div class="form-group">
                                <label for="username">
                                    <i class="fas fa-user-edit text-warning"></i> <strong>Nom d'utilisateur :</strong>
                                </label>
                                <input type="text" id="username" name="username" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </form>
                        <p class="mt-4">
                            <i class="fas fa-star text-success"></i> <strong>Abonnement Prime :</strong>
                            <?php if ($isPrime): ?>
                                <span class="badge badge-success">Oui</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Non</span>
                            <?php endif; ?>
                        </p>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-block mt-3">
                                <i class="fas fa-save text-white"></i> Mettre à jour
                            </button>
                            <a href="logout.php" class="btn btn-danger btn-block mt-3">
                                <i class="fas fa-sign-out-alt text-white"></i> Se déconnecter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
