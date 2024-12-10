<?php
// On démarre une session
session_start();

if(isset($_GET['id']) && !empty($_GET['id'])){
    require_once('connect.php');
    $db->exec("SET NAMES 'utf8mb4'");

    $id = strip_tags($_GET['id']);

    $sql = 'DELETE FROM `liste` WHERE `id`=:id;';
    $query = $db->prepare($sql);

    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    $_SESSION['message'] = "Produit supprimé";
    require_once('close.php');

    header('Location: index.php');
}else{
    $_SESSION['erreur'] = "URL invalide";
    header('Location: index.php');
}
?>
