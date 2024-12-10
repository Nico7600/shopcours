<?php
// On démarre une session
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// On inclut la connexion à la base
require_once 'connect.php';

$userName = null;
$isPrime = false;

// Récupération des informations de l'utilisateur
$sql = 'SELECT fname, username, is_prime FROM users WHERE id = :id';
$query = $db->prepare($sql);
$query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
$query->execute();
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $userName = $user['fname'];
    $isPrime = (bool)$user['is_prime'];
} else {
    header('Location: logout.php'); // Rediriger si l'utilisateur n'existe pas
    exit;
}

require_once('close.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Contenu principal -->
    <main class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Profil utilisateur</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Nom complet :</strong> <?= htmlspecialchars($userName); ?></p>
                        <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']); ?></p>
                        <p>
                            <strong>Abonnement Prime :</strong>
                            <?php if ($isPrime): ?>
                                <span class="badge badge-success">Oui</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Non</span>
                            <?php endif; ?>
                        </p>
                        <div class="text-center mt-4">
                            <a href="edit_profile.php" class="btn btn-warning">Modifier le profil</a>
                            <a href="logout.php" class="btn btn-danger">Se déconnecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
