<?php
session_start();

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    setcookie('lang', $lang, time() + (86400 * 30), "/");
} else {
    $lang = $_COOKIE['lang'] ?? 'fr';
}

$search = $search ?? '';

$translations = [
    'fr' => [
        'tickets' => 'Tickets',
        'create_ticket' => 'Créer un ticket',
        'view_tickets' => 'Voir mes tickets',
        'archive_ticket' => 'Archive ticket',
        'support' => 'Support',
        'about_us' => 'Qui sommes-nous',
        'patchnote' => 'Patchnote',
        'search' => 'Rechercher',
        'connect' => 'Connecter',
        'login' => 'Se connecter',
        'register' => 'S\'inscrire',
    ],
    'en' => [
        'tickets' => 'Tickets',
        'create_ticket' => 'Create a ticket',
        'view_tickets' => 'View my tickets',
        'archive_ticket' => 'Archive ticket',
        'support' => 'Support',
        'about_us' => 'About us',
        'patchnote' => 'Patchnote',
        'search' => 'Search',
        'connect' => 'Connect',
        'login' => 'Login',
        'register' => 'Register',
    ],
    'nl' => [
        'tickets' => 'Tickets',
        'create_ticket' => 'Maak een ticket',
        'view_tickets' => 'Bekijk mijn tickets',
        'archive_ticket' => 'Archief ticket',
        'support' => 'Ondersteuning',
        'about_us' => 'Over ons',
        'patchnote' => 'Patchnote',
        'search' => 'Zoeken',
        'connect' => 'Verbinden',
        'login' => 'Inloggen',
        'register' => 'Registreren',
    ],
];
$text = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        @keyframes textHover {
            0% {
                transform: scale(1);
                color: inherit;
            }

            50% {
                transform: scale(1.1);
                color: #22c55e;
            }

            100% {
                transform: scale(1);
                color: inherit;
            }
        }

        .hover-animate {
            transition: all 0.3s ease;
        }

        .hover-animate:hover {
            animation: textHover 0.5s ease-in-out;
        }

        @keyframes textHoverRed {
            0% {
                transform: scale(1);
                color: inherit;
            }

            50% {
                transform: scale(1.1);
                color: #ef4444;
            }

            100% {
                transform: scale(1);
                color: inherit;
            }
        }

        .hover-animate-red {
            transition: all 0.3s ease;
        }

        .hover-animate-red:hover {
            animation: textHoverRed 0.5s ease-in-out;
        }
    </style>
</head>

