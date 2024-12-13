<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['id']) || !isset($_GET['ban_id'])) {
    header('Location: admin.php');
    exit;
}

$banId = $_GET['ban_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_unban'])) {
    $sql = 'DELETE FROM bans WHERE id = :ban_id';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    // Update the users table to set banned to 0
    $sql = 'UPDATE users SET banned = 0 WHERE id = (SELECT user_id FROM (SELECT user_id FROM bans WHERE id = :ban_id) AS subquery)';
    $query = $db->prepare($sql);
    $query->bindValue(':ban_id', $banId, PDO::PARAM_INT);
    $query->execute();

    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de déban</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
            font-family: 'Ubuntu', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
            border-radius: 10px;
        }
        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .btn {
            margin: 10px;
        }
        .fa-gavel {
            color: green;
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <i class="fa-solid fa-gavel"></i>
        <h1>Confirmation de déban</h1>
        <p>Êtes-vous sûr de vouloir débannir cet utilisateur ?</p>
        <form method="post">
            <button type="submit" name="confirm_unban" class="btn btn-success">Oui</button>
            <a href="admin.php" class="btn btn-danger">Non</a>
        </form>
    </div>
</body>
</html>