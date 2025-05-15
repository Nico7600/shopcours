<?php
session_start();

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if (
    !isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])
    || empty($_ENV['DB_HOST']) || empty($_ENV['DB_NAME']) || empty($_ENV['DB_USER']) || empty($_ENV['DB_PASSWORD'])
) {
    die('Erreur : Variables d\'environnement de connexion à la base de données manquantes ou incomplètes.');
}

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];

$userStmt = $pdo->prepare("
    SELECT users.name, role.name AS role_name
    FROM users
    LEFT JOIN role ON users.id_perm = role.id
    WHERE users.id = :id
");
$userStmt->execute(['id' => $userId]);
$userInfo = $userStmt->fetch();
if (!$userInfo) {
    die('Erreur : Utilisateur introuvable en base de données.');
}
$staffName = $userInfo['name'] ?? 'Staff';
$staffGrade = $userInfo['role_name'] ?? 'Staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticketId = (int)$_POST['ticket_id'];

    $checkTicketStmt = $pdo->prepare("SELECT id FROM ticket WHERE id = :ticket_id");
    $checkTicketStmt->bindParam(":ticket_id", $ticketId, PDO::PARAM_INT);
    $checkTicketStmt->execute();
    $ticketExists = $checkTicketStmt->fetch();
    if (!$ticketExists) {
        http_response_code(400);
        echo "Ticket inexistant";
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE ticket SET claimed_by = :user_id, status = 'taken', has_new_staff_reply = 0 WHERE id = :ticket_id");
        $stmt->bindParam(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam(":ticket_id", $ticketId, PDO::PARAM_INT);
        $stmt->execute();

        $autoMsg = "Ce ticket sera traité par {$staffName}" . ($staffGrade ? " ({$staffGrade})" : "") . " sur le site. Merci de patienter.";
        $msgStmt = $pdo->prepare("INSERT INTO ticket_message (ticket_id, creator, message) VALUES (:ticket_id, :creator, :message)");
        $msgStmt->bindParam(":ticket_id", $ticketId, PDO::PARAM_INT);
        $msgStmt->bindParam(":creator", $userId, PDO::PARAM_INT);
        $msgStmt->bindParam(":message", $autoMsg, PDO::PARAM_STR);
        $msgStmt->execute();
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Erreur SQL";
        exit;
    } catch (Throwable $e) {
        http_response_code(500);
        echo "Erreur PHP";
        exit;
    }
}

header("Location: admin.php");
exit;
