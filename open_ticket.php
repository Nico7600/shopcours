<?php

session_start();

include 'db_connection.php';



if (!isset($db) || !$db) {

    die("Erreur : La connexion à la base de données n'a pas été établie.");

}



if (!isset($_SESSION['user']['id'])) {

    header("Location: login.php");

    exit;

}



$userId = $_SESSION['user']['id'];

$isAdmin = $_SESSION['user']['is_admin'] ?? false;



// Validate the ticket ID

$ticketId = $_GET['id'] ?? null;

if (!$ticketId || !is_numeric($ticketId)) {

    include 'header.php';

    ?>

    <!DOCTYPE html>

    <html lang="<?= htmlspecialchars($lang) ?>">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Erreur</title>

        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <style>

            body {

                font-family: 'Orbitron', sans-serif;

            }

            h1, p, a {

                font-family: 'Orbitron', sans-serif;

            }

        </style>

    </head>

    <body class="font-sans bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col">

        <div class="flex-grow container mx-auto max-w-3xl bg-gray-800 rounded-lg shadow-2xl p-12 text-gray-200 my-16 sm:p-10 md:p-12 lg:p-16 flex flex-col items-center justify-center border border-gray-700">

            <h1 class="text-5xl font-extrabold mb-10 text-center text-red-500">

                <i class="fas fa-exclamation-circle"></i> Erreur

            </h1>

            <p class="text-xl text-center bg-gray-700 p-6 rounded-lg shadow-md mb-8">

                <i class="fas fa-lock"></i> L'ID du ticket est invalide.

            </p>

            <a href="view_tickets.php?lang=<?= htmlspecialchars($lang) ?>" class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 font-semibold flex items-center justify-center text-lg shadow-lg">

                <i class="fas fa-arrow-left mr-3"></i> Retour à la liste des tickets

            </a>

        </div>

        <?php include 'footer.php'; ?>

    </body>

    </html>

    <?php

    exit;

}



$stmt = $db->prepare("SELECT creator FROM ticket WHERE id = :id");

$stmt->bindParam(":id", $ticketId, PDO::PARAM_INT);

$stmt->execute();

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);



if (!$ticket) {

    include 'header.php';

    ?>

    <!DOCTYPE html>

    <html lang="<?= htmlspecialchars($lang) ?>">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Erreur</title>

        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <style>

            body {

                font-family: 'Orbitron', sans-serif;

            }

            h1, p, a {

                font-family: 'Orbitron', sans-serif;

            }

        </style>

    </head>

    <body class="font-sans bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col">

        <div class="flex-grow container mx-auto max-w-3xl bg-gray-800 rounded-lg shadow-2xl p-12 text-gray-200 my-16 sm:p-10 md:p-12 lg:p-16 flex flex-col items-center justify-center border border-gray-700">

            <h1 class="text-5xl font-extrabold mb-10 text-center text-red-500">

                <i class="fas fa-exclamation-circle"></i> Erreur

            </h1>

            <p class="text-xl text-center bg-gray-700 p-6 rounded-lg shadow-md mb-8">

                <i class="fas fa-times-circle"></i> Ce ticket n'existe pas.

            </p>

            <a href="view_tickets.php?lang=<?= htmlspecialchars($lang) ?>" class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 font-semibold flex items-center justify-center text-lg shadow-lg">

                <i class="fas fa-arrow-left mr-3"></i> Retour à la liste des tickets

            </a>

        </div>

        <?php include 'footer.php'; ?>

    </body>

    </html>

    <?php

    exit;

}



