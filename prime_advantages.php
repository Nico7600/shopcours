<?php require_once 'bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valomazone Prime Advantages</title>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px; /* Ajuste pour éviter que la navbar ne recouvre le contenu */
            padding: 20px;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030; /* Assure que la navbar est au-dessus des autres éléments */
        }
        .container {
            margin-top: 60px; 
            margin-bottom: 40px; 
        }
        .container h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #33c1ff;
            margin-bottom: 50px; 
        }
        .container h1 span {
            color: #ff5733; 
        }
        .advantages-list {
            list-style-type: none;
            padding: 0;
        }
        .advantages-list li {
            display: flex;
            align-items: center;
            margin-bottom: 30px; 
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color:#6c757d;
            color: #ffffff;
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
            color: #ffffff;
        }
        .advantage-description {
            margin-left: 10px;
            color: #ffffff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(function (dropdown) {
                new bootstrap.Dropdown(dropdown);
            });
        });
    </script>
    <div class="container">
        <h1><span>Avantages</span> de Valomazone Prime</h1>
        <ul class="advantages-list">
            <li>
                <i class="fas fa-shipping-fast"></i>
                <div>
                    <span class="advantage-title">Livraison rapide gratuite</span>
                    <div class="advantage-description">Profitez d'une livraison rapide et gratuite sur vos commandes, vous permettant de recevoir vos produits en un temps record sans frais supplémentaires.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-star"></i>
                <div>
                    <span class="advantage-title">Accès à des offres exclusives</span>
                    <div class="advantage-description">Bénéficiez d'offres spéciales et de promotions uniques réservées uniquement aux membres Prime, vous permettant d'économiser davantage sur vos achats.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-percent"></i>
                <div>
                    <span class="advantage-title">Réduction de 20% sur tous les produits de nos partenaires</span>
                    <div class="advantage-description">Économisez 20% sur une large gamme de produits proposés par nos partenaires, tout en profitant de la qualité et de la diversité de leurs offres.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-tags"></i>
                <div>
                    <span class="advantage-title">Économies supplémentaires sur certains produits</span>
                    <div class="advantage-description">Profitez de remises supplémentaires sur une sélection de produits populaires, vous permettant de maximiser vos économies sur vos achats préférés.</div>
                </div>
            </li>
            <li>
                <i class="fas fa-gift"></i>
                <div>
                    <span class="advantage-title">2 mois de Prime offerts</span>
                    <div class="advantage-description">Essayez Prime gratuitement pendant 2 mois et découvrez tous ses avantages, y compris les livraisons rapides, les offres exclusives et bien plus encore, sans aucun engagement.</div>
                </div>
            </li>
        </ul>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script>
        // Link the "Connexion" button
        document.querySelectorAll('.btn-connexion').forEach(function (button) {
            button.addEventListener('click', function () {
                window.location.href = 'connexion.php'; // Update with the correct path
            });
        });
    </script>
</body>

</html>
