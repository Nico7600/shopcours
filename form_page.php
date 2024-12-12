
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire</title>
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['message_type']) ?>">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <form action="form_handler.php" method="POST">
        <label for="field1">Champ 1:</label>
        <input type="text" id="field1" name="field1" required>
        
        <label for="field2">Champ 2:</label>
        <input type="text" id="field2" name="field2" required>
        
        <label for="field3">Champ 3:</label>
        <input type="text" id="field3" name="field3" required>
        
        <button type="submit">Soumettre</button>
    </form>
</body>
</html>