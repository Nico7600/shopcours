<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valomazone Prime Advantages</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .advantages-list {
            list-style-type: none;
            padding: 0;
        }
        .advantages-list li {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .advantages-list i {
            font-size: 24px;
            margin-right: 15px;
            color: #007bff; 
        }
        .advantages-list li > div {
            display: flex;
            flex-direction: column;
        }
        .advantage-title {
            font-weight: bold;
            color: #333; 
        }
        .advantage-description {
            margin-left: 10px;
            color: #333; 
        }
        .container h1 {
            text-align: center; 
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="container">
        <h1>Avantages de Valomazone Prime</h1>
        <ul class="advantages-list">
            <li>
                <i class="fas fa-tags"></i>
                <div>
                    <span class="advantage-title">Accès exclusif aux promotions</span>
                    <div class="advantage-description">Profitez de promotions réservées uniquement aux membres Prime.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-headset"></i>
                <div>
                    <span class="advantage-title">Support client prioritaire</span>
                    <div class="advantage-description">Obtenez une assistance rapide et prioritaire pour toutes vos questions et problèmes.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-clock"></i>
                <div>
                    <span class="advantage-title">Accès anticipé aux nouvelles fonctionnalités</span>
                    <div class="advantage-description">Soyez les premiers à tester et utiliser les nouvelles fonctionnalités de notre plateforme.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-percent"></i>
                <div>
                    <span class="advantage-title">Réductions spéciales de 10% sur certains produits</span>
                    <div class="advantage-description">Bénéficiez de réductions exclusives sur une sélection de produits.</div>
                </div>
            </li>
        </ul>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>

</html>
