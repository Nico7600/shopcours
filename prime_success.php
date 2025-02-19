
<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];
\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);

$sessionId = $_GET['session_id'] ?? null;
$option = $_GET['option'] ?? null;

if (!$sessionId || !$option) {
    $_SESSION['message'] = 'Session de paiement introuvable.';
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit();
}

try {
    $session = \Stripe\Checkout\Session::retrieve($sessionId);
    if ($session->payment_status !== 'paid') {
        throw new Exception("Le paiement n'a pas été validé.");
    }

    $primeOptions = [
        '30_days' => 30,
        '365_days' => 365
    ];

    if (isset($primeOptions[$option])) {
        $duration = $primeOptions[$option];
        $sql = 'UPDATE users SET is_prime = 1, prime_expiration = DATE_ADD(NOW(), INTERVAL :duration DAY) WHERE id = :id';
        $query = $db->prepare($sql);
        $query->bindValue(':duration', $duration, PDO::PARAM_INT);
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        $query->execute();

        $_SESSION['message'] = 'Votre adhésion Prime a été activée avec succès.';
        $_SESSION['message_type'] = 'success';
    } else {
        throw new Exception("Option Prime invalide.");
    }

    header('Location: index.php');
    exit();
} catch (Exception $e) {
    $_SESSION['message'] = "Erreur lors de la validation de l'adhésion Prime : " . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    header('Location: index.php');
    exit();
}