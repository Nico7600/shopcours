<?php
include "connect.php";

session_start();

if (isset($_SESSION['id'])) {
    header("Location: /");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Requête invalide. Veuillez soumettre le formulaire.";
    header("Location: login.php");
    exit;
}

$uname = trim($_POST['uname']);
$pass = trim($_POST['pass']);

if (empty($uname)) {
    $_SESSION['error'] = "Le nom d'utilisateur est requis.";
    header("Location: login.php");
    exit;
}

if (empty($pass)) {
    $_SESSION['error'] = "Le mot de passe est requis.";
    header("Location: login.php");
    exit;
}

try {
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$uname]);

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($pass, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['fname'] = $user['fname'];
            header("Location: /");
            exit;
        } else {
            $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect.";
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "Une erreur s'est produite. Veuillez réessayer plus tard.";
    header("Location: login.php");
    exit;
}
?>
