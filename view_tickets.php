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
        'my_tickets' => 'Mes Tickets',
        'title' => 'Titre',
        'message' => 'Message',
        'created_at' => 'Créé le',
        'no_tickets' => 'Aucun ticket trouvé.',
        'tickets_count' => [
            'one' => 'Vous avez actuellement 1 ticket ouvert.',
            'many' => 'Vous avez actuellement %d tickets ouverts.',
        ],
        'open' => 'Ouvrir',
        'archive' => 'Archiver',
        'confirm_archive' => 'Êtes-vous sûr de vouloir archiver ce ticket ?',
        'yes' => 'Oui',
        'no' => 'Non',
        'new_reply' => 'Nouvelle réponse',
        'status' => [
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'taken' => 'Pris en charge',
            'validated' => 'Validé',
            'refused' => 'Refusé',
        ],
    ],
    'en' => [
        'my_tickets' => 'My Tickets',
        'title' => 'Title',
        'message' => 'Message',
        'created_at' => 'Created At',
        'no_tickets' => 'No tickets found.',
        'tickets_count' => [
            'one' => 'You currently have 1 open ticket.',
            'many' => 'You currently have %d open tickets.',
        ],
        'open' => 'Open',
        'archive' => 'Archive',
        'confirm_archive' => 'Are you sure you want to archive this ticket?',
        'yes' => 'Yes',
        'no' => 'No',
        'new_reply' => 'New reply',
        'status' => [
            'pending' => 'Pending',
            'in_progress' => 'In progress',
            'taken' => 'Taken',
            'validated' => 'Validated',
            'refused' => 'Refused',
        ],
    ],
    'nl' => [
        'my_tickets' => 'Mijn Tickets',
        'title' => 'Titel',
        'message' => 'Bericht',
        'created_at' => 'Aangemaakt op',
        'no_tickets' => 'Geen tickets gevonden.',
        'tickets_count' => [
            'one' => 'U heeft momenteel 1 open ticket.',
            'many' => 'U heeft momenteel %d open tickets.',
        ],
        'open' => 'Openen',
        'archive' => 'Archiveren',
        'confirm_archive' => 'Weet u zeker dat u dit ticket wilt archiveren?',
        'yes' => 'Ja',
        'no' => 'Nee',
        'new_reply' => 'Nieuw antwoord',
        'status' => [
            'pending' => 'In afwachting',
            'in_progress' => 'Bezig',
            'taken' => 'Opgenomen',
            'validated' => 'Goedgekeurd',
            'refused' => 'Geweigerd',
        ],
    ],
];

$text_ui = $translations[$lang];

if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user']['id'];
$isAdmin = $_SESSION['user']['is_admin'] ?? false;

// Récupérer les permissions de l'utilisateur
$roleId = $_SESSION['user']['role_id'] ?? null;
$canViewAll = false;
if ($roleId) {
    $stmt = $db->prepare("SELECT can_view_all_tickets FROM role WHERE id = :role_id");
    $stmt->bindParam(":role_id", $roleId, PDO::PARAM_INT);
    $stmt->execute();
    $canViewAll = (bool)$stmt->fetchColumn();
}

$itemsPerPage = 8;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

if ($canViewAll) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM ticket WHERE is_close = 0");
} else if ($isAdmin) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM ticket WHERE is_close = 0");
} else {
    $stmt = $db->prepare("SELECT COUNT(*) FROM ticket WHERE creator = :creator AND is_close = 0");
    $stmt->bindParam(":creator", $userId, PDO::PARAM_INT);
}
$stmt->execute();
$totalTickets = $stmt->fetchColumn();

