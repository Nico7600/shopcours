<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
        throw new Exception('Les variables d\'environnement pour la base de données ne sont pas définies.');
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME']
    );

    $db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données (PDO) : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
} catch (Exception $e) {
    error_log('Erreur de connexion à la base de données (Exception) : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
}
?>