if (!$isAdmin && $ticket['creator'] != $userId) {

    include 'header.php';

    ?>

    <!DOCTYPE html>

    <html lang="<?= htmlspecialchars($lang) ?>">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Erreur</title>

        <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

        <script src="https://cdn.tailwindcss.com"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <style>

            body {

                font-family: 'Orbitron', sans-serif;

            }

            h1, p, a {

                font-family: 'Orbitron', sans-serif;

            }

        </style>

    </head>

    <body class="font-sans bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col">

        <div class="flex-grow container mx-auto max-w-3xl bg-gray-800 rounded-lg shadow-2xl p-12 text-gray-200 my-16 sm:p-10 md:p-12 lg:p-16 flex flex-col items-center justify-center border border-gray-700">

            <h1 class="text-5xl font-extrabold mb-10 text-center text-red-500">

                <i class="fas fa-exclamation-circle"></i> Erreur

            </h1>

            <p class="text-xl text-center bg-gray-700 p-6 rounded-lg shadow-md mb-8">

                <i class="fas fa-lock"></i> Vous n'êtes pas autorisé à voir ce ticket.

            </p>

            <a href="view_tickets.php?lang=<?= htmlspecialchars($lang) ?>" class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 font-semibold flex items-center justify-center text-lg shadow-lg">

                <i class="fas fa-arrow-left mr-3"></i> Retour à la liste des tickets

            </a>

        </div>

        <?php include 'footer.php'; ?>

    </body>

    </html>

    <?php

    exit;

}



ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);



require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();



try {

    $db = new PDO(

        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",

        $_ENV['DB_USER'],

        $_ENV['DB_PASSWORD']

    );

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Erreur de connexion à la base de données : " . $e->getMessage());

}



$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : 0;



$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr';

$supported_languages = ['fr', 'en', 'nl'];

if (!in_array($lang, $supported_languages)) {

    $lang = 'fr';

}



$translations = [

    'fr' => [

        'title' => 'Ticket',

        'write_message' => 'Écrivez votre message...',

        'send' => 'Envoyer',

        'ticket_not_found' => 'Ticket introuvable.',

        'request' => 'Requête',

    ],

    'en' => [

        'title' => 'Ticket',

        'write_message' => 'Write your message...',

        'send' => 'Send',

        'ticket_not_found' => 'Ticket not found.',

        'request' => 'Request',

    ],

    'nl' => [

        'title' => 'Ticket',

        'write_message' => 'Schrijf uw bericht...',

        'send' => 'Verzenden',

        'ticket_not_found' => 'Ticket niet gevonden.',

        'request' => 'Verzoek',

    ],

];



$trans = $translations[$lang];



$roleImages = [

    1 => 'utilisateur.png',

    2 => 'admin.png',

    3 => 'dev.png',

    4 => 'modo.png',

    5 => 'guide.png',

];

$roleLabels = [

    1 => 'Utilisateur',

    2 => 'Admin',

    3 => 'Dev',

    4 => 'Modo',

    5 => 'Guide',

];



