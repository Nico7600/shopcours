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
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo "ID du ticket manquant.";
        exit;
    }

    $ticketId = (int)$data['id'];

    // Vérifiez si le ticket existe
    $stmt = $db->prepare("SELECT * FROM ticket WHERE id = :id AND is_close = 0");
    $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        http_response_code(404);
        echo "Ticket introuvable ou déjà archivé.";
        exit;
    }

    // Insérer dans la table archive_ticket
    $stmt = $db->prepare("INSERT INTO archive_ticket (creator, ticket_name, message, created_at, closed_by) VALUES (:creator, :ticket_name, :message, :created_at, :closed_by)");
    $stmt->bindParam(':creator', $ticket['creator'], PDO::PARAM_INT);
    $stmt->bindParam(':ticket_name', $ticket['ticket_name'], PDO::PARAM_STR);
    $stmt->bindParam(':message', $ticket['message'], PDO::PARAM_STR);
    $stmt->bindParam(':created_at', $ticket['created_at'], PDO::PARAM_STR);
    $stmt->bindParam(':closed_by', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $db->prepare("
        UPDATE users
        SET 
            open_ticket_count = IF(open_ticket_count IS NULL OR open_ticket_count < 1, 0, open_ticket_count - 1), 
            closed_ticket_count = IF(closed_ticket_count IS NULL, 1, closed_ticket_count + 1) 
        WHERE id = :creatorId
    ");
    $stmt->bindParam(':creatorId', $ticket['creator'], PDO::PARAM_INT);
    $stmt->execute();

    // Supprimer le ticket de la table ticket
    $stmt = $db->prepare("DELETE FROM ticket WHERE id = :id");
    $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
    $stmt->execute();

    http_response_code(200);
    echo "Ticket archivé avec succès.";
    exit;
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
