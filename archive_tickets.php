<?php
session_start();
include 'db_connection.php';

if (!isset($db) || !$db) {
    http_response_code(500);
    echo "Erreur : La connexion à la base de données n'a pas été établie.";
    exit;
}

if (!isset($_SESSION['user']['id'])) {
    http_response_code(403);
    echo "Erreur : Vous devez être connecté pour effectuer cette action.";
    exit;
}

$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'fr');
$available_languages = ['en', 'nl', 'fr'];
if (!in_array($lang, $available_languages)) {
    $lang = 'fr';
}

$translations = [
    'fr' => [
        'archived_tickets' => 'Tickets Archivés',
        'no_tickets' => 'Aucun ticket archivé trouvé.',
        'created_at' => 'Créé le',
    ],
    'en' => [
        'archived_tickets' => 'Archived Tickets',
        'no_tickets' => 'No archived tickets found.',
        'created_at' => 'Created at',
    ],
    'nl' => [
        'archived_tickets' => 'Gearchiveerde Tickets',
        'no_tickets' => 'Geen gearchiveerde tickets gevonden.',
        'created_at' => 'Aangemaakt op',
    ],
];

$text_ui = $translations[$lang];
$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $ticketId = isset($input['id']) ? (int)$input['id'] : null;

    if (!$ticketId) {
        http_response_code(400);
        exit("ID de ticket manquant.");
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT * FROM ticket WHERE id = :id AND creator = :creator");
        $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
        $stmt->bindParam(':creator', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ticket) {
            $archiveStmt = $db->prepare("INSERT INTO archive_ticket (id, ticket_name, message, created_at, creator) VALUES (:id, :ticket_name, :message, :created_at, :creator)");
            $archiveStmt->bindParam(':id', $ticket['id'], PDO::PARAM_INT);
            $archiveStmt->bindParam(':ticket_name', $ticket['ticket_name'], PDO::PARAM_STR);
            $archiveStmt->bindParam(':message', $ticket['message'], PDO::PARAM_STR);
            $archiveStmt->bindParam(':created_at', $ticket['created_at'], PDO::PARAM_STR);
            $archiveStmt->bindParam(':creator', $ticket['creator'], PDO::PARAM_INT);
            $archiveStmt->execute();

            $deleteStmt = $db->prepare("DELETE FROM ticket WHERE id = :id");
            $deleteStmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
            $deleteStmt->execute();

            // Update user counts
            $updateUserStmt = $db->prepare("UPDATE users SET closed_ticket_count = closed_ticket_count + 1, open_ticket_count = open_ticket_count - 1 WHERE id = :userId");
            $updateUserStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $updateUserStmt->execute();

            $db->commit();
            http_response_code(200);
            exit("Ticket archived successfully");
        } else {
            $db->rollBack();
            http_response_code(404);
            exit("Ticket not found");
        }
    } catch (PDOException $e) {
        $db->rollBack();
        http_response_code(500);
        echo "Erreur PDO : " . $e->getMessage();
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        http_response_code(500);
        exit("Une erreur inattendue s'est produite.");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->prepare("SELECT * FROM archive_ticket WHERE creator = :creator");
        $stmt->bindParam(':creator', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $archivedTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        http_response_code(500);
        echo $e->getMessage();
        exit;
    }
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($text_ui['archived_tickets']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-orbitron { font-family: 'Orbitron', sans-serif; }
        .scrollable-container { max-height: calc(100vh - 100px); overflow-y: auto; }
        .ticket-container { min-height: 112px; display: flex; flex-direction: column; justify-content: center; position: relative; }
        .ticket-container h2 { font-size: 1.5rem; position: relative; text-align: left; margin: 0; }
        .tickets-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-900 text-gray-100 font-orbitron fade-in">
    <div class="container mx-auto flex-grow p-6 font-orbitron h-full">
        <h1 class="text-3xl font-bold text-center mb-6"><?= htmlspecialchars($text_ui['archived_tickets']) ?></h1>
        <div class="scrollable-container">
            <?php if (empty($archivedTickets)): ?>
                <p class="text-center text-gray-400"><?= htmlspecialchars($text_ui['no_tickets']) ?></p>
            <?php else: ?>
                <div class="tickets-grid">
                    <?php foreach ($archivedTickets as $ticket): ?>
                        <div class="ticket-container bg-gray-800 border border-gray-700 rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow font-orbitron relative">
                            <h2 class="text-2xl font-semibold text-cyan-400 font-bold text-left">
                                <?= htmlspecialchars($ticket['ticket_name']) ?>
                            </h2>
                            <p class="text-sm text-gray-500 absolute bottom-2 left-4 text-left">
                                <?= htmlspecialchars($text_ui['created_at']) ?>: <?= date('d/m/Y à H:i:s', strtotime($ticket['created_at'])) ?>
                            </p>
                            <?php if ($ticket['is_closed']): ?>
                                <p class="text-sm text-gray-400 absolute bottom-2 right-4 text-right">
                                    Fermé le: <?= date('d/m/Y à H:i:s', strtotime($ticket['closed_at'])) ?>
                                </p>
                            <?php endif; ?>
                            <div class="absolute inset-y-0 right-4 flex items-center space-x-4">
                                <a href="view_archive_ticket.php?id=<?= urlencode($ticket['id']) ?>" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
