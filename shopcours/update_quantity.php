<?php
require_once 'bootstrap.php'; // Gère sessions, connexion et variables d'environnement

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

$user_id = (int) $_SESSION['id'];
$cart_id = isset($_POST['cart_id']) ? (int) $_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

if ($cart_id <= 0 || $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'ID du panier ou quantité invalide']);
    exit();
}

try {
    $sql = 'UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id';
    $query = $db->prepare($sql);
    $query->execute([
        ':quantity' => $quantity,
        ':cart_id' => $cart_id,
        ':user_id' => $user_id
    ]);

    if ($query->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Panier mis à jour avec succès']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Mise à jour du panier échouée ou aucune modification apportée']);
    }
} catch (PDOException $e) {
    error_log('Erreur lors de la mise à jour du panier : ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur de base de données']);
}
?>
