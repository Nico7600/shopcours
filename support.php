<?php
include 'header.php';

$supportedLangs = ['fr', 'en', 'nl'];
$lang = $_GET['lang'] ?? $_COOKIE['lang'] ?? 'fr';
if (!in_array($lang, $supportedLangs)) {
    $lang = 'fr'; // Définit le français comme langue par défaut
}
setcookie('lang', $lang, time() + (86400 * 30), "/");

$translations = [
    'fr' => [
        'title' => 'Support',
        'email' => 'Email : support@Mytickets.be',
        'phone' => 'Téléphone : +32 475 12 34 56',
        'address' => 'Adresse : Sq. des Martyrs 1, 6000 Charleroi',
        'email_text' => 'Pour toute question ou assistance, n\'hésitez pas à nous envoyer un email. Nous vous répondrons dans les plus brefs délais.',
        'phone_text' => 'Appelez-nous pour une assistance immédiate. Notre équipe est disponible pour répondre à vos besoins.',
        'address_text' => 'Venez nous rendre visite à notre magasin physique. Nous serons ravis de vous accueillir et de vous aider en personne.',
    ],
    'en' => [
        'title' => 'Support',
        'email' => 'Email: support@Mytickets.be',
        'phone' => 'Phone: +32 475 12 34 56',
        'address' => 'Address: Sq. des Martyrs 1, 6000 Charleroi',
        'email_text' => 'For any questions or assistance, feel free to send us an email. We will respond as quickly as possible.',
        'phone_text' => 'Call us for immediate assistance. Our team is available to meet your needs.',
        'address_text' => 'Visit us at our physical store. We will be happy to welcome you and assist you in person.',
    ],
    'nl' => [
        'title' => 'Ondersteuning',
        'email' => 'E-mail: support@Mytickets.be',
        'phone' => 'Telefoon: +32 475 12 34 56',
        'address' => 'Adres: Sq. des Martyrs 1, 6000 Charleroi',
        'email_text' => 'Voor vragen of hulp kunt u ons een e-mail sturen. We zullen zo snel mogelijk reageren.',
        'phone_text' => 'Bel ons voor directe hulp. Ons team staat klaar om aan uw behoeften te voldoen.',
        'address_text' => 'Bezoek ons in onze fysieke winkel. We verwelkomen u graag en helpen u persoonlijk verder.',
    ],
];

$text = $translations[$lang];
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
        --blue: #3b82f6;
        --gray-light: #e5e5e5;
        --hover-green: #22c55e;
        --white: #ffffff;
    }

    body {
        font-family: 'Orbitron', sans-serif;
        background-color: #1a202c;
        color: var(--white);
    }

    .section-container {
        padding: 2rem;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        margin-bottom: 2rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .section-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
    }

    .icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .icon-email {
        color: var(--blue);
    }

    .icon-phone {
        color: var(--green);
    }

    .icon-address {
        color: var(--red);
    }

    iframe {
        border: 0;
        width: 100%;
        height: 300px;
        border-radius: 15px;
        margin-top: 1rem;
    }

    .font-bold {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .text-center {
        text-align: center;
    }

    .text-lg {
        font-size: 1rem;
        line-height: 1.5;
    }
</style>

<section class="text-center py-12 bg-gradient-to-r from-purple-dark via-purple to-cyan-light text-white">
    <h1 class="text-4xl font-bold"><?= $text['title'] ?></h1>
</section>

<section class="section-container text-center">
    <div>
        <i class="fas fa-envelope icon icon-email"></i>
        <p class="font-bold">
            <a href="mailto:support@Mytickets.be" class="hover:underline"><?= $text['email'] ?></a>
        </p>
        <p class="text-lg"><?= $text['email_text'] ?></p>
    </div>
</section>

<section class="section-container text-center">
    <div>
        <i class="fas fa-phone-alt icon icon-phone"></i>
        <p class="font-bold"><?= $text['phone'] ?></p>
        <p class="text-lg"><?= $text['phone_text'] ?></p>
    </div>
</section>

<section class="section-container text-center">
    <div>
        <i class="fas fa-map-marker-alt icon icon-address"></i>
        <p class="font-bold"><?= $text['address'] ?></p>
        <p class="text-lg"><?= $text['address_text'] ?></p>
        <div class="flex flex-col md:flex-row justify-between items-start">
            <div class="text-left md:w-1/2">
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Lundi :</span> 8h - 12h30, 14h - 20h</p>
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Mardi :</span> 8h - 12h30, 14h - 19h</p>
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Mercredi :</span> 14h - 20h</p>
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Jeudi :</span> 8h - 12h30, 14h - 20h</p>
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Vendredi :</span> 9h - 13h30, 15h - 21h</p>
                <p><i class="fas fa-calendar-alt text-red-400"></i> <span class="font-bold">Samedi :</span> Fermé</p>
                <p><i class="fas fa-calendar-alt text-blue-400"></i> <span class="font-bold">Dimanche :</span> 10h - 12h30</p>
            </div>
            <div class="md:w-1/2 mt-4 md:mt-0 md:ml-4">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2530.9739376345801!2d4.43941865654716!3d50.40517694644672!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c23c0b6f6f6f6f%3A0x123456789abcdef!2sSq.%20des%20Martyrs%201%2C%206000%20Charleroi!5e0!3m2!1sfr!2sbe!4v1696445960462!5m2!1sfr!2sbe" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="w-full h-64 md:h-full rounded-lg">
                </iframe>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>