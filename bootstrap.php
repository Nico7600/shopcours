<?php
use Dotenv\Dotenv;

if (!defined('BOOTSTRAP_LOADED')) {
    define('BOOTSTRAP_LOADED', true);

    require_once 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER']) || !isset($_ENV['DB_PASSWORD'])) {
        throw new Exception("Les variables d'environnement pour la base de données ne sont pas correctement définies.");
    }
    
    require_once 'session.php';

    require_once 'connect.php';
}
?>