if ($canViewAll) {
    $stmt = $db->prepare("SELECT id, ticket_name, message, created_at, status, has_new_staff_reply, claimed_by FROM ticket WHERE is_close = 0 ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
} else if ($isAdmin) {
    $stmt = $db->prepare("SELECT id, ticket_name, message, created_at, status, has_new_staff_reply, claimed_by FROM ticket WHERE is_close = 0 ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
} else {
    $stmt = $db->prepare("SELECT id, ticket_name, message, created_at, status, has_new_staff_reply, claimed_by FROM ticket WHERE creator = :creator AND is_close = 0 ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(":creator", $userId, PDO::PARAM_INT);
}
$stmt->bindValue(":limit", $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $text_ui['my_tickets'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .font-orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .scrollable-container {
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .ticket-container {
            min-height: 112px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        .ticket-container h2 {
            font-size: 1.5rem;
            position: relative;
            text-align: left;
            margin: 0;
        }
        .tickets-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #loading-screen .spinner {
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid #00ff00;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 0.8s linear infinite; 
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-900 text-gray-100 font-orbitron">
    <div id="loading-screen">
        <div class="spinner"></div>
    </div>
    <script>
        window.addEventListener('load', () => {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }
        });
    </script>
    <div class="container mx-auto flex-grow p-6 font-orbitron h-full">
        <h1 class="text-3xl font-bold text-center mb-6"><?= $text_ui['my_tickets'] ?></h1>
        <div class="scrollable-container">
            <?php if (empty($tickets)): ?>
                <p class="text-left text-gray-400"><?= $text_ui['no_tickets'] ?></p>
            <?php else: ?>
                <div class="tickets-grid">
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket-container bg-gray-800 border border-gray-700 rounded-lg p-6 shadow-lg hover:shadow-xl transition-shadow font-orbitron relative">
                            <div class="flex items-center space-x-2 mb-2">
                                <h2 class="text-2xl font-semibold text-cyan-400 font-bold text-left">
                                    <?= htmlspecialchars($ticket['ticket_name']) ?>
                                </h2>
                                <?php if (!$isAdmin && !empty($ticket['has_new_staff_reply'])): ?>
                                    <span class="ml-2 px-2 py-1 bg-yellow-500 text-black text-xs rounded-full font-bold"><?= $text_ui['new_reply'] ?></span>
                                <?php endif; ?>
                                <?php
                                    $status = $ticket['status'] ?? 'pending';
                                    $statusColors = [
                                        'pending' => 'bg-gray-500',
                                        'in_progress' => 'bg-blue-500',
                                        'taken' => 'bg-purple-500',
                                        'validated' => 'bg-green-500',
                                        'refused' => 'bg-red-500',
                                    ];
                                    $color = $statusColors[$status] ?? 'bg-gray-500';
                                ?>
                                <span class="ml-2 px-2 py-1 <?= $color ?> text-white text-xs rounded-full font-bold">
                                    <?= $text_ui['status'][$status] ?? ucfirst($status) ?>
                                </span>
                                <?php if ($canViewAll && empty($ticket['claimed_by'])): ?>
                                    <form method="post" action="claim_ticket.php" class="inline ml-2">
                                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                        <button type="submit" class="px-2 py-1 bg-orange-500 text-white text-xs rounded-full font-bold hover:bg-orange-600">
                                            Claim
                                        </button>
                                    </form>
                                <?php elseif ($canViewAll && !empty($ticket['claimed_by'])): ?>
                                    <span class="ml-2 px-2 py-1 bg-indigo-500 text-white text-xs rounded-full font-bold">
                                        Claimed by <?= htmlspecialchars($ticket['claimed_by']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($canViewAll): ?>
                                <form method="post" action="change_ticket_status.php" class="mb-2">
                                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                    <select name="status" class="text-black rounded px-2 py-1 text-xs">
                                        <?php foreach ($text_ui['status'] as $key => $label): ?>
                                            <option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="ml-2 px-2 py-1 bg-blue-500 text-white text-xs rounded-full font-bold hover:bg-blue-600">
                                        Changer
                                    </button>
                                </form>
                            <?php endif; ?>
                            <p class="text-sm text-gray-500 absolute bottom-2 left-4 text-left">
                                <?= $text_ui['created_at'] ?>: <?= date('d/m/Y à H:i:s', strtotime($ticket['created_at'])) ?>
                            </p>
                            <div class="absolute inset-y-0 right-4 flex items-center space-x-4">
                                <a href="open_ticket.php?id=<?= urlencode($ticket['id']) ?>" class="text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                    <i class="fas fa-folder-open"></i> <?= $text_ui['open'] ?>
                                </a>
                                <button onclick="confirmArchive(<?= htmlspecialchars($ticket['id']) ?>)" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                    <i class="fas fa-archive"></i> <?= $text_ui['archive'] ?>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex justify-center mt-6">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&lang=<?= $lang ?>" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Précédent
                </a>
            <?php endif; ?>
            <?php if ($page * $itemsPerPage < $totalTickets): ?>
                <a href="?page=<?= $page + 1 ?>&lang=<?= $lang ?>" class="ml-4 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Suivant
                </a>
            <?php endif; ?>
        </div>
        <p class="text-center text-gray-400 mt-4">
            <?php if ($totalTickets === 1): ?>
                <?= $text_ui['tickets_count']['one'] ?>
            <?php else: ?>
                <?= sprintf($text_ui['tickets_count']['many'], $totalTickets) ?>
            <?php endif; ?>
        </p>
    </div>
    <div id="archive-confirmation" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-lg p-6 shadow-lg text-center">
            <p class="text-xl text-gray-100 mb-4">
                <i class="fas fa-exclamation-circle text-red-500"></i> <?= $text_ui['confirm_archive'] ?>
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirm-yes" class="text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    <i class="fas fa-check"></i> <?= $text_ui['yes'] ?>
                </button>
                <button id="confirm-no" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-6 py-3 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                    <i class="fas fa-times"></i> <?= $text_ui['no'] ?>
                </button>
            </div>
        </div>
    </div>
    <script>
        let ticketToArchive = null;

        function confirmArchive(ticketId) {
            ticketToArchive = ticketId;
            document.getElementById('archive-confirmation').classList.remove('hidden');
        }

        document.getElementById('confirm-yes').addEventListener('click', () => {
            if (ticketToArchive) {
                fetch('archive_tickets.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: ticketToArchive })
                }).then(response => {
                    if (response.ok) {
                        alert("Le ticket a été archivé avec succès.");
                        location.reload();
                    } else {
                        return response.text().then(text => {
                            alert("Erreur: " + text);
                        });
                    }
                }).catch(error => {
                    console.error("Erreur:", error);
                    alert("Une erreur réseau s'est produite.");
                });
            }
            document.getElementById('archive-confirmation').classList.add('hidden');
        });

        document.getElementById('confirm-no').addEventListener('click', () => {
            ticketToArchive = null;
            document.getElementById('archive-confirmation').classList.add('hidden');
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>