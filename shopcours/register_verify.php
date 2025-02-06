<?php
require_once('bootstrap.php');

// Vérifier si les champs nécessaires sont présents dans le formulaire
if (isset($_POST['fname']) && isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['cpass'])) {
    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    // Vérification de la correspondance des mots de passe
    if ($pass !== $cpass) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: register.php");
        exit();
    }

    // Hachage du mot de passe
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    try {
        // Préparer et exécuter la requête d'insertion
        $sql = "INSERT INTO users (fname, uname, pass) VALUES (:fname, :uname, :pass)";
        $query = $db->prepare($sql);
        $query->bindValue(':fname', $fname, PDO::PARAM_STR);
        $query->bindValue(':uname', $uname, PDO::PARAM_STR);
        $query->bindValue(':pass', $hashed_pass, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['success'] = "Inscription réussie.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de l'inscription: " . $query->errorInfo()[2];
            header("Location: register.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de connexion à la base de données: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Tous les champs sont obligatoires.";
    header("Location: register.php");
    exit();
}
?>