<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

// Fetch the prime option from the session cart
$primeOption = end($_SESSION['cart']);
$duration = 0;

if ($primeOption['product'] === '30_days') {
    $duration = 30;
} elseif ($primeOption['product'] === '365_days') {
    $duration = 365;
}

if ($duration > 0) {
    // Update the user's is_prime status and set the expiration date
    $expirationDate = date('Y-m-d', strtotime("+$duration days"));
    $stmt = $pdo->prepare("UPDATE users SET is_prime = 1, prime_expiration = ? WHERE id = ?");
    $stmt->execute([$expirationDate, $userId]);
}

// Clear the cart
unset($_SESSION['cart']);

// Redirect to the index page with a success message
header('Location: index.php?message=prime_success');
exit();
?>