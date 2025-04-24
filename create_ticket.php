<?php
session_start();

include 'db_connection.php';

if (!isset($db) || !$db) {
    die("Erreur : La connexion à la base de données n'a pas été établie.");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$lang = $_GET['lang'] ?? ($_COOKIE['lang'] ?? 'fr');
$available_languages = ['en', 'nl', 'fr'];
if (!in_array($lang, $available_languages)) {
    $lang = 'fr';
}

$translations = [
    'en' => [
        'create_ticket' => 'Create a New Ticket',
        'title' => 'Title:',
        'description' => 'Description:',
        'priority' => 'Priority:',
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'submit' => 'Submit',
        'success_message' => 'Ticket successfully created!',
        'error_message' => 'An error occurred while creating the ticket.',
        'missing_fields' => 'Please fill in all fields.',
    ],
    'nl' => [
        'create_ticket' => 'Maak een Nieuw Ticket',
        'title' => 'Titel:',
        'description' => 'Beschrijving:',
        'priority' => 'Prioriteit:',
        'low' => 'Laag',
        'medium' => 'Gemiddeld',
        'high' => 'Hoog',
        'submit' => 'Indienen',
        'success_message' => 'Ticket succesvol aangemaakt!',
        'error_message' => 'Er is een fout opgetreden bij het aanmaken van het ticket.',
        'missing_fields' => 'Gelieve alle velden in te vullen.',
    ],
    'fr' => [
        'create_ticket' => 'Créer un Nouveau Ticket',
        'title' => 'Titre:',
        'description' => 'Description:',
        'priority' => 'Priorité:',
        'low' => 'Faible',
        'medium' => 'Moyenne',
        'high' => 'Haute',
        'submit' => 'Envoyer',
        'success_message' => 'Ticket créé avec succès !',
        'error_message' => 'Une erreur est survenue lors de la création du ticket.',
        'missing_fields' => 'Veuillez remplir tous les champs.',
    ],
];

// Vérification de la connexion
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}
$text_ui = $translations[$lang];

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title   = trim($_POST["ticket_name"]);
    $message = trim($_POST["message"]);
    $userId  = $_SESSION['user']['id'];

    if (empty($title) || empty($message)) {
        $_SESSION["error_message"] = $text_ui['missing_fields'];
    } elseif (strlen($title) > 20) {
        $_SESSION["error_message"] = "Le titre ne doit pas dépasser 20 caractères.";
    } else {
        // Insérer un nouveau ticket
        $stmtTicket = $db->prepare("INSERT INTO ticket (creator, ticket_name, message, created_at) VALUES (:creator, :ticket_name, :message, NOW())");
        $stmtTicket->bindParam(":creator", $userId, PDO::PARAM_INT);
        $stmtTicket->bindParam(":ticket_name", $title);
        $stmtTicket->bindParam(":message", $message);

        if ($stmtTicket->execute()) {
            $ticketId = $db->lastInsertId();

            $stmtUpdateUser = $db->prepare("UPDATE users SET ticket_count = ticket_count + 1, open_ticket_count = open_ticket_count + 1 WHERE id = :userId");
            $stmtUpdateUser->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmtUpdateUser->execute();

            $_SESSION["success_message"] = $text_ui['success_message'];
            header("Location: view_tickets.php?id=" . $ticketId);
            exit;
        } else {
            $_SESSION["error_message"] = $text_ui['error_message'];
        }
    }
}

include 'header.php';
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $text_ui['create_ticket'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Orbitron', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container {
            flex: 1;
        }
        footer {
            margin-top: auto;
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-900 text-white fade-in">
    <div class="container mx-auto p-6 fade-in">
        <h1 class="text-3xl font-bold text-center mb-6 fade-in">
            <i class="fas fa-ticket-alt"></i> <?= $text['create_ticket'] ?>
        </h1>

        <?php if (!empty($_SESSION["error_message"])): ?>
            <p class="text-red-500 text-center mb-4"><?= $_SESSION["error_message"]; unset($_SESSION["error_message"]); ?></p>
        <?php endif; ?>

        <form action="create_ticket.php" method="POST" class="bg-gray-800 p-8 rounded-lg shadow-lg fade-in">
            <div class="mb-6 fade-in">
                <label for="ticket_name" class="block text-lg font-medium mb-2">
                    <i class="fas fa-heading"></i> <?= $text_ui['title'] ?>
                </label>
                <input type="text" id="ticket_name" name="ticket_name" maxlength="20" 
                       class="w-full border border-gray-700 rounded-lg px-4 py-2 bg-gray-700 text-white focus:ring-2 focus:ring-blue-500" 
                       oninput="if(this.value.length > 20) this.value = this.value.slice(0, 20);" required>
            </div>
            <div class="mb-6 fade-in">
                <label for="message" class="block text-lg font-medium mb-2">
                    <i class="fas fa-align-left"></i> <?= $text_ui['description'] ?>
                </label>
                <textarea id="message" name="message" rows="5" class="w-full border border-gray-700 rounded-lg px-4 py-2 bg-gray-700 text-white focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white text-lg font-medium px-6 py-3 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-500 fade-in">
                <i class="fas fa-paper-plane"></i> <?= $text_ui['submit'] ?>
            </button>
        </form>
    </div>
</body>
</html>

<?php
include 'footer.php';
?>