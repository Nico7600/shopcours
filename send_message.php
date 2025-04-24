<?php
session_start();
include 'db_connection.php';

if (!isset($db) || !$db) {
    error_log("Database connection is not established.");
    http_response_code(500);
    echo "Erreur : La connexion à la base de données n'a pas été établie.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $user_id = $_SESSION['user']['id'] ?? null;

if (!$user_id) {
    http_response_code(403);
    echo "Utilisateur non connecté.";
    exit;
}

    if ($ticket_id > 0 && $message !== '') {
        try {
            $query = null;

            $ticketQuery = $db->prepare("SELECT id FROM ticket WHERE id = ?");
            $ticketQuery->execute([$ticket_id]);
            $ticket = $ticketQuery->fetch(PDO::FETCH_ASSOC);

            if (!$ticket) {
                http_response_code(404);
                echo "Ticket introuvable.";
                exit;
            }

            $query = $db->prepare("INSERT INTO ticket_message (ticket_id, creator, message, created_at, published_at) VALUES (?, ?, ?, NOW(), NOW())");
            $query->execute([$ticket_id, $user_id, $message]);

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            error_log("SQL Query: " . ($query->queryString ?? 'N/A'));
            error_log("Parameters: ticket_id=$ticket_id, creator=$user_id, message=$message");
            http_response_code(500);
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Erreur</title>
                <script src="https://cdn.tailwindcss.com"></script>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
                <style>
                    .font-orbitron {
                        font-family: 'Orbitron', sans-serif;
                    }
                    body {
                        display: flex;
                        flex-direction: column;
                        min-height: 100vh;
                        margin: 0;
                        background-color: #111827;
                        color: #fff;
                    }
                    .main-container {
                        display: flex;
                        flex: 1;
                        justify-content: center;
                        align-items: center;
                    }
                    .error-container {
                        max-width: 600px;
                        width: 90%;
                        padding: 20px;
                        background-color: #1f2937;
                        border-radius: 8px;
                        text-align: center;
                        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                    }
                    .error-container h1 {
                        font-size: 2rem;
                        margin-bottom: 20px;
                        color: #f87171;
                    }
                    .error-container p {
                        font-size: 1.2rem;
                        margin-bottom: 20px;
                    }
                    .error-container a {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #3b82f6;
                        border-radius: 5px;
                        text-decoration: none;
                        color: #fff;
                    }
                    .error-container a:hover {
                        background-color: #2563eb;
                    }
                    footer {
                        margin-top: auto;
                        padding: 10px;
                        background-color: #1f2937;
                        text-align: center;
                    }
                </style>
            </head>
            <body class="font-orbitron">
                <div class="main-container">
                    <div class="error-container">
                        <h1><i class="fas fa-exclamation-circle"></i> Une erreur est survenue</h1>
                        <p>Une erreur est survenue lors de l'envoi du message. Veuillez réessayer plus tard.</p>
                        <a href="view_tickets.php">Retour à la liste des tickets</a>
                    </div>
                </div>
                <footer>
                    <?php include 'footer.php'; ?>
                </footer>
            </body>
            </html>
            <?php
            exit;
        }
    } else {
        http_response_code(400);
        echo "Données invalides.";
        exit;
    }
}

header("Location: open_ticket.php?id=$ticket_id");
exit;
		