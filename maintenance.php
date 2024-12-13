<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site en maintenance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .maintenance-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .maintenance-container img {
            max-width: 100px;
            margin-bottom: 1rem;
        }
        @keyframes hourglass {
            0% { transform: rotate(0); }
            45% { transform: rotate(180deg); }
            55% { transform: rotate(180deg); }
            100% { transform: rotate(360deg); }
        }
        .maintenance-container .fa-hourglass {
            font-size: 4rem;
            color: #d3d3d3;
            margin-bottom: 1rem;
            animation: hourglass 8s infinite linear;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <i class="fa-solid fa-hourglass"></i>
        <h1>Site en maintenance</h1>
        <p>Nous reviendrons bientôt. Merci de votre patience.</p>
    </div>
</body>
</html>