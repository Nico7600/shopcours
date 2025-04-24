<?php
ob_start();
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Connexion à la base de données
$host = $_ENV['DB_HOST'];
$db_user = $_ENV['DB_USER'];
$db_password = $_ENV['DB_PASSWORD'];
$db_name = $_ENV['DB_NAME'];

$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $password = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);

    // Traitement de l'upload de la photo de profil
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $destination = $upload_dir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination)) {
            $profile_picture = $conn->real_escape_string(basename($_FILES['profile_picture']['name']));
        }
    }

    // Insertion en base de données
    $sql = "INSERT INTO users (name, email, phone, password, is_admin, ticket_count, open_ticket_count, closed_ticket_count, profile_picture, created_at, updated_at) 
            VALUES ('$name', '$email', '$phone', '$password', 0, 0, 0, 0, '$profile_picture', NOW(), NOW())";

    if ($conn->query($sql) === TRUE) {
        // Envoi de l'email de confirmation
        $subject = "Registration Successful";
        $message = "Hello $name,\n\nThank you for registering. Your account has been successfully created.";
        $headers = "From: no-reply@example.com";
        mail($email, $subject, $message, $headers);

        header("Location: index.php?registration=success");
        exit;
    } else {
        header("Location: index.php?registration=error&message=" . urlencode($conn->error));
        exit;
    }
}

$conn->close();
ob_end_flush();
?>