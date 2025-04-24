<?php
session_start();

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dsn = 'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'] . ';charset=utf8';
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

$id_perm = (int)$_SESSION['user']['id_perm'];
if (!in_array($id_perm, [2, 3, 4, 5])) {
    header('Location: index.php');
    exit();
}

$languages = ['en' => 'English', 'fr' => 'Français', 'nl' => 'Nederlands'];
$selected_lang = $_GET['lang'] ?? 'fr';
if (!array_key_exists($selected_lang, $languages)) {
    $selected_lang = 'fr';
}

$translations = [
    'fr' => [
        'welcome' => 'Bienvenue, Administrateur',
        'navigate' => 'Utilisez le menu ci-dessus pour naviguer entre les différentes sections.',
        'dashboard' => 'Tableau de bord',
        'users' => 'Gestion des utilisateurs',
        'settings' => 'Paramètres',
    ],
    'en' => [
        'welcome' => 'Welcome, Administrator',
        'navigate' => 'Use the menu above to navigate between different sections.',
        'dashboard' => 'Dashboard',
        'users' => 'User Management',
        'settings' => 'Settings',
    ],
    'nl' => [
        'welcome' => 'Welkom, Beheerder',
        'navigate' => 'Gebruik het menu hierboven om tussen de verschillende secties te navigeren.',
        'dashboard' => 'Dashboard',
        'users' => 'Gebruikersbeheer',
        'settings' => 'Instellingen',
    ],
];

$trans = $translations[$selected_lang];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


    <style>
        :root {
            --purple-dark: #4c1d95;
            --red: #ef4444;
            --green: #22c55e;
            --purple: #6d28d9;
            --cyan-light: #a5f3fc;
            --gray-light: #e5e5e5;
            --hover-green: #22c55e;
            --white: #ffffff;
        }

        body {
            background-color: #1a202c;
            font-family: 'Orbitron', sans-serif;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-transform: uppercase;
            color: var(--cyan-light);
        }

        .section-container {
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .text-gray-light {
            color: var(--gray-light);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
       <?php include 'header.php'; ?>
        <main class="flex-grow container mx-auto py-8">
            <div class="section-container">
                <h2 class="section-title"><?= htmlspecialchars($trans['welcome']) ?></h2>
                <p class="text-gray-light"><?= htmlspecialchars($trans['navigate']) ?></p>
                <div class="mt-6">
                    <a href="dashboard.php" class="card bg-purple text-white px-4 py-2 rounded hover:bg-purple-dark">
                        <i class="fas fa-tachometer-alt"></i> <?= htmlspecialchars($trans['dashboard']) ?>
                    </a>
                    <a href="users.php" class="card bg-green text-white px-4 py-2 rounded hover:bg-hover-green ml-2">
                        <i class="fas fa-users"></i> <?= htmlspecialchars($trans['users']) ?>
                    </a>
                    <a href="settings.php" class="card bg-red text-white px-4 py-2 rounded hover:bg-purple ml-2">
                        <i class="fas fa-tools"></i> <?= htmlspecialchars($trans['settings']) ?>
                    </a>
                </div>
            </div>
        </main>
        <?php include 'footer.php'; ?>
    </div>
</body>
</html>
