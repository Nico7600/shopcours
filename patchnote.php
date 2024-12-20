<?php
session_start();
$isAdmin = isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $update = $_POST['update'];
    file_put_contents('updates.txt', $update . PHP_EOL, FILE_APPEND);
}

$updates = file_exists('updates.txt') ? file('updates.txt', FILE_IGNORE_NEW_LINES) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patch Notes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
            color: #6c757d;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 50px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .badge-bottom-right {
            position: absolute;
            bottom: 10px;
            right: 10px;
        }
        .fixed-height {
            height: 200px;
            object-fit: cover;
        }
        .star-rating i {
            color: #ffc107;
        }
        .card-price-original {
            text-decoration: line-through;
            color: #dc3545;
        }
        .card-price-promo {
            color: #28a745;
            font-weight: bold;
        }
        .card-quantity {
            font-size: 0.9rem;
        }
        .out-of-stock {
            color: #dc3545;
        }
        .low-quantity {
            color: #ffc107;
        }
        .medium-quantity {
            color: #17a2b8;
        }
        .high-quantity {
            color: #28a745;
        }
        .very-high-quantity {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/navbar.php'; ?>
    <div class="container mt-5">
        <div class="header text-center mb-4">
            <h1>Patch Notes</h1>
        </div>
        <?php if ($isAdmin): ?>
            <form method="post">
                <textarea name="update" required class="form-control mb-3"></textarea>
                <button type="submit" class="btn btn-primary btn-block">Add Update</button>
            </form>
        <?php endif; ?>
        <ul class="list-group">
            <?php foreach ($updates as $update): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($update); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
