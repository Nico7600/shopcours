<?php
session_start();
require_once('connect.php');

if (isset($_POST['product_id'], $_POST['rating'], $_POST['comment'], $_SESSION['id'])) {
    $productId = strip_tags($_POST['product_id']);
    $rating = strip_tags($_POST['rating']);
    $comment = strip_tags($_POST['comment']);
    $userId = $_SESSION['id'];

    // Fetch the username of the logged-in user
    $sqlUser = 'SELECT username FROM users WHERE id = :id';
    $queryUser = $db->prepare($sqlUser);
    $queryUser->bindValue(':id', $userId, PDO::PARAM_INT);
    $queryUser->execute();
    $user = $queryUser->fetch();

    if ($user) {
        $username = $user['username'];

        $sql = 'INSERT INTO comments (product_id, user_id, username, rating, comment, created_at) VALUES (:product_id, :user_id, :username, :rating, :comment, NOW())';
        $query = $db->prepare($sql);
        $query->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->bindValue(':rating', $rating, PDO::PARAM_INT);
        $query->bindValue(':comment', $comment, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['message'] = "Commentaire ajouté avec succès";
    } else {
        $_SESSION['erreur'] = "Utilisateur non trouvé";
    }
} else {
    $_SESSION['erreur'] = "Erreur lors de l'ajout du commentaire";
}

header('Location: details.php?id=' . $productId);
exit;
?>