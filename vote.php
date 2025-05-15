<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['type'], $data['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

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
    echo json_encode(['success' => false]);
    exit;
}

$id = intval($data['id']);
if ($data['type'] === 'up') {
    $pdo->prepare("UPDATE patch_note SET up_vote = up_vote + 1 WHERE id = ?")->execute([$id]);
} elseif ($data['type'] === 'down') {
    $pdo->prepare("UPDATE patch_note SET down_vote = down_vote + 1 WHERE id = ?")->execute([$id]);
} else {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("SELECT up_vote, down_vote FROM patch_note WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
$score = intval($row['up_vote']) - intval($row['down_vote']);

echo json_encode(['success' => true, 'score' => $score]);
