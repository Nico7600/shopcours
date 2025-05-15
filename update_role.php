<?php
session_start();

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
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
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

if (!is_array($data) || !isset($data['user_id']) || !isset($data['id_perm'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides.']);
    exit();
}

$userId = (int)$data['user_id'];
$newRole = (int)$data['id_perm'];

try {
    $stmt = $pdo->prepare("UPDATE users SET id_perm = :id_perm WHERE id = :id");
    $stmt->execute([
        'id_perm' => $newRole,
        'id' => $userId,
    ]);

    echo json_encode(['success' => true, 'message' => 'Rôle mis à jour avec succès.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de la mise à jour du rôle : ' . $e->getMessage()]);
}
?>
