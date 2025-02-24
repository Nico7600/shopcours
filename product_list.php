
<?php
session_start();

try {
    if (!isset($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'])) {
        throw new Exception('Database configuration environment variables are not set.');
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'],
        $_ENV['DB_NAME']
    );

    $db = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    $_SESSION['error'] = "Une erreur interne s'est produite. Merci de réessayer plus tard.";
    exit();
}
?>

<table>
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['price']); ?></td>
            <td>
                <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>