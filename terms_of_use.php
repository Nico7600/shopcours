<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conditions d'Utilisation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .fade-in-container {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease-in-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-container:nth-child(1) {
            animation-delay: 0.2s;
        }

        .fade-in-container:nth-child(2) {
            animation-delay: 0.4s;
        }

        .fade-in-container:nth-child(3) {
            animation-delay: 0.6s;
        }

        .fade-in-container:nth-child(4) {
            animation-delay: 0.8s;
        }

        .fade-in-container:nth-child(5) {
            animation-delay: 1s;
        }

        .fade-in-container:nth-child(6) {
            animation-delay: 1.2s;
        }

        .fade-in-container:nth-child(7) {
            animation-delay: 1.4s;
        }

        .fade-in-container:nth-child(8) {
            animation-delay: 1.6s;
        }

        .fade-in-container:nth-child(9) {
            animation-delay: 1.8s;
        }

        .fade-in-container:nth-child(10) {
            animation-delay: 2s;
        }

        .fade-in-container:nth-child(11) {
            animation-delay: 2.2s;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 font-sans" style="font-family: 'Orbitron', sans-serif;">
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
    <?php include 'header.php'; ?>
    <main class="p-8 bg-gray-800 shadow-lg">
        <section class="lang-section" data-lang="fr" style="<?= $lang === 'fr' ? 'display: block;' : 'display: none;' ?>">
            <h1 class="text-3xl font-bold text-blue-400 text-center">Conditions d'Utilisation</h1>
            <p class="mt-4 text-gray-300 text-center">Dernière mise à jour : 30/03/2025</p>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-circle-info text-blue-400 mr-2"></i> 1. Objet du Service
                </h2>
                <p class="mt-4 text-gray-300">
                    MyTicket permet aux utilisateurs de créer, gérer et partager des tickets pour divers événements ou services. Notre plateforme met à disposition un outil de conception et de gestion de tickets, mais n'est pas responsable de la gestion des événements eux-mêmes.
                </p>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-lock text-green-400 mr-2"></i> 2. Accès au Service
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>L'accès à certaines fonctionnalités du site peut nécessiter la création d'un compte utilisateur.</li>
                    <li>Vous êtes responsable de la confidentialité de vos identifiants de connexion et de toute activité réalisée sous votre compte.</li>
                    <li>Nous nous réservons le droit de suspendre ou de supprimer un compte en cas de non-respect des présentes conditions.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-check text-yellow-400 mr-2"></i> 3. Utilisation Acceptable
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>Vous acceptez d'utiliser le site uniquement à des fins légales et conformes aux réglementations en vigueur.</li>
                    <li>Il est interdit d'utiliser le service pour créer des tickets pour des événements frauduleux, illégaux ou trompeurs.</li>
                    <li>Tout contenu injurieux, discriminatoire, diffamatoire ou contraire à l'éthique est strictement interdit.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-shield text-red-400 mr-2"></i> 4. Responsabilité et Garantie
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>MyTicket agit uniquement en tant que plateforme technique et décline toute responsabilité quant à la véracité des informations fournies par les utilisateurs.</li>
                    <li>Nous ne garantissons pas un service ininterrompu ou exempt d'erreurs et nous nous réservons le droit de modifier ou d'interrompre tout ou partie du service à tout moment.</li>
                    <li>L'utilisateur est seul responsable des données qu'il génère et partage via notre plateforme.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-copyright text-purple-400 mr-2"></i> 5. Propriété Intellectuelle
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>Tous les éléments du site (logo, design, interface, logiciels, etc.) sont protégés par les lois sur la propriété intellectuelle et ne peuvent être copiés ou reproduits sans notre autorisation écrite.</li>
                    <li>Les utilisateurs conservent la propriété des tickets qu’ils créent, mais accordent à MyTicket une licence non exclusive pour l'affichage et la promotion de leurs créations sur la plateforme.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-user-shield text-cyan-400 mr-2"></i> 6. Protection des Données Personnelles
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> Nous nous engageons à respecter la confidentialité des informations personnelles des utilisateurs conformément aux lois en vigueur sur la protection des données.</li>
                    <li> Pour plus d’informations sur la collecte et l’utilisation de vos données, veuillez consulter notre Politique de Confidentialité.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-pen text-orange-400 mr-2"></i> 7. Modification des Conditions
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> Nous nous réservons le droit de modifier ces conditions d’utilisation à tout moment. Les utilisateurs seront informés des mises à jour via le site.</li>
                    <li> En continuant à utiliser le service après une modification des conditions, vous acceptez les nouvelles dispositions.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-gavel text-gray-400 mr-2"></i> 8. Droit Applicable et Litiges
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> Ces conditions sont régies par les lois en vigueur dans le pays où MyTicket est enregistré.</li>
                    <li> En cas de litige, une tentative de résolution à l’amiable sera privilégiée avant toute action judiciaire.</li>
                </ul>
            </div>
        </section>

        <section class="lang-section" data-lang="en" style="<?= $lang === 'en' ? 'display: block;' : 'display: none;' ?>">
            <h1 class="text-3xl font-bold text-blue-400 text-center">Terms of Use</h1>
            <p class="mt-4 text-gray-300 text-center">Last updated: 30/03/2025</p>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-circle-info text-blue-400 mr-2"></i> 1. Service Purpose
                </h2>
                <p class="mt-4 text-gray-300">
                    MyTicket allows users to create, manage, and share tickets for various events or services. Our platform provides a tool for ticket design and management but is not responsible for managing the events themselves.
                </p>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-lock text-green-400 mr-2"></i> 2. Access to the Service
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>Access to certain features of the site may require the creation of a user account.</li>
                    <li>You are responsible for the confidentiality of your login credentials and any activity conducted under your account.</li>
                    <li>We reserve the right to suspend or delete an account in case of non-compliance with these terms.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-check text-yellow-400 mr-2"></i> 3. Acceptable Use
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>You agree to use the site only for lawful purposes and in compliance with applicable regulations.</li>
                    <li>It is prohibited to use the service to create tickets for fraudulent, illegal, or misleading events.</li>
                    <li>Any abusive, discriminatory, defamatory, or unethical content is strictly prohibited.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-shield text-red-400 mr-2"></i> 4. Liability and Warranty
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>MyTicket acts solely as a technical platform and disclaims any responsibility for the accuracy of information provided by users.</li>
                    <li>We do not guarantee uninterrupted or error-free service and reserve the right to modify or discontinue all or part of the service at any time.</li>
                    <li>The user is solely responsible for the data they generate and share through our platform.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-copyright text-purple-400 mr-2"></i> 5. Intellectual Property
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>All elements of the site (logo, design, interface, software, etc.) are protected by intellectual property laws and may not be copied or reproduced without our written permission.</li>
                    <li>Users retain ownership of the tickets they create but grant MyTicket a non-exclusive license to display and promote their creations on the platform.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-user-shield text-cyan-400 mr-2"></i> 6. Personal Data Protection
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> We are committed to respecting the confidentiality of users' personal information in accordance with applicable data protection laws.</li>
                    <li> For more information on the collection and use of your data, please refer to our Privacy Policy.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-pen text-orange-400 mr-2"></i> 7. Modification of Terms
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> We reserve the right to modify these terms of use at any time. Users will be informed of updates via the site.</li>
                    <li> By continuing to use the service after a modification of the terms, you accept the new provisions.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-gavel text-gray-400 mr-2"></i> 8. Applicable Law and Disputes
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> These terms are governed by the laws in force in the country where MyTicket is registered.</li>
                    <li> In case of dispute, an attempt at amicable resolution will be preferred before any legal action.</li>
                </ul>
            </div>
        </section>

        <section class="lang-section" data-lang="nl" style="<?= $lang === 'nl' ? 'display: block;' : 'display: none;' ?>">
            <h1 class="text-3xl font-bold text-blue-400 text-center">Gebruiksvoorwaarden</h1>
            <p class="mt-4 text-gray-300 text-center">Laatst bijgewerkt: 30/03/2025</p>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-circle-info text-blue-400 mr-2"></i> 1. Doel van de Dienst
                </h2>
                <p class="mt-4 text-gray-300">
                    MyTicket stelt gebruikers in staat om tickets te maken, beheren en delen voor verschillende evenementen of diensten. Ons platform biedt een tool voor ticketontwerp en -beheer, maar is niet verantwoordelijk voor het beheer van de evenementen zelf.
                </p>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-lock text-green-400 mr-2"></i> 2. Toegang tot de Dienst
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>Toegang tot bepaalde functies van de site kan het aanmaken van een gebruikersaccount vereisen.</li>
                    <li>U bent verantwoordelijk voor de vertrouwelijkheid van uw inloggegevens en alle activiteiten die onder uw account worden uitgevoerd.</li>
                    <li>We behouden ons het recht voor om een account op te schorten of te verwijderen in geval van niet-naleving van deze voorwaarden.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-check text-yellow-400 mr-2"></i> 3. Acceptabel Gebruik
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>U stemt ermee in de site alleen te gebruiken voor legale doeleinden en in overeenstemming met de toepasselijke regelgeving.</li>
                    <li>Het is verboden de service te gebruiken om tickets te maken voor frauduleuze, illegale of misleidende evenementen.</li>
                    <li>Elk beledigend, discriminerend, lasterlijk of onethisch inhoud is strikt verboden.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-shield text-red-400 mr-2"></i> 4. Aansprakelijkheid en Garantie
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>MyTicket fungeert uitsluitend als een technisch platform en wijst elke verantwoordelijkheid af voor de juistheid van de door gebruikers verstrekte informatie.</li>
                    <li>We garanderen geen ononderbroken of foutloze service en behouden ons het recht voor om de gehele of een deel van de service op elk moment te wijzigen of stop te zetten.</li>
                    <li>De gebruiker is als enige verantwoordelijk voor de gegevens die hij genereert en deelt via ons platform.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-copyright text-purple-400 mr-2"></i> 5. Intellectuele Eigendom
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li>Alle elementen van de site (logo, ontwerp, interface, software, enz.) zijn beschermd door intellectuele eigendomswetten en mogen niet worden gekopieerd of gereproduceerd zonder onze schriftelijke toestemming.</li>
                    <li>Gebruikers behouden het eigendom van de tickets die ze maken, maar verlenen MyTicket een niet-exclusieve licentie om hun creaties op het platform weer te geven en te promoten.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-user-shield text-cyan-400 mr-2"></i> 6. Bescherming van Persoonsgegevens
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> We verbinden ons ertoe de vertrouwelijkheid van de persoonlijke informatie van gebruikers te respecteren in overeenstemming met de toepasselijke wetgeving inzake gegevensbescherming.</li>
                    <li> Voor meer informatie over het verzamelen en gebruiken van uw gegevens, raadpleegt u ons Privacybeleid.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-pen text-orange-400 mr-2"></i> 7. Wijziging van de Voorwaarden
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> We behouden ons het recht voor om deze gebruiksvoorwaarden op elk moment te wijzigen. Gebruikers worden via de site op de hoogte gebracht van updates.</li>
                    <li>Door de service te blijven gebruiken na een wijziging van de voorwaarden, accepteert u de nieuwe bepalingen.</li>
                </ul>
            </div>

            <div class="mt-6 p-6 bg-gray-700 rounded-lg shadow-md fade-in-container">
                <h2 class="text-2xl font-semibold text-blue-300 text-center">
                    <i class="fa-solid fa-gavel text-gray-400 mr-2"></i> 8. Toepasselijk Recht en Geschillen
                </h2>
                <ul class="list-disc ml-6 mt-4 text-gray-300">
                    <li> Deze voorwaarden worden beheerst door de wetten die van kracht zijn in het land waar MyTicket is geregistreerd.</li>
                    <li> In geval van een geschil zal een poging tot minnelijke schikking worden geprefereerd voordat juridische stappen worden ondernomen.</li>
                </ul>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>

</html>