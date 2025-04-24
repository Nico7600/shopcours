<?php
$supportedLangs = ['fr', 'en', 'nl'];
$lang = $_COOKIE['lang'] ?? 'fr';
$translations = [
    'fr' => [
        'support_title' => 'Support',
        'support_intro' => 'Nous sommes là pour vous aider. Veuillez remplir le formulaire ci-dessous.',
        'name' => 'Nom',
        'email' => 'Email',
        'message' => 'Message',
        'submit' => 'Envoyer',
        'contact' => 'Contact',
        'messages' => 'Messages',
    ],
    'en' => [
        'support_title' => 'Support',
        'support_intro' => 'We are here to help. Please fill out the form below.',
        'name' => 'Name',
        'email' => 'Email',
        'message' => 'Message',
        'submit' => 'Submit',
        'contact' => 'Contact',
        'messages' => 'Messages',
    ],
    'nl' => [
        'support_title' => 'Ondersteuning',
        'support_intro' => 'Wij zijn hier om te helpen. Vul het onderstaande formulier in.',
        'name' => 'Naam',
        'email' => 'E-mail',
        'message' => 'Bericht',
        'submit' => 'Verzenden',
        'contact' => 'Contact',
        'messages' => 'Berichten',
    ],
];
$text = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $text['support_title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .section-container {
            padding: 2rem;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body class="text-gray-100 fade-in flex flex-col min-h-screen">
    <?php include 'header.php'; ?>
    <main class="p-4 flex-grow">
        <section class="text-center py-12 bg-gradient-to-r from-purple-dark via-purple to-cyan-light text-white max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold mb-4 text-animate"><?= $text['support_title'] ?></h1>
            <p class="text-lg text-animate text-animate-delay"><?= $text['support_intro'] ?></p>
        </section>
        <section class="section-container mt-8">
            <form method="POST" action="support_handler.php" class="bg-gray-800 p-6 rounded-lg shadow-lg max-w-sm mx-auto">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-300"><?= $text['name'] ?>Contact</label>
                    <input type="text" id="name" name="name" placeholder="<?= $text['contact'] ?>" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-300"><?= $text['email'] ?>Adresse mail</label>
                    <input type="email" id="email" name="email" placeholder="<?= $text['email'] ?>" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-gray-300"><?= $text['message'] ?>Message</label>
                    <textarea id="message" name="message" rows="4" placeholder="<?= $text['message'] ?>" class="mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600"><?= $text['submit'] ?>Envoyez</button>
            </form>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>

</html>