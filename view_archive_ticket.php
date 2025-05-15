<?php
session_start();
include 'db_connection.php';

if (!isset($db) || !$db) {
    die("Erreur : La connexion à la base de données n'a pas été établie.");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'fr');
$available_languages = ['en', 'nl', 'fr'];
if (!in_array($lang, $available_languages)) {
    $lang = 'fr';
}

$translations = [
    'fr' => [
        'ticket_details' => 'Aperçu de l\'Archive',
        'title' => 'Titre',
        'message' => 'Message',
        'created_at' => 'Créé le',
        'closed_at' => 'Fermé le',
        'closed_by' => 'Fermé par',
        'back' => 'Retour',
    ],
    'en' => [
        'ticket_details' => 'Archive Overview',
        'title' => 'Title',
        'message' => 'Message',
        'created_at' => 'Created At',
        'closed_at' => 'Closed At',
        'closed_by' => 'Closed By',
        'back' => 'Back',
    ],
    'nl' => [
        'ticket_details' => 'Overzicht van het Archief',
        'title' => 'Titel',
        'message' => 'Bericht',
        'created_at' => 'Aangemaakt op',
        'closed_at' => 'Gesloten op',
        'closed_by' => 'Gesloten door',
        'back' => 'Terug',
    ],
];

$text_ui = $translations[$lang];

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$ticketId = $_GET['id'] ?? null;
if (!$ticketId || !is_numeric($ticketId)) {
    die("Erreur : ID de ticket invalide.");
}

try {
    $stmt = $db->prepare("SELECT archive_ticket.*, users.name AS closed_by_name 
                          FROM archive_ticket 
                          LEFT JOIN users ON archive_ticket.closed_by = users.id 
                          WHERE archive_ticket.id = :id");
    $stmt->bindParam(':id', $ticketId, PDO::PARAM_INT);
    $stmt->execute();
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        die("Erreur : Ticket archivé introuvable.");
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}

include 'header.php';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($text_ui['ticket_details']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .ticket-container {
            min-height: 112px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            background: linear-gradient(145deg, #1f2937, #374151);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .btn {
            transition: all 0.3s ease-in-out;
        }
        .btn:hover {
            transform: scale(1.05);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in {
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
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
        <h1 class="text-4xl font-bold text-center mb-8 text-cyan-400"><?= htmlspecialchars($text_ui['ticket_details']) ?></h1>
        <div class="ticket-container bg-gray-800 border border-gray-700 rounded-lg p-8 shadow-lg">
            <p class="text-lg"><strong class="text-cyan-400"><?= htmlspecialchars($text_ui['title']) ?>:</strong> <?= htmlspecialchars($ticket['ticket_name']) ?></p>
            <p class="text-lg"><strong class="text-cyan-400"><?= htmlspecialchars($text_ui['message']) ?>:</strong> <?= nl2br(htmlspecialchars($ticket['message'], ENT_QUOTES, 'UTF-8')) ?></p>
            <p class="text-lg"><strong class="text-cyan-400"><?= htmlspecialchars($text_ui['created_at']) ?>:</strong> <?= date('d/m/Y à H:i:s', strtotime($ticket['created_at'])) ?></p>
        </div>
        <div class="flex justify-center mt-8">
            <a href="archive_tickets.php?lang=<?= htmlspecialchars($lang) ?>" class="btn text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-lg px-8 py-4 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <i class="fas fa-arrow-left"></i> <?= htmlspecialchars($text_ui['back']) ?>
            </a>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
