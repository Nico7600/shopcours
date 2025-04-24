<?php
$supportedLangs = ['fr', 'en', 'nl'];
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, $supportedLangs)) {
        setcookie('lang', $lang, time() + (86400 * 30), "/");
    } else {
        $lang = $_COOKIE['lang'] ?? 'fr';
    }
} else {
    $lang = $_COOKIE['lang'] ?? 'fr';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription réussie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1a202c;
            font-family: 'Orbitron', sans-serif;
        }
    </style>
</head>

<body class="text-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg text-center">
        <h1 class="text-4xl font-bold text-green-500 mb-4">Inscription réussie !</h1>
        <p class="text-lg text-gray-300 mb-6">Merci de vous être inscrit. Vous pouvez maintenant 
            <a href="login.php" class="text-blue-400 hover:underline">vous connecter</a>.
        </p>
        <i class="fas fa-check-circle text-green-500 text-6xl"></i>
    </div>
</body>

</html>
