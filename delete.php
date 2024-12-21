<?php
require_once 'bootstrap.php'; // Gère sessions, connexion et variables d'environnement

if (isset($_GET['id']) && !empty($_GET['id'])) {
    try {
        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['erreur'] = "Identifiant de produit invalide.";
            header('Location: index.php');
            exit();
        }

        $sql = 'DELETE FROM liste WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            $_SESSION['message'] = "Produit supprimé avec succès.";
        } else {
            $_SESSION['erreur'] = "Le produit n'existe pas ou a déjà été supprimé.";
        }
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du produit : ' . $e->getMessage());
        $_SESSION['erreur'] = "Une erreur est survenue lors de la suppression du produit.";
    }

    header('Location: index.php');
    exit();
} else {
    $_SESSION['erreur'] = "URL invalide.";
    header('Location: index.php');
    exit();
}
?>