<?php
session_start();

$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'fr');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $conn = new mysqli('nicolavdeprets.mysql.db', 'nicolavdeprets', 'Rex220405', 'nicolavdeprets');
        if ($conn->connect_error) {
            header("Location: index.php?lang=" . urlencode($lang) . "&error=" . urlencode("Erreur de connexion à la base de données : " . $conn->connect_error));
            exit;
        }

        $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email = ?");
        if (!$stmt) {
            header("Location: index.php?lang=" . urlencode($lang) . "&error=" . urlencode("Erreur de préparation de la requête : " . $conn->error));
            exit;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword, $userName);
        $stmt->fetch();

        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            // Connexion automatique si les identifiants sont corrects
            $_SESSION['user'] = [
                'id'    => $userId,
                'name'  => $userName,
                'email' => $email,
            ];
            session_write_close();

            $stmt->close();
            $conn->close();
            header("Location: index.php?lang=" . urlencode($lang) . "&login=success");
            exit;
        } else {
            $stmt->close();
            $conn->close();
            header("Location: index.php?lang=" . urlencode($lang) . "&error=" . urlencode("Email ou mot de passe incorrect."));
            exit;
        }
    } else {
        header("Location: index.php?lang=" . urlencode($lang) . "&error=" . urlencode("Veuillez remplir tous les champs."));
        exit;
    }
} else {
    header("Location: index.php?lang=" . urlencode($lang));
    exit;
}
