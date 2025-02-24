<?php
session_start();

try {
    if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
        throw new Exception('Database configuration environment variables are not set.');
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME']
    );

    $db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
}
?>
