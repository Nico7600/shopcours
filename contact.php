<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page en développement</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap">
    <style> 
        body {
            background-color: #2f4f4f; 
            color: #343a40;
            font-family: 'Ubuntu', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            background-color: #3e3e3e; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        .error-container h1 {
            font-size: 5rem;
            font-weight: 700;
            color: #ffffff;
        }
        .error-container h1 i {
            color: #ff5733; 
        }
        .error-container h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #ffffff; 
        }
        .error-container p {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 30px;
            color: #d3d3d3; 
        }
        .error-container a {
            font-size: 1.2rem;
            font-weight: 400;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-home {
            background-color: #007bff;
            color: #ffffff;
        }
        .btn-home:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1><i class="fas fa-tools"></i></h1>
        <h2>Page en développement</h2>
        <p>Cette page est actuellement en cours de développement. Revenez plus tard pour plus d'informations.</p>
        <div>
            <a href="index.php" class="btn btn-home"><i class="fas fa-home"></i> Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>