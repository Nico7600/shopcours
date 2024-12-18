<?php
session_start();

$include_path = 'db_conn.php';
if (!file_exists($include_path)) {
    $_SESSION['error'] = "Erreur de connexion à la base de données.";
    header("Location: register.php");
    exit();
}

include $include_path;

// Check database connection
if ($conn->connect_error) {
    $_SESSION['error'] = "Erreur de connexion à la base de données: " . $conn->connect_error;
    header("Location: register.php");
    exit();
}

if (isset($_POST['fname']) && isset($_POST['uname']) && isset($_POST['pass']) && isset($_POST['cpass'])) {
    $fname = $_POST['fname'];
    $uname = $_POST['uname'];
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];

    if ($pass !== $cpass) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (fname, uname, pass) VALUES (?, ?, ?)");
    if ($stmt === false) {
        $_SESSION['error'] = "Erreur lors de la préparation de la requête: " . $conn->error;
        header("Location: register.php");
        exit();
    }
    $stmt->bind_param("sss", $fname, $uname, $hashed_pass);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Inscription réussie.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Erreur lors de l'inscription: " . $stmt->error;
        header("Location: register.php");
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = "Tous les champs sont obligatoires.";
    header("Location: register.php");
}
?>