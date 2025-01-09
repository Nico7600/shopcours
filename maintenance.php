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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
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
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .maintenance-container img {
            max-width: 100px;
            margin-bottom: 1rem;
        }
        @keyframes hourglass {
            0%, 50% { transform: rotate(0); }
            100% { transform: rotate(180deg); }
        }
        .maintenance-container .fa-hourglass {
            font-size: 4rem;
            color: #ff6666; /* Light red color */
            margin-bottom: 1rem;
            animation: hourglass 4s infinite linear;
        }
        .maintenance-container h1 {
            font-size: 2rem;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .maintenance-container p {
            font-size: 1.2rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .maintenance-container .brand {
            font-size: 2.5rem; /* Larger font size */
            font-weight: bold; /* Bold text */
            color: #343a40;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <div class="brand">Valomazone</div>
        <i class="fa-solid fa-hourglass"></i>
        <h1>Site en maintenance</h1>
        <p>Nous reviendrons bientôt. Merci de votre patience.</p>
    </div>
</body>
</html>