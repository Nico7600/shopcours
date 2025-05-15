<?php
require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Ajout des traductions pour les boutons
$languages = ['en' => 'English', 'fr' => 'Français', 'nl' => 'Nederlands'];
$selected_lang = $_GET['lang'] ?? 'fr';
if (!array_key_exists($selected_lang, $languages)) {
    $selected_lang = 'fr';
}

$translations = [
    'fr' => [
        'yes' => 'Oui',
        'no' => 'Non',
        'unauthorized_column' => 'Colonne non autorisée.',
    ],
    'en' => [
        'yes' => 'Yes',
        'no' => 'No',
        'unauthorized_column' => 'Unauthorized column.',
    ],
    'nl' => [
        'yes' => 'Ja',
        'no' => 'Nee',
        'unauthorized_column' => 'Niet-geautoriseerde kolom.',
    ],
];

$trans = $translations[$selected_lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_perm = (int)$_POST['id_perm'];
    $column = $_POST['column'];

    // Vérification de la colonne pour éviter les injections SQL
    $allowedColumns = ['can_reply_ticket', 'can_ban_permanently', 'can_ban_temporarily', 'can_post_patchnotes', 'can_manage_users', 'can_view_reports', 'can_edit_roles', 'can_delete_tickets'];
    if (!in_array($column, $allowedColumns)) {
        die($trans['unauthorized_column']);
    }

    // Récupération de la valeur actuelle
    $stmt = $pdo->prepare("SELECT {$column} FROM permissions WHERE id_perm = :id_perm");
    $stmt->execute(['id_perm' => $id_perm]);
    $currentValue = $stmt->fetchColumn();

    // Inversion de la valeur (0 -> 1, 1 -> 0)
    $newValue = $currentValue ? 0 : 1;

    // Mise à jour de la valeur
    $stmt = $pdo->prepare("UPDATE permissions SET {$column} = :new_value WHERE id_perm = :id_perm");
    $stmt->execute(['new_value' => $newValue, 'id_perm' => $id_perm]);

    // Redirection vers la page admin
    header('Location: admin.php');
    exit();
}
?>
