<?php
include "connect.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Requête invalide. Veuillez soumettre le formulaire.";
    header("Location: register.php");
    exit;
}

$fname = trim($_POST['fname']);
$uname = trim($_POST['uname']);
$pass = trim($_POST['pass']);
$cpass = trim($_POST['cpass']);

$_SESSION['fname'] = $fname;
$_SESSION['uname'] = $uname;

if (empty($fname)) {
    $_SESSION['error'] = "Le prénom est requis.";
    header("Location: register.php");
    exit;
} elseif (empty($uname)) {
    $_SESSION['error'] = "Le nom d'utilisateur est requis.";
    header("Location: register.php");
    exit;
} elseif (empty($pass)) {
    $_SESSION['error'] = "Le mot de passe est requis.";
    header("Location: register.php");
    exit;
} elseif ($pass !== $cpass) {
    $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
    header("Location: register.php");
    exit;
}

try {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$uname]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Le nom d'utilisateur existe déjà. Veuillez en choisir un autre.";
        header("Location: register.php");
        exit;
    }

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (fname, username, password) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute([$fname, $uname, $hashed_pass]);

    unset($_SESSION['fname']);
    unset($_SESSION['uname']);

    $_SESSION['success'] = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "Une erreur s'est produite. Veuillez réessayer plus tard.";
    header("Location: register.php");
    exit;
}
?>
