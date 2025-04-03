<?php
require_once('bootstrap.php');

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

$primeOption = end($_SESSION['cart']);
$duration = 0;

if ($primeOption['product'] === '30_days') {
    $duration = 30;
} elseif ($primeOption['product'] === '365_days') {
    $duration = 365;
}

if ($duration > 0) {
    $expirationDate = date('Y-m-d', strtotime("+$duration days"));
    $stmt = $pdo->prepare("UPDATE users SET is_prime = 1, prime_expiration = ? WHERE id = ?");
    $stmt->execute([$expirationDate, $userId]);
}

unset($_SESSION['cart']);

header('Location: index.php?message=prime_success');
exit();
?>