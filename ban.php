<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['id'];
$sql = 'SELECT reason, ban_end_date, banned_by FROM bans WHERE user_id = :user_id';
$query = $db->prepare($sql);
$query->bindValue(':user_id', $userId, PDO::PARAM_INT);
$query->execute();
$ban = $query->fetch();

if (!$ban) {
    header('Location: index.php');
    exit;
}

$ban['ban_end_date'] = date('d/m/Y à H:i:s', strtotime($ban['ban_end_date']));

$sql = 'SELECT username FROM users WHERE id = :id';
$query = $db->prepare($sql);
$query->bindValue(':id', $ban['banned_by'], PDO::PARAM_INT);
$query->execute();
$bannedBy = $query->fetch();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vous êtes banni</title>
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
        .ban-container {
            text-align: center;
            padding: 60px 30px 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            border-radius: 10px;
            position: relative;
        }
        h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .fa-gavel {
            color: red;
            font-size: 6rem;
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
        }
    </style>
</head>
<body>
    <div class="ban-container">
        <i class="fa-solid fa-gavel"></i>
        <h1>Vous êtes banni</h1>
        <p><strong>Raison:</strong> <?php echo htmlspecialchars($ban['reason']); ?></p>
        <p><strong>Date de fin:</strong> <?php echo htmlspecialchars($ban['ban_end_date']); ?></p>
        <p><strong>Banni par:</strong> <?php echo htmlspecialchars($bannedBy['username']); ?></p>
    </div>
</body>
</html>
