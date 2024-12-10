
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body>
    <main class="container">
        <h1>Votre Panier</h1>
        <?php if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['panier'] as $id_produit => $quantite): ?>
                        <tr>
                            <td><?= htmlspecialchars($id_produit) ?></td>
                            <td><?= htmlspecialchars($quantite) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Continuer vos achats</a>
    </main>
</body>
</html>