<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistance en Ligne</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
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

        .fade-in {
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .text-animate {
            opacity: 0;
            transform: translateY(20px);
            animation: textFadeIn 1s ease-in-out forwards;
        }

        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .text-animate-delay {
            animation-delay: 0.5s;
        }

        .icon-ticket {
            color: var(--white);
        }

        .icon-list {
            color: var(--green);
        }

        .icon-support {
            color: var(--purple);
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
    </style>
</head>
<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>
<?php
session_start();

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

$translations = [
    'fr' => [
        'welcome' => 'Bienvenue sur MyTicket',
        'service' => 'Service de ticket support',
        'our_services' => 'Nos Services',
        'create_ticket' => 'Créer un Ticket',
        'create_ticket_desc' => 'Soumettez un nouveau ticket pour obtenir de l\'aide.',
        'view_tickets' => 'Voir les Tickets',
        'view_tickets_desc' => 'Consultez vos tickets existants et leur statut.',
        'support' => 'Support',
        'support_desc' => 'Contactez notre équipe pour toute assistance.',
        'information' => 'Informations',
        'about_us' => 'Qui sommes-nous',
        'about_us_desc' => 'Découvrez notre équipe et notre mission.',
        'patch_notes' => 'Patch-Notes',
        'patch_notes_desc' => 'Restez informé des dernières mises à jour et améliorations.',
        'title' => 'Connexion',
        'email' => 'Email',
        'password' => 'Mot de passe',
        'login' => 'Se connecter',
        'db_error' => 'Erreur de connexion à la base de données : ',
        'query_error' => 'Erreur de requête : ',
        'login_success' => 'Connexion réussie !',
        'login_error' => 'Email ou mot de passe incorrect.',
        'fill_fields' => 'Veuillez remplir tous les champs.',
    ],
    'en' => [
        'welcome' => 'Welcome to MyTicket',
        'service' => 'Ticket support service',
        'our_services' => 'Our Services',
        'create_ticket' => 'Create a Ticket',
        'create_ticket_desc' => 'Submit a new ticket for assistance.',
        'view_tickets' => 'View Tickets',
        'view_tickets_desc' => 'Check your existing tickets and their status.',
        'support' => 'Support',
        'support_desc' => 'Contact our team for any assistance.',
        'information' => 'Information',
        'about_us' => 'About Us',
        'about_us_desc' => 'Learn about our team and mission.',
        'patch_notes' => 'Patch Notes',
        'patch_notes_desc' => 'Stay informed about the latest updates and improvements.',
        'title' => 'Login',
        'email' => 'Email',
        'password' => 'Password',
        'login' => 'Log in',
        'db_error' => 'Database connection error: ',
        'query_error' => 'Query error: ',
        'login_success' => 'Login successful!',
        'login_error' => 'Incorrect email or password.',
        'fill_fields' => 'Please fill in all fields.',
    ],
    'nl' => [
        'welcome' => 'Welkom bij MyTicket',
        'service' => 'Ticket ondersteuningsdienst',
        'our_services' => 'Onze Diensten',
        'create_ticket' => 'Maak een Ticket',
        'create_ticket_desc' => 'Dien een nieuw ticket in voor hulp.',
        'view_tickets' => 'Bekijk Tickets',
        'view_tickets_desc' => 'Controleer uw bestaande tickets en hun status.',
        'support' => 'Ondersteuning',
        'support_desc' => 'Neem contact op met ons team voor hulp.',
        'information' => 'Informatie',
        'about_us' => 'Over Ons',
        'about_us_desc' => 'Leer meer over ons team en onze missie.',
        'patch_notes' => 'Patch Notes',
        'patch_notes_desc' => 'Blijf op de hoogte van de nieuwste updates en verbeteringen.',
        'title' => 'Inloggen',
        'email' => 'E-mail',
        'password' => 'Wachtwoord',
        'login' => 'Inloggen',
        'db_error' => 'Database verbindingsfout: ',
        'query_error' => 'Query fout: ',
        'login_success' => 'Succesvol ingelogd!',
        'login_error' => 'Onjuiste e-mail of wachtwoord.',
        'fill_fields' => 'Vul alle velden in.',
    ],
];
$text = $translations[$lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        require_once __DIR__ . '/register_handler.php';

        $dbHost = getEnvOrFail('DB_HOST');
        $dbUsername = getEnvOrFail('DB_USER');
        $dbPassword = getEnvOrFail('DB_PASSWORD');
        $dbDatabase = getEnvOrFail('DB_NAME');

        $mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbDatabase);

        if ($mysqli->connect_error) {
            echo '<p class="text-red-500 text-center">' . $text['db_error'] . $mysqli->connect_error . '</p>';
            exit;
        }

        $stmt = $mysqli->prepare("SELECT id, password FROM user WHERE email = ?");
        if (!$stmt) {
            echo '<p class="text-red-500 text-center">' . $text['query_error'] . $mysqli->error . '</p>';
            exit;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();

        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit;
        } else {
            echo "<p class='text-red-500 text-center'>" . ($text['login_error'] ?? 'Login error') . "</p>";
        }

        $stmt->close();
        $mysqli->close();
    } else {
        echo "<p class='text-red-500 text-center'>" . ($text['fill_fields'] ?? 'Please fill in all fields.') . "</p>";
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<body class="text-gray-100 fade-in">
    <?php if ($isLoggedIn): ?>
        <p class="text-center text-green-500">Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</p>
    <?php endif; ?>
    <main class="p-4">
        <section class="text-center py-12 bg-gradient-to-r from-purple-dark via-purple to-cyan-light text-white">
            <h1 class="text-4xl font-bold mb-4 text-animate"><?= $text['welcome'] ?></h1>
            <p class="text-lg text-animate text-animate-delay"><?= $text['service'] ?></p>
        </section>
        <div id="authChoicePopup" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="overlay absolute inset-0 bg-black opacity-50" onclick="closeAuthChoicePopup()"></div>
            <div class="popup relative bg-gray-800 p-6 rounded-lg shadow-lg z-10">
                <h1 class="text-3xl font-bold mb-6 text-center text-white">
                    <?= $lang === 'fr' ? 'Bienvenue' : ($lang === 'en' ? 'Welcome' : 'Welkom'); ?>
                </h1>
                <p class="text-center text-gray-300 mb-6">
                    <?= $lang === 'fr' ? 'Choisissez une option pour continuer' : ($lang === 'en' ? 'Choose an option to continue' : 'Kies een optie om verder te gaan'); ?>
                </p>
                <div class="flex flex-col space-y-4">
                    <button onclick="openLoginPopup()" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600">
                        <?= $lang === 'fr' ? 'Connexion' : ($lang === 'en' ? 'Log in' : 'Inloggen'); ?>
                    </button>
                    <button onclick="openRegisterPopup()" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                        <?= $lang === 'fr' ? 'Créer un compte' : ($lang === 'en' ? 'Create an account' : 'Maak een account aan'); ?>
                    </button>
                </div>
                <button onclick="closeAuthChoicePopup()" class="mt-4 w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600">
                    <?= $lang === 'fr' ? 'Fermer' : ($lang === 'en' ? 'Close' : 'Sluiten'); ?>
                </button>
            </div>
        </div>
        <div id="loginPopup" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="overlay absolute inset-0 bg-black opacity-50" onclick="closeLoginPopup()"></div>
            <div class="popup relative bg-gray-800 p-6 rounded-lg shadow-lg z-10">
                <h1 class="text-3xl font-bold mb-6 text-center text-white"><?= $text['title'] ?></h1>
                <form method="POST" action="?lang=<?= $lang ?>">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-300"><?= $text['email'] ?></label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-300"><?= $text['password'] ?></label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"><?= $text['login'] ?></button>
                </form>
                <button onclick="closeLoginPopup()" class="mt-4 w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600">
                    <?= $lang === 'fr' ? 'Fermer' : ($lang === 'en' ? 'Close' : 'Sluiten'); ?>
                </button>
            </div>
        </div>
        <div id="registerPopup" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div class="overlay absolute inset-0 bg-black opacity-50" onclick="closeRegisterPopup()"></div>
            <div class="popup relative bg-gray-800 p-6 rounded-lg shadow-lg z-10">
                <h1 class="text-3xl font-bold mb-6 text-center text-white">
                    <?= $lang === 'fr' ? 'Créer un compte' : ($lang === 'en' ? 'Create an account' : 'Maak een account aan'); ?>
                </h1>
                <form method="POST" action="register_handler.php">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Nom' : ($lang === 'en' ? 'Name' : 'Naam'); ?>
                        </label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Email' : ($lang === 'en' ? 'Email' : 'E-mail'); ?>
                        </label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Numéro de téléphone' : ($lang === 'en' ? 'Phone Number' : 'Telefoonnummer'); ?>
                        </label>
                        <input type="tel" id="phone" name="phone" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Photo de profil' : ($lang === 'en' ? 'Profile Picture' : 'Profielfoto'); ?>
                        </label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Mot de passe' : ($lang === 'en' ? 'Password' : 'Wachtwoord'); ?>
                        </label>
                        <input type="password" id="password" name="password" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-300">
                            <?= $lang === 'fr' ? 'Confirmer le mot de passe' : ($lang === 'en' ? 'Confirm Password' : 'Bevestig wachtwoord'); ?>
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                        <?= $lang === 'fr' ? "S'inscrire" : ($lang === 'en' ? 'Sign up' : 'Aanmelden'); ?>
                    </button>
                </form>
                <button onclick="closeRegisterPopup()" class="mt-4 w-full bg-red-500 text-white py-2 px-4 rounded-md hover:bg-red-600">
                    <?= $lang === 'fr' ? 'Fermer' : ($lang === 'en' ? 'Close' : 'Sluiten'); ?>
                </button>
            </div>
        </div>
        <section class="section-container mt-8">
            <h2 class="section-title text-center"><?= $text['our_services'] ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="create_ticket.php" class="card bg-gray-800 shadow-md p-6 text-center block transform transition duration-200 hover:-translate-y-2 hover:shadow-lg active:translate-y-1 active:shadow-md">
                    <i class="fa-solid fa-ticket text-red-500 text-4xl mb-4"></i>
                    <h2 class="text-xl font-semibold mb-2 text-animate"><?= $text['create_ticket'] ?></h2>
                    <p class="text-gray-400 text-animate text-animate-delay"><?= $text['create_ticket_desc'] ?></p>
                </a>
                <a href="view_tickets.php" class="card bg-gray-800 shadow-md p-6 text-center block transform transition duration-200 hover:-translate-y-2 hover:shadow-lg active:translate-y-1 active:shadow-md">
                    <i class="fas fa-list text-4xl mb-4 icon-list"></i>
                    <h2 class="text-xl font-semibold mb-2 text-animate"><?= $text['view_tickets'] ?></h2>
                    <p class="text-gray-400 text-animate text-animate-delay"><?= $text['view_tickets_desc'] ?></p>
                </a>
                <a href="support.php" class="card bg-gray-800 shadow-md p-6 text-center block transform transition duration-200 hover:-translate-y-2 hover:shadow-lg active:translate-y-1 active:shadow-md">
                    <i class="fas fa-headset text-4xl mb-4 icon-support"></i>
                    <h2 class="text-xl font-semibold mb-2 text-animate"><?= $text['support'] ?></h2>
                    <p class="text-gray-400 text-animate text-animate-delay"><?= $text['support_desc'] ?></p>
                </a>
            </div>
        </section>
        <section class="section-container mt-8">
            <h2 class="section-title text-center"><?= $text['information'] ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="about_us.php" class="card bg-gray-800 shadow-md p-6 text-center block transform transition duration-200 hover:-translate-y-2 hover:shadow-lg active:translate-y-1 active:shadow-md">
                    <i class="fas fa-users text-4xl mb-4 text-purple-500"></i>
                    <h2 class="text-xl font-semibold mb-2 text-animate"><?= $text['about_us'] ?></h2>
                    <p class="text-gray-400 text-animate text-animate-delay"><?= $text['about_us_desc'] ?></p>
                </a>
                <a href="news.php" class="card bg-gray-800 shadow-md p-6 text-center block transform transition duration-200 hover:-translate-y-2 hover:shadow-lg active:translate-y-1 active:shadow-md">
                    <i class="fas fa-newspaper text-4xl mb-4 text-cyan-500"></i>
                    <h2 class="text-xl font-semibold mb-2 text-animate"><?= $text['patch_notes'] ?></h2>
                    <p class="text-gray-400 text-animate text-animate-delay"><?= $text['patch_notes_desc'] ?></p>
                </a>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
    <script>
        function openAuthChoicePopup() {
            document.getElementById('authChoicePopup').classList.remove('hidden');
        }

        function closeAuthChoicePopup() {
            document.getElementById('authChoicePopup').classList.add('hidden');
        }

        function openLoginPopup() {
            document.getElementById('loginPopup').classList.remove('hidden');
        }

        function closeLoginPopup() {
            document.getElementById('loginPopup').classList.add('hidden');
        }

        function openRegisterPopup() {
            closeAuthChoicePopup();
            document.getElementById('registerPopup').classList.remove('hidden');
        }

        function closeRegisterPopup() {
            document.getElementById('registerPopup').classList.add('hidden');
        }
    </script>
</body>

</html>