<body>
    <nav class="bg-gray-800 shadow-md">
        <div class="container mx-auto flex items-center justify-between px-4 py-3">
            <a class="text-white text-xl font-bold uppercase" href="index.php">
                MyTicket
            </a>
            <button class="text-white lg:hidden" type="button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
            <div class="flex items-center space-x-4 relative">
                <div class="relative">
                    <button id="ticketsDropdownButton" class="text-white flex items-center space-x-2 hover:text-gray-300 hover-animate-red" onclick="toggleDropdown('ticketsDropdown', 'ticketsArrow')">
                        <i class="fa-solid fa-ticket text-red-500"></i>
                        <span><?= $text['tickets'] ?></span>
                        <i id="ticketsArrow" class="fas fa-chevron-down"></i>
                    </button>
                    <div id="ticketsDropdown" class="hidden absolute left-1/2 transform -translate-x-1/2 bg-gray-700 text-white rounded-lg shadow-lg mt-4 z-10 w-64">
                        <a href="create_ticket.php" class="block px-6 py-3 hover:bg-gray-600 rounded-t-lg transition-colors flex items-center space-x-2 group">
                            <i class="fas fa-plus-circle text-green-500 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                            <span class="group-hover:text-green-500 transition-colors"><?= $text['create_ticket'] ?></span>
                        </a>
                        <a href="view_tickets.php" class="block px-6 py-3 hover:bg-gray-600 transition-colors flex items-center space-x-2 group">
                            <i class="fas fa-list text-blue-300 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                            <span class="group-hover:text-blue-300 transition-colors"><?= $text['view_tickets'] ?></span>
                        </a>
                        <a href="archive_tickets.php" class="block px-6 py-3 hover:bg-gray-600 rounded-b-lg transition-colors flex items-center space-x-2 group">
                            <i class="fas fa-archive text-red-500 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                            <span class="group-hover:text-red-500 transition-colors"><?= $text['archive_ticket'] ?></span>
                        </a>
                    </div>
                </div>
                <a href="support.php" class="text-white flex items-center space-x-2 hover:text-purple-500 group">
                    <i class="fas fa-headset text-purple-500 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                    <span class="group-hover:text-purple-500 transition-colors"><?= $text['support'] ?></span>
                </a>
                <a href="about_us.php" class="text-white flex items-center space-x-2 hover:text-purple-500 group">
                    <i class="fas fa-users text-purple-500 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                    <span class="group-hover:text-purple-500 transition-colors"><?= $text['about_us'] ?></span>
                </a>
                <a href="patchnote.php" class="text-white flex items-center space-x-2 hover:text-cyan-500 group">
                    <i class="fas fa-newspaper text-cyan-500 group-hover:scale-110 group-hover:rotate-6 transition-transform"></i>
                    <span class="group-hover:text-cyan-500 transition-colors"><?= $text['patchnote'] ?></span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <?php if (!empty($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <button id="profileDropdownButton" class="flex items-center space-x-2 text-white hover:text-gray-300">
                            <i class="fas fa-user-circle text-white text-2xl"></i>
                            <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Profile') ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="profileDropdown" class="hidden absolute right-0 bg-gray-700 text-white rounded-lg shadow-lg mt-2 z-10 w-48">
                            <a href="view_tickets.php" class="block px-4 py-2 hover:bg-gray-600 transition-colors">Mes Tickets</a>
                            
                            <a href="logout.php" class="block px-4 py-2 hover:bg-gray-600 transition-colors">Déconnexion</a>
                        </div>
                    <?php else: ?>
                        <button id="authDropdownButton" class="flex items-center space-x-2 text-white hover:text-gray-300">
                            <i class="fas fa-user-circle text-white text-2xl"></i>
                            <span>Connexion</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div id="authDropdown" class="hidden absolute right-0 bg-gray-700 text-white rounded-lg shadow-lg mt-2 z-10 w-48">
                            <a href="login.php" class="block px-4 py-2 hover:bg-gray-600 transition-colors">Se connecter</a>
                            <a href="register.php?lang=<?= $lang ?>" class="block px-4 py-2 hover:bg-gray-600 transition-colors">S'inscrire</a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="relative">
                    <button id="langDropdownButton" class="text-white flex items-center space-x-2 hover:text-gray-300 hover-animate-red">
                        <span><?= $lang === 'fr' ? '🇫🇷 Fr' : ($lang === 'en' ? '🇬🇧 En' : '🇳🇱 Nl') ?></span>
                        <i id="langArrow" class="fas fa-chevron-down"></i>
                    </button>
                    <div id="langDropdown" class="hidden absolute left-1/2 transform -translate-x-1/2 bg-gray-700 text-white rounded-lg shadow-lg mt-4 z-10 w-32">
                        <form method="GET" class="block">
                            <button name="lang" value="fr" class="block w-full px-4 py-2 text-left hover:bg-gray-600 rounded-t-lg transition-colors flex items-center space-x-2">
                                <span>🇫🇷 Fr</span>
                            </button>
                            <button name="lang" value="en" class="block w-full px-4 py-2 text-left hover:bg-gray-600 transition-colors flex items-center space-x-2">
                                <span>🇬🇧 En</span>
                            </button>
                            <button name="lang" value="nl" class="block w-full px-4 py-2 text-left hover:bg-gray-600 rounded-b-lg transition-colors flex items-center space-x-2">
                                <span>🇳🇱 Nl</span>
                            </button>
                        </form>
                    </div>
    </nav>
    <?php if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true): ?>
        <div id="authModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-gray-800 text-white rounded-lg shadow-lg w-96">
                <div class="flex justify-between items-center border-b border-gray-700 p-4">
                    <h5 class="text-lg font-bold"><?= $text['connect'] ?></h5>
                    <button class="text-gray-400 hover:text-gray-200" onclick="toggleModal('authModal')">&times;</button>
                </div>
                <div class="p-4 flex flex-col space-y-4">
                    <button onclick="window.location.href='login.php'" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600">
                        <?= $text['login'] ?>
                    </button>
                    <button onclick="window.location.href='register.php?lang=<?= $lang ?>'" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600">
                        <?= $text['register'] ?>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        function toggleDropdown(dropdownId, arrowId) {
            const dropdown = document.getElementById(dropdownId);
            const arrow = document.getElementById(arrowId);
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('fa-chevron-down');
            arrow.classList.toggle('fa-chevron-up');
        }

        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        function openPopup() {
            toggleModal('authModal');
        }

        function openAuthChoicePopup() {
            toggleModal('authModal');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const dropdownButton = document.getElementById('ticketsDropdownButton');
            const dropdown = document.getElementById('ticketsDropdown');
            const arrow = document.getElementById('ticketsArrow');

            document.addEventListener('click', (event) => {
                if (!dropdown.contains(event.target) && !dropdownButton.contains(event.target)) {
                    dropdown.classList.add('hidden');
                    arrow.classList.remove('fa-chevron-up');
                    arrow.classList.add('fa-chevron-down');
                }
            });

            dropdownButton.addEventListener('mouseenter', () => {
                dropdown.classList.remove('hidden');
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-up');
            });

            dropdownButton.addEventListener('mouseleave', () => {
                setTimeout(() => {
                    if (!dropdown.matches(':hover')) {
                        dropdown.classList.add('hidden');
                        arrow.classList.remove('fa-chevron-up');
                        arrow.classList.add('fa-chevron-down');
                    }
                }, 100);
            });

            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.add('hidden');
                arrow.classList.remove('fa-chevron-up');
                arrow.classList.add('fa-chevron-down');
            });

            dropdownButton.addEventListener('click', () => {
                dropdown.classList.toggle('hidden');
                arrow.classList.toggle('fa-chevron-down');
                arrow.classList.toggle('fa-chevron-up');
            });

            const langDropdownButton = document.getElementById('langDropdownButton');
            const langDropdown = document.getElementById('langDropdown');
            const langArrow = document.getElementById('langArrow');

            document.addEventListener('click', (event) => {
                if (!langDropdown.contains(event.target) && !langDropdownButton.contains(event.target)) {
                    langDropdown.classList.add('hidden');
                    langArrow.classList.remove('fa-chevron-up');
                    langArrow.classList.add('fa-chevron-down');
                }
            });

            langDropdownButton.addEventListener('click', () => {
                langDropdown.classList.toggle('hidden');
                langArrow.classList.toggle('fa-chevron-down');
                langArrow.classList.toggle('fa-chevron-up');
            });

            const profileDropdownButton = document.getElementById('profileDropdownButton');
            const profileDropdown = document.getElementById('profileDropdown');
            const authDropdownButton = document.getElementById('authDropdownButton');
            const authDropdown = document.getElementById('authDropdown');

            if (profileDropdownButton) {
                document.addEventListener('click', (event) => {
                    if (!profileDropdown.contains(event.target) && !profileDropdownButton.contains(event.target)) {
                        profileDropdown.classList.add('hidden');
                    }
                });

                profileDropdownButton.addEventListener('click', () => {
                    profileDropdown.classList.toggle('hidden');
                });
            }

            if (authDropdownButton) {
                document.addEventListener('click', (event) => {
                    if (!authDropdown.contains(event.target) && !authDropdownButton.contains(event.target)) {
                        authDropdown.classList.add('hidden');
                    }
                });

                authDropdownButton.addEventListener('click', () => {
                    authDropdown.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>

</html>