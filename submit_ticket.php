<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<pre>Session non définie : ";
    print_r($_SESSION);
    echo "</pre>";
    header('Location: login.php');
    exit;
} else {
    echo "<pre>Session active : ";
    print_r($_SESSION);
    echo "</pre>";
}

$lang = $_GET['lang'] ?? 'fr';
$available_languages = ['en', 'nl', 'fr'];
if (!in_array($lang, $available_languages)) {
    $lang = 'fr';
}

include 'db_connection.php';

if ($db->connect_error) {
    error_log("Échec de la connexion à la base de données : " . $db->connect_error);
    die("Une erreur interne s'est produite. Veuillez réessayer plus tard.");
}

$creator = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_name = htmlspecialchars($_POST['ticket_name'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    $created_at = date('Y-m-d H:i:s');

    $errors = [];
    if (empty($ticket_name)) {
        $errors[] = "Le champ nom du ticket est requis.";
    }
    if (empty($message)) {
        $errors[] = "Le champ message est requis.";
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            error_log($error);
        }
        die(implode('<br>', $errors)); 
    }

    $query = "INSERT INTO tickets (creator, ticket_name, message, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        error_log("Échec de la préparation de la requête : " . $db->error);
        die("Une erreur interne s'est produite. Veuillez réessayer plus tard.");
    }

    $stmt->bind_param('ssss', $creator, $ticket_name, $message, $created_at);

    if ($stmt->execute()) {
        header('Location: success.php');
        exit;
    } else {
        error_log("Erreur lors de l'exécution de la requête : " . $stmt->error);
        die("Une erreur interne s'est produite. Veuillez réessayer plus tard.");
    }

    $stmt->close();
    $db->close();
} else {
    header('Location: create_ticket.php');
    exit;
}
?>

<form method="POST" action="submit_ticket.php">
    <input type="hidden" id="creator" name="creator" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
    <label for="ticket_name">Titre du ticket :</label>
    <input type="text" id="ticket_name" name="ticket_name" required>

    <label for="message">Description :</label>
    <textarea id="message" name="message" required></textarea>

    <button type="submit">Soumettre</button>
</form>
