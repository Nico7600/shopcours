<?php
include 'header.php';

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
        font-family: 'Orbitron', sans-serif;
        background-color: #1a202c;
        color: var(--white);
    }

    .text-container {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.8s ease forwards;
    }

    .text-container:nth-child(1) {
        animation-delay: 0.2s;
    }

    .text-container:nth-child(2) {
        animation-delay: 0.4s;
    }

    .text-container:nth-child(3) {
        animation-delay: 0.6s;
    }

    .text-container:nth-child(4) {
        animation-delay: 0.8s;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }
</style>

<?php
$translations = [
    'fr' => [
        'title' => 'À propos de nous',
        'welcome' => 'Bienvenue chez MyTicket ! Nous nous engageons à offrir un service client exceptionnel.',
        'card1' => 'Depuis 2025, nous mettons notre expertise au service de tout le monde, en proposant des solutions adaptées à leurs besoins. Notre mission est de recueillir vos besoins à l\'aide de tickets auxquels nous répondrons dans les plus brefs délais.',
        'card2' => 'Notre équipe est composée de 4 personnes, et nous croyons fermement en l’innovation, la durabilité, la proximité avec nos clients.',
        'card3' => 'Chez MyTicket, nous avançons avec une seule ambition : satisfaire chaque client.',
        'card4' => 'Basés à Charleroi, nous sommes fiers de collaborer avec vous.',
        'closing' => 'Nous serions ravis de vous accompagner ! Contactez-nous dès aujourd’hui et découvrez ce que nous pouvons faire ensemble.',
    ],
    'en' => [
        'title' => 'About Us',
        'welcome' => 'Welcome to MyTicket! We are committed to providing exceptional customer service.',
        'card1' => 'Since 2025, we have been leveraging our expertise to serve everyone by offering solutions tailored to their needs. Our mission is to collect your needs through tickets, which we will address as quickly as possible.',
        'card2' => 'Our team consists of 4 people, and we strongly believe in innovation, sustainability, and proximity to our clients.',
        'card3' => 'At MyTicket, we move forward with one ambition: to satisfy every customer.',
        'card4' => 'Based in Charleroi, we are proud to collaborate with you.',
        'closing' => 'We would be delighted to assist you! Contact us today and discover what we can achieve together.',
    ],
    'nl' => [
        'title' => 'Over Ons',
        'welcome' => 'Welkom bij MyTicket! Wij zetten ons in voor uitstekende klantenservice.',
        'card1' => 'Sinds 2025 zetten wij onze expertise in om iedereen van dienst te zijn door oplossingen op maat aan te bieden. Onze missie is om uw behoeften te verzamelen via tickets, die we zo snel mogelijk zullen behandelen.',
        'card2' => 'Ons team bestaat uit 4 personen, en we geloven sterk in innovatie, duurzaamheid en nabijheid tot onze klanten.',
        'card3' => 'Bij MyTicket gaan we vooruit met één ambitie: elke klant tevreden stellen.',
        'card4' => 'Gevestigd in Charleroi, zijn we trots om met u samen te werken.',
        'closing' => 'We helpen u graag! Neem vandaag nog contact met ons op en ontdek wat we samen kunnen bereiken.',
    ],
];

$text = $translations[$lang];
?>

<section class="text-center py-12 bg-gradient-to-r from-purple-dark via-purple to-cyan-light text-white">
    <div class="text-container">
        <h1 class="text-4xl font-bold mb-4 text-animate"><?= $text['title'] ?></h1>
        <p class="text-lg text-animate text-animate-delay"><?= $text['welcome'] ?></p>
    </div>
</section>

<section class="section-container mt-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card shadow-md p-6 text-center">
            <i class="fas fa-briefcase text-4xl mb-4" style="color: var(--purple);"></i>
            <div class="text-container">
                <p><?= $text['card1'] ?></p>
            </div>
        </div>
        <div class="card shadow-md p-6 text-center">
            <i class="fas fa-users text-4xl mb-4" style="color: var(--green);"></i>
            <div class="text-container">
                <p><?= $text['card2'] ?></p>
            </div>
        </div>
        <div class="card shadow-md p-6 text-center">
            <i class="fas fa-bullseye text-4xl mb-4" style="color: var(--red);"></i>
            <div class="text-container">
                <p><?= $text['card3'] ?></p>
            </div>
        </div>
        <div class="card shadow-md p-6 text-center">
            <i class="fas fa-map-marker-alt text-4xl mb-4" style="color: var(--cyan-light);"></i>
            <div class="text-container">
                <p><?= $text['card4'] ?></p>
            </div>
        </div>
    </div>
</section>

<section class="text-center mt-8 mb-12">
    <div class="text-container">
        <p class="text-lg"><?= $text['closing'] ?></p>
    </div>
</section>

<?php
include 'footer.php';
?>