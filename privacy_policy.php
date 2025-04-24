<?php
$lang = $_GET['lang'] ?? 'fr';

$privacy_content = [
    'fr' => [
        'title' => 'Politique de Confidentialité',
        'last_update' => 'Dernière mise à jour : 30/03/2025',
        'intro' => 'Bienvenue sur MyTicket. Nous accordons une grande importance à la protection de votre vie privée et à la sécurité de vos données personnelles. Cette politique de confidentialité explique quelles informations nous collectons, comment nous les utilisons et quels sont vos droits à cet égard.',
        'sections' => [
            '1. Collecte des Informations' => [
                'Informations personnelles (nom, adresse e-mail, numéro de téléphone, etc.)',
                'Informations de connexion et de navigation (adresse IP, type de navigateur, temps de visite, etc.)',
                'Données relatives aux transactions (en cas d\'achat ou de paiement sur notre site)',
                'Toute autre information que vous choisissez de nous fournir.'
            ],
            '2. Utilisation des Informations' => [
                'Fournir et améliorer nos services',
                'Personnaliser votre expérience utilisateur',
                'Répondre à vos demandes et vous fournir un support',
                'Assurer la sécurité et prévenir la fraude',
                'Respecter nos obligations légales et réglementaires.'
            ],
            '3. Partage des Informations' => [
                'Nous ne vendons ni ne louons vos données personnelles à des tiers. Cependant, nous pouvons partager certaines informations avec :',
                'Nos prestataires de services pour assurer le bon fonctionnement de notre site',
                'Les autorités légales si la loi l\'exige',
                'Nos partenaires en cas de fusion, acquisition ou restructuration.'
            ],
            '4. Sécurité des Données' => [
                'Nous mettons en place des mesures de sécurité appropriées pour protéger vos informations contre tout accès non autorisé, altération ou divulgation.'
            ],
            '5. Vos Droits' => [
                'Conformément à la réglementation en vigueur, vous avez le droit de :',
                'Accéder à vos données personnelles',
                'Demander la rectification ou la suppression de vos données',
                'Vous opposer à leur traitement ou en demander la limitation',
                'Retirer votre consentement à tout moment',
                'Demander la portabilité de vos données.'
            ],
            '6. Cookies et Technologies Similaires' => [
                'Nous utilisons des cookies pour améliorer votre expérience et analyser l\'utilisation de notre site. Vous pouvez gérer vos préférences en matière de cookies via les paramètres de votre navigateur.'
            ],
            '7. Modifications de la Politique de Confidentialité' => [
                'Nous nous réservons le droit de modifier cette politique à tout moment. Toute modification sera publiée sur cette page avec la date de mise à jour.'
            ],
            '8. Contact' => [
                'Pour toute question relative à cette politique ou à vos données personnelles, vous pouvez nous contacter à : [Adresse e-mail ou formulaire de contact].'
            ]
        ]
    ],
    'en' => [
        'title' => 'Privacy Policy',
        'last_update' => 'Last updated: 30/03/2025',
        'intro' => 'Welcome to MyTicket. We highly value the protection of your privacy and the security of your personal data. This privacy policy explains what information we collect, how we use it, and your rights regarding it.',
        'sections' => [
            '1. Information Collection' => [
                'Personal information (name, email address, phone number, etc.)',
                'Login and browsing information (IP address, browser type, visit duration, etc.)',
                'Transaction data (in case of purchase or payment on our site)',
                'Any other information you choose to provide us.'
            ],
            '2. Use of Information' => [
                'Provide and improve our services',
                'Personalize your user experience',
                'Respond to your requests and provide support',
                'Ensure security and prevent fraud',
                'Comply with our legal and regulatory obligations.'
            ],
            '3. Information Sharing' => [
                'We do not sell or rent your personal data to third parties. However, we may share certain information with:',
                'Our service providers to ensure the proper functioning of our site',
                'Legal authorities if required by law',
                'Our partners in case of merger, acquisition, or restructuring.'
            ],
            '4. Data Security' => [
                'We implement appropriate security measures to protect your information from unauthorized access, alteration, or disclosure.'
            ],
            '5. Your Rights' => [
                'In accordance with current regulations, you have the right to:',
                'Access your personal data',
                'Request the rectification or deletion of your data',
                'Object to their processing or request a limitation',
                'Withdraw your consent at any time',
                'Request the portability of your data.'
            ],
            '6. Cookies and Similar Technologies' => [
                'We use cookies to enhance your experience and analyze the use of our site. You can manage your cookie preferences through your browser settings.'
            ],
            '7. Changes to the Privacy Policy' => [
                'We reserve the right to modify this policy at any time. Any changes will be posted on this page with the update date.'
            ],
            '8. Contact' => [
                'For any questions regarding this policy or your personal data, you can contact us at: [Email address or contact form].'
            ]
        ]
    ],
    'nl' => [
        'title' => 'Privacybeleid',
        'last_update' => 'Laatst bijgewerkt: 30/03/2025',
        'intro' => 'Welkom bij MyTicket. Wij hechten veel waarde aan de bescherming van uw privacy en de veiligheid van uw persoonlijke gegevens. Dit privacybeleid legt uit welke informatie we verzamelen, hoe we deze gebruiken en wat uw rechten zijn.',
        'sections' => [
            '1. Informatie Verzameling' => [
                'Persoonlijke informatie (naam, e-mailadres, telefoonnummer, etc.)',
                'Inlog- en browse-informatie (IP-adres, browsertype, bezoektijd, etc.)',
                'Transactiegegevens (bij aankoop of betaling op onze site)',
                'Alle andere informatie die u ervoor kiest om ons te verstrekken.'
            ],
            '2. Gebruik van Informatie' => [
                'Onze diensten leveren en verbeteren',
                'Uw gebruikerservaring personaliseren',
                'Reageren op uw verzoeken en ondersteuning bieden',
                'Zorgen voor veiligheid en fraude voorkomen',
                'Voldoen aan onze wettelijke en regelgevende verplichtingen.'
            ],
            '3. Informatie Delen' => [
                'We verkopen of verhuren uw persoonlijke gegevens niet aan derden. We kunnen echter bepaalde informatie delen met:',
                'Onze dienstverleners om de goede werking van onze site te waarborgen',
                'Wettelijke autoriteiten indien wettelijk vereist',
                'Onze partners in geval van fusie, overname of herstructurering.'
            ],
            '4. Gegevensbeveiliging' => [
                'We implementeren passende beveiligingsmaatregelen om uw informatie te beschermen tegen ongeautoriseerde toegang, wijziging of openbaarmaking.'
            ],
            '5. Uw Rechten' => [
                'In overeenstemming met de huidige regelgeving heeft u het recht om:',
                'Toegang te krijgen tot uw persoonlijke gegevens',
                'Rectificatie of verwijdering van uw gegevens aan te vragen',
                'Bezwaar te maken tegen de verwerking ervan of een beperking aan te vragen',
                'Uw toestemming op elk moment in te trekken',
                'De overdraagbaarheid van uw gegevens aan te vragen.'
            ],
            '6. Cookies en Vergelijkbare Technologieën' => [
                'We gebruiken cookies om uw ervaring te verbeteren en het gebruik van onze site te analyseren. U kunt uw cookievoorkeuren beheren via uw browserinstellingen.'
            ],
            '7. Wijzigingen in het Privacybeleid' => [
                'We behouden ons het recht voor om dit beleid op elk moment te wijzigen. Eventuele wijzigingen worden op deze pagina geplaatst met de update datum.'
            ],
            '8. Contact' => [
                'Voor vragen over dit beleid of uw persoonlijke gegevens kunt u contact met ons opnemen via: [E-mailadres of contactformulier].'
            ]
        ]
    ]
];
$privacy = $privacy_content[$lang] ?? $privacy_content['fr'];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $privacy['title'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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

        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
            animation-fill-mode: both;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 font-sans" style="font-family: 'Orbitron', sans-serif;">
    <?php include 'header.php'; ?>
    <main class="p-8 bg-gray-800 shadow-lg">
        <section class="mt-8">
            <h1 class="text-2xl font-bold text-blue-400 text-center"><?= $privacy['title'] ?></h1>
            <p class="mt-4 text-gray-300 text-center"><?= $privacy['last_update'] ?></p>
            <p class="mt-4 text-gray-300"><?= $privacy['intro'] ?></p>

            <?php
            $delay = 0;
            foreach ($privacy['sections'] as $section_title => $section_content):
            ?>
                <div class="mt-6 p-4 bg-gray-700 rounded-lg shadow-md fade-in-up" style="animation-delay: <?= $delay ?>s;">
                    <h2 class="text-xl font-semibold text-blue-300 text-center"><?= $section_title ?></h2>
                    <?php if (is_array($section_content)): ?>
                        <ul class="list-disc ml-6 mt-2 text-gray-300">
                            <?php foreach ($section_content as $item): ?>
                                <li><?= $item ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mt-2 text-gray-300"><?= $section_content ?></p>
                    <?php endif; ?>
                </div>
                <?php $delay += 0.3;
                ?>
            <?php endforeach; ?>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>

</html>