$query = $db->prepare("

    SELECT t.ticket_name, t.message, t.created_at, u.name AS creator_name, u.profile_picture, u.id_perm

    FROM ticket t

    JOIN users u ON t.creator = u.id

    WHERE t.id = ?

");

$query->execute([$ticket_id]);

$ticket = $query->fetch(PDO::FETCH_ASSOC);



if (!$ticket) {

    die($trans['ticket_not_found']);

}



$messages_query = $db->prepare("

    SELECT m.message, u.name, u.profile_picture, u.id_perm

    FROM ticket_message m

    JOIN users u ON m.creator = u.id

    WHERE m.ticket_id = ?

    ORDER BY m.created_at ASC

");

$messages_query->execute([$ticket_id]);

$messages = $messages_query->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;



    if (!empty($message) && $ticket_id > 0) {

        $insert_query = $db->prepare("

            INSERT INTO ticket_message (ticket_id, creator, message, created_at, published_at)

            VALUES (?, ?, ?, NOW(), NOW())

        ");

        $insert_query->execute([

            $ticket_id, 

            $userId, 

            $message

        ]);

        header("Location: open_ticket.php?id=$ticket_id&lang=$lang");

        exit;

    }

}

?>



<!DOCTYPE html>

<html lang="<?= htmlspecialchars($lang) ?>">



<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($ticket['ticket_name']) ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>

        body {

            font-family: 'Orbitron', sans-serif;

        }



        * {

            font-family: 'Orbitron', sans-serif;

            /* Applique Orbitron à tout le texte */

        }

    </style>

</head>



<body class="font-sans bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col">

    <?php include 'header.php'; ?>

    <div class="flex-grow container mx-auto max-w-4xl bg-gray-800 rounded-lg shadow-lg p-8 text-gray-200 my-8 sm:p-6 md:p-8 lg:p-10">

        <h1 class="text-4xl font-extrabold mb-8 text-center text-white text-xl sm:text-2xl md:text-3xl lg:text-4xl"><?= htmlspecialchars($ticket['ticket_name']) ?></h1>

        <div class="mb-8 flex flex-col sm:flex-row items-center bg-gray-700 p-4 rounded-lg shadow-md">

            <img src="<?= !empty($ticket['profile_picture']) ? htmlspecialchars($ticket['profile_picture']) : 'uploads/default_profile.png' ?>" alt="<?= htmlspecialchars($ticket['creator_name']) ?>" class="w-20 h-20 rounded-full mr-0 sm:mr-6 border-2 border-gray-500">

            <div class="text-center sm:text-left">

                <div class="flex items-center justify-center sm:justify-start mb-2">

                    <span class="font-semibold text-lg text-white mr-3"><?= htmlspecialchars($ticket['creator_name']) ?></span>

                    <img src="<?= isset($roleImages[$ticket['id_perm']]) ? "images_statut/" . $roleImages[$ticket['id_perm']] : "images_statut/default_role.png" ?>" alt="<?= isset($roleLabels[$ticket['id_perm']]) ? $roleLabels[$ticket['id_perm']] : 'Role inconnu' ?>" class="w-[69px] h-[39px]">

                </div>

                <p class="text-sm text-gray-400"><?= date('d/m/Y à H:i:s', strtotime($ticket['created_at'])) ?></p>

            </div>

        </div>

        <h2 class="text-2xl font-bold text-center mb-4"><?= htmlspecialchars($trans['request']) ?></h2>

        <p class="text-lg mb-6 bg-gray-700 p-4 rounded-lg shadow-md text-sm sm:text-base md:text-lg">

            <?= nl2br(htmlspecialchars($ticket['message'])) ?>

        </p>



        <?php foreach ($messages as $message): ?>

            <div class="message flex items-start mb-6 bg-gray-800 p-4 rounded-lg shadow-md">

                <img src="<?= !empty($message['profile_picture']) ? htmlspecialchars($message['profile_picture']) : 'uploads/default_profile.png' ?>" alt="<?= htmlspecialchars($message['name']) ?>" class="w-14 h-14 rounded-full mr-6 border-2 border-gray-500">

                <div class="flex-1">

                    <div class="flex items-center mb-2">

                        <span class="name font-bold text-lg text-white mr-3"><?= htmlspecialchars($message['name']) ?></span>

                        <img src="<?= isset($roleImages[$message['id_perm']]) ? "images_statut/" . $roleImages[$message['id_perm']] : "images_statut/default_role.png" ?>" alt="<?= isset($roleLabels[$message['id_perm']]) ? $roleLabels[$message['id_perm']] : 'Role inconnu' ?>" class="w-[69px] h-[39px]">

                    </div>

                    <p class="text-gray-300"><strong>Réponse :</strong> <?= htmlspecialchars($message['message']) ?></p>

                </div>

            </div>

        <?php endforeach; ?>



        <form id="chat-form" method="POST" action="send_message.php" class="flex flex-col bg-gray-700 p-6 rounded-lg shadow-md">

            <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">

            <textarea name="message" placeholder="<?= htmlspecialchars($trans['write_message']) ?>" required class="border border-gray-600 p-4 mb-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-200 resize-none bg-gray-800"></textarea>

            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center justify-center font-semibold">

                <i class="fas fa-paper-plane mr-2"></i> <?= htmlspecialchars($trans['send']) ?>

            </button>

        </form>

    </div>

    <?php include 'footer.php'; ?>

</body>



</html>