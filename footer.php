<?php
$lang = $_GET['lang'] ?? 'fr';

$valid_languages = ['fr', 'en', 'nl'];
if (!in_array($lang, $valid_languages)) {
    $lang = 'fr';
}

$translations = [
    'fr' => [
        'copyright' => 'Tous droits réservés',
        'privacy_policy' => 'Politique de confidentialité',
        'terms_of_service' => 'Conditions d\'utilisation',
    ],
    'en' => [
        'copyright' => 'All rights reserved',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
    ],
    'nl' => [
        'copyright' => 'Alle rechten voorbehouden',
        'privacy_policy' => 'Privacybeleid',
        'terms_of_service' => 'Gebruiksvoorwaarden',
    ],
];
$text = $translations[$lang];
?>
<footer class="bg-gray-800 text-white py-4">
    <div class="container mx-auto text-center">
        <p class="text-sm"><?= $text['copyright'] ?> &copy; <?= date('Y') ?> MyTicket</p>
        <div class="flex justify-center space-x-4 mt-2">
            <a href="privacy_policy.php?lang=<?= $lang ?>" class="hover:underline"><?= $text['privacy_policy'] ?></a>
            <a href="terms_of_use.php?lang=<?= $lang ?>" class="hover:underline"><?= $text['terms_of_service'] ?></a>
        </div>
    </div>
</footer>