<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Politique de Confidentialité</title>
    <link rel="stylesheet" href="path/to/index/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            background-color: #1e1e1e; /* Darker background */
            font-family: 'Ubuntu', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #2f4f4f; /* Changed background color */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2); /* Stronger shadow */
        }
        h1, h2 {
            color: #333333;
            font-family: 'Ubuntu', sans-serif;
            text-align: center; /* Center the titles */
        }
        h1 {
            color: #333333;
            font-family: 'Ubuntu', sans-serif;
            text-align: center; /* Center the titles */
        }
        .orange-blue {
            color: #007bff; /* Set text to blue */
        }
        .orange-blue i {
            color: #ff5733; /* Set logos to orange */
        }
        h2 {
            color: #007bff; /* Set subtitles to blue */
        }
        h2 i {
            color: #007bff; /* Set subtitle logos to blue */
        }
        p, ul {
            color: #666666; /* Slightly darker text */
            line-height: 1.8; /* Increased line height */
        }
        .logo {
            display: block;
            margin: 20px auto;
            width: 150px;
        }
        .text-container {
            background-color: #f0f0f0; /* Lighter background */
            padding: 20px;
            margin-bottom: 20px;
            border-left: 5px solid #007bff;
            border-radius: 5px;
        }
        .home-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Stronger shadow */
            text-decoration: none;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        .home-button:hover {
            background-color: #0056b3;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4); /* Stronger shadow on hover */
        }
        .home-button i {
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: white;
            font-size: 24px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1 class="orange-blue"><i class="fas fa-user-shield"></i> Politique de <span>Confidentialité</span> <i class="fas fa-user-shield"></i></h1> <!-- Added logo at the end -->
        <div class="text-container">
            <p>Nous accordons une grande importance à votre vie privée et nous nous engageons à protéger vos données personnelles. Cette politique de confidentialité vous informera sur la manière dont nous gérons les cookies et les données que nous collectons lorsque vous utilisez notre site web.</p>
        </div>
        
        <h2><i class="fas fa-cookie-bite"></i> Cookies <i class="fas fa-cookie-bite"></i></h2>
        <div class="text-container">
            <p>Les cookies sont de petits fichiers texte qui sont placés sur votre appareil pour nous aider à améliorer votre expérience sur notre site web. Nous utilisons des cookies pour :</p>
            <ul>
                <li>Comprendre et enregistrer vos préférences pour de futures visites.</li>
                <li>Compiler des données agrégées sur le trafic du site et les interactions sur le site afin d'offrir de meilleures expériences et outils à l'avenir.</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-database"></i> Collecte de Données <i class="fas fa-database"></i></h2>
        <div class="text-container">
            <p>Nous collectons les données suivantes :</p>
            <ul>
                <li>Informations d'identification personnelle (Nom, adresse e-mail, etc.)</li>
                <li>Données d'utilisation (pages visitées, temps passé sur le site, etc.)</li>
                <li>Données techniques (adresse IP, type et version du navigateur, etc.)</li>
            </ul>
        </div>
        
        <div class="text-container">
            <p>Si vous avez des questions concernant notre politique de confidentialité, veuillez nous contacter via Soon</p>
        </div>
    </div>
    <a href="index.php" class="home-button" onclick="window.location.href='index.php'; return false;"><i class="fas fa-home"></i></a>
</body>
</html>
