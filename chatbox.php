
<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message) && isset($_SESSION['id'])) {
        $sql = 'INSERT INTO chatbox (user_id, message, created_at) VALUES (:user_id, :message, NOW())';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $_SESSION['id'], PDO::PARAM_INT);
        $query->bindValue(':message', $message, PDO::PARAM_STR);
        if ($query->execute()) {
            $_SESSION['message'] = 'Message sent successfully';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to send message';
            $_SESSION['message_type'] = 'danger';
        }
    } else {
        $_SESSION['message'] = 'Message cannot be empty';
        $_SESSION['message_type'] = 'warning';
    }
    header('Location: chatbox.php');
    exit;
}

$sql = 'SELECT c.*, u.fname FROM chatbox c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC';
$query = $db->prepare($sql);
$query->execute();
$messages = $query->fetchAll(PDO::FETCH_ASSOC);

require_once 'close.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        .chatbox {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chatbox-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .chatbox-messages {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .chatbox-message {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .chatbox-message:last-child {
            border-bottom: none;
        }
        .chatbox-message .username {
            font-weight: bold;
        }
        .chatbox-message .timestamp {
            font-size: 0.8rem;
            color: #888;
        }
        .chatbox-form {
            display: flex;
        }
        .chatbox-form input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }
        .chatbox-form button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #fff;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        .chatbox-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['message_type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>
    <div class="chatbox">
        <div class="chatbox-header">
            <h1>Chatbox</h1>
        </div>
        <div class="chatbox-messages">
            <?php foreach ($messages as $message): ?>
                <div class="chatbox-message">
                    <div class="username"><?= htmlspecialchars($message['fname']) ?></div>
                    <div class="timestamp"><?= htmlspecialchars($message['created_at']) ?></div>
                    <div class="message"><?= htmlspecialchars($message['message']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <form class="chatbox-form" method="post" action="chatbox.php">
            <input type="text" name="message" placeholder="Type your message here...">
            <button type="submit">Send</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>