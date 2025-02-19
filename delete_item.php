<?php
require_once 'bootstrap.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

$user_id = (int) $_SESSION['id'];
$cart_id = isset($_POST['cart_id']) ? (int) $_POST['cart_id'] : 0;

if ($cart_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID du panier invalide']);
    exit();
}

try {
    $sql = 'DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id';
    $query = $db->prepare($sql);
    $query->execute([':cart_id' => $cart_id, ':user_id' => $user_id]);

    if ($query->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Article supprimé du panier avec succès']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Impossible de supprimer l’article ou article non trouvé']);
    }
} catch (PDOException $e) {
    error_log('Erreur lors de la suppression du panier : ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données']);
}
?>