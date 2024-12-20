<?php
session_start();
require_once('connect.php');

$sql = 'SELECT updates.message, updates.created_at, users.username 
        FROM updates 
        JOIN users ON updates.user_id = users.id 
        ORDER BY updates.created_at DESC';
$query = $db->prepare($sql);
$query->execute();
$patchNotes = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patch Notes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Valomazone Admin</a>
        <div class="menu">
            <a href="add.php">Ajouter produit</a>
            <a href="admin.php">Admin</a>
        </div>
    </div>
    <div class="container mt-5">
        <h1>Patch Notes</h1>
        <div class="patch-notes">
            <?php foreach ($patchNotes as $note): ?>
                <div class="patch-note">
                    <p><?php echo htmlspecialchars($note['message']); ?></p>
                    <small>By <?php echo htmlspecialchars($note['username']); ?> on <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($note['created_at']))); ?></small>
                </div>
                <hr>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
