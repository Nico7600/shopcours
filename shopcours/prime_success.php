<?php
require_once('bootstrap.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $options);
    echo "Connexion à la base réussie<br>";
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

$userId = $_SESSION['id'];
$sessionId = $_GET['session_id'] ?? null;
$option = $_GET['option'] ?? null;

if (!$sessionId || !$option) {
    $_SESSION['message'] = 'Session de paiement introuvable.';
    $_SESSION['message_type'] = 'danger';
    echo "Session ou option non fournie<br>";
    header('Location: index.php');
    exit();
}

try {
    $pdo->beginTransaction();
    echo "Transaction commencée<br>";

    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    echo "Session Stripe récupérée<br>";
    if ($session->payment_status !== 'paid') {
        throw new Exception("Le paiement n'a pas été validé.");
    }

    $primeOptions = [
        '30_days' => 999,  
        '365_days' => 9999 
    ];
    $amount = $primeOptions[$option] ?? null;
    if ($amount === null) {
        throw new Exception("Option Prime invalide.");
    }

    $currentDateTime = new DateTime();
    $orderDate = $currentDateTime->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, stripe_session_id, order_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $amount / 100, $sessionId, $orderDate]); 
    echo "Commande insérée<br>";

    // Mettre à jour le statut Prime de l'utilisateur
    $expirationDate = new DateTime();
    $expirationDate->add(new DateInterval('P' . ($option === '30_days' ? 30 : 365) . 'D'));
    $stmt = $pdo->prepare("UPDATE users SET is_prime = 1, prime_expiration = ? WHERE id = ?");
    $stmt->execute([$expirationDate->format('Y-m-d H:i:s'), $userId]);
    echo "Statut Prime mis à jour<br>";

    $pdo->commit();
    echo "Transaction validée<br>";

    $_SESSION['message'] = 'Votre adhésion Prime a été activée avec succès.';
    $_SESSION['message_type'] = 'success';
    header('Location: index.php');
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['message'] = "Erreur lors de la validation de l'adhésion Prime : " . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    echo "Erreur : " . $e->getMessage() . "<br>";
    exit();
}
?>
