
<?php
session_start();
require 'config.php';


if (!isset($_SESSION['user_id'])) {
    die('Accès refusé. Veuillez vous connecter.');
}

$user_id = $_SESSION['user_id'];
$query = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$query->execute([$user_id]);
$user = $query->fetch();

if (!$user) {
    die('Utilisateur non trouvé.');
}

if ($user['role'] !== 'admin') {
    die('Accès refusé. Vous n\'avez pas les permissions nécessaires pour supprimer des articles.');
}

if (!isset($_GET['id'])) {
    die('Aucun identifiant d\'article spécifié.');
}
$article_id = $_GET['id'];


$query = $pdo->prepare("DELETE FROM articles WHERE id = ?");
$query->execute([$article_id]);

echo 'Article supprimé avec succès.';
?>