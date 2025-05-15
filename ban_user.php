<?php
session_start();

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
}

// Vérification de l'accès utilisateur
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'error' => 'Accès refusé. Vous devez être connecté.']);
    exit();
}

$id_perm = (int)$_SESSION['user']['id_perm'];
if (!in_array($id_perm, [2, 3, 4, 5])) {
    echo json_encode(['success' => false, 'error' => 'Accès refusé. Permissions insuffisantes.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data) || !isset($data['user_id']) || empty($data['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'ID utilisateur manquant ou données JSON invalides.']);
    exit();
}

$userId = (int)$data['user_id'];
$duration = isset($data['duration']) ? (int)$data['duration'] : 0;
$reason = $data['reason'] ?? '';

try {
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 1, ban_reason = :reason, ban_duration = :duration WHERE id = :id");
    $stmt->execute([
        'id' => $userId,
        'reason' => $reason,
        'duration' => $duration,
    ]);

    echo json_encode(['success' => true, 'message' => 'Utilisateur banni avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage()]);
}
?>
