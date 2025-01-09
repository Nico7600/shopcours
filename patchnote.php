<?php
require_once 'bootstrap.php';

$userName = null;
$isPrime = false;
if (isset($_SESSION['id'])) {
    $sql = 'SELECT fname, is_prime FROM users WHERE id = :id';
    $query = $db->prepare($sql);
    $query->bindValue(':id', $_SESSION['id'], PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userName = $user['fname'];
        $isPrime = (bool)$user['is_prime'];
    }
}

if (!isset($_SESSION['id'])) {
    $_SESSION['erreur'] = "Vous devez être connecté pour accéder à cette page";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $message = $_POST['update'];
    $userId = $_SESSION['id'];
    $sql = 'INSERT INTO updates (user_id, message, created_at, vote) VALUES (:user_id, :message, NOW(), 0)';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':message', $message, PDO::PARAM_STR);
    $query->execute();
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $vote = $_POST['vote'];
    $updateId = $_POST['update_id'];
    $userId = $_SESSION['id'];

    // Check if the user has already voted for this update
    $sql = 'SELECT vote FROM updates WHERE user_id = :user_id AND id = :update_id';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $query->bindValue(':update_id', $updateId, PDO::PARAM_INT);
    $query->execute();
    $existingVote = $query->fetchColumn();

    if ($existingVote === false) {
        // Insert the vote if the user hasn't voted yet
        $sql = 'UPDATE updates SET vote = vote + :vote WHERE id = :update_id';
        $query = $db->prepare($sql);
        $query->bindValue(':vote', $vote, PDO::PARAM_INT);
        $query->bindValue(':update_id', $updateId, PDO::PARAM_INT);
        $query->execute();
    } elseif ($existingVote != $vote) {
        // Update the vote if the user changes their vote
        $sql = 'UPDATE updates SET vote = vote + :vote - :existing_vote WHERE id = :update_id';
        $query = $db->prepare($sql);
        $query->bindValue(':vote', $vote, PDO::PARAM_INT);
        $query->bindValue(':existing_vote', $existingVote, PDO::PARAM_INT);
        $query->bindValue(':update_id', $updateId, PDO::PARAM_INT);
        $query->execute();
    }
}

// Fetch patch notes
$sql = 'SELECT updates.id, updates.message, updates.created_at, users.username, 
        updates.vote as votes 
        FROM updates 
        JOIN users ON updates.user_id = users.id 
        ORDER BY updates.created_at DESC';
$query = $db->prepare($sql);
$query->execute();
$patchNotes = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patch Notes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            background-color: #f8f9fa;
            color: #343a40;
        }
        .developer {
            position: absolute;
            bottom: 10px;
            left: 10px;
            font-weight: bold;
            font-size: 14px;
        }
        .current-date {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 14px;
        }
        .centered-bold {
            text-align: center;
            font-weight: bold;
            font-size: 36px;
            margin-bottom: 30px;
            color: #007bff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .patch-note-container {
            position: relative;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .patch-note-container .card-body {
            position: relative;
        }
        .formatted-text ul {
            padding-left: 20px;
        }
        .formatted-text ul li {
            list-style-type: disc;
            font-size: 18px;
            margin-bottom: 10px;
        }
        .formatted-text .highlight {
            text-align: center;
            font-weight: bold;
            font-size: 22px;
            margin-bottom: 15px;
        }
        .vote-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .vote-buttons .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            transition: transform 0.2s;
        }
        .vote-buttons .btn-success:hover {
            transform: scale(1.2);
        }
        .vote-buttons .btn-danger:hover {
            transform: scale(1.2);
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <a href="index.php" class="home-button"><i class="fas fa-home"></i></a>
    <div class="container mt-5">
        <h1 class="centered-bold">Patch Notes</h1>
        <div class="patch-notes">
            <?php foreach ($patchNotes as $note): ?>
                <div class="card mb-3 patch-note-container">
                    <div class="card-body">
                        <div class="formatted-text">
                            <?php
                            $lines = explode("\n", htmlspecialchars($note['message']));
                            foreach ($lines as $line) {
                                if (strpos($line, '#') === 0) {
                                    echo '<p class="highlight">' . substr($line, 1) . '</p>';
                                } else {
                                    echo '<ul><li>' . $line . '</li></ul>';
                                }
                            }
                            ?>
                        </div>
                        <div class="developer">Développeur : <?php echo htmlspecialchars($note['username']); ?></div>
                        <div class="current-date">Posté le <?php echo htmlspecialchars(date('d/m/Y', strtotime($note['created_at']))); ?> à <?php echo htmlspecialchars(date('H:i:s', strtotime($note['created_at']))); ?></div>
                        <div class="vote-buttons">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="update_id" value="<?php echo $note['id']; ?>">
                                <button type="submit" name="vote" value="1" class="btn btn-success"><i class="fas fa-arrow-up"></i></button>
                            </form>
                            <span>Votes: <?php echo $note['votes']; ?></span>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="update_id" value="<?php echo $note['id']; ?>">
                                <button type="submit" name="vote" value="-1" class="btn btn-danger"><i class="fas fa-arrow-down"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
