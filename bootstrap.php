<?php
use Dotenv\Dotenv;

// Assurez-vous que ce fichier ne soit inclus qu'une seule fois
if (!defined('BOOTSTRAP_LOADED')) {
    define('BOOTSTRAP_LOADED', true);

    // Charger les dépendances via Composer
    require_once 'vendor/autoload.php';

    // Charger les variables d'environnement
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Vérifier les variables nécessaires
    if (empty($_ENV['DB_HOST']) || empty($_ENV['DB_NAME']) || empty($_ENV['DB_USER']) || empty($_ENV['DB_PASSWORD'])) {
        throw new Exception("Les variables d'environnement pour la base de données ne sont pas correctement définies.");
    }

    // Inclure la gestion des sessions
    require_once 'session.php';

    // Inclure la connexion à la base de données
    require_once 'connect.php';
}
?>