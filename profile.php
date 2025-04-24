<?php
session_start();

require_once 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s', $_ENV['DB_HOST'], $_ENV['DB_NAME']),
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if (!$pdo) {
    die('Impossible de se connecter à la base de données.');
}

try {
    $stmt = $pdo->query('SELECT 1 FROM users LIMIT 1');
} catch (PDOException $e) {
    die('Erreur : La table "users" n\'est pas accessible. ' . $e->getMessage());
}

$translations = [
    'fr' => [
        'title' => 'Modifier le profil',
        'name' => 'Nom',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'profile_picture' => 'Image de profil',
        'current_password' => 'Mot de passe actuel',
        'new_password' => 'Nouveau mot de passe',
        'update' => 'Mettre à jour',
        'current_password_incorrect' => 'Mot de passe actuel incorrect.',
        'profile_updated' => 'Profil mis à jour avec succès.',
        'profile_update_error' => 'Erreur lors de la mise à jour du profil : ',
        'statistics' => 'Statistiques',
        'total_tickets' => 'Tickets Total',
        'open_tickets' => 'Tickets Ouverts',
        'closed_tickets' => 'Tickets Fermés',
    ],
    'en' => [
        'title' => 'Edit Profile',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'profile_picture' => 'Profile Picture',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'update' => 'Update',
        'current_password_incorrect' => 'Current password is incorrect.',
        'profile_updated' => 'Profile updated successfully.',
        'profile_update_error' => 'Error updating profile: ',
        'statistics' => 'Statistics',
        'total_tickets' => 'Total Tickets',
        'open_tickets' => 'Open Tickets',
        'closed_tickets' => 'Closed Tickets',
    ],
    'nl' => [
        'title' => 'Profiel bewerken',
        'name' => 'Naam',
        'email' => 'E-mail',
        'phone' => 'Telefoon',
        'profile_picture' => 'Profielfoto',
        'current_password' => 'Huidig wachtwoord',
        'new_password' => 'Nieuw wachtwoord',
        'update' => 'Bijwerken',
        'current_password_incorrect' => 'Huidig wachtwoord is onjuist.',
        'profile_updated' => 'Profiel succesvol bijgewerkt.',
        'profile_update_error' => 'Fout bij het bijwerken van het profiel: ',
        'statistics' => 'Statistieken',
        'total_tickets' => 'Totaal Tickets',
        'open_tickets' => 'Open Tickets',
        'closed_tickets' => 'Gesloten Tickets',
    ],
];

$lang = $_GET['lang'] ?? 'fr';
if (!array_key_exists($lang, $translations)) {
    $lang = 'fr';
}
$t = $translations[$lang];

include 'header.php';

$currentUser = null;
if (isset($_SESSION['user']['id'])) {
    try {
        $stmt = $pdo->prepare('
            SELECT 
                id, 
                name, 
                email, 
                phone, 
                profile_picture, 
                ticket_count, 
                open_ticket_count, 
                closed_ticket_count
            FROM users
            WHERE id = :id
        ');
        $stmt->execute(['id' => $_SESSION['user']['id']]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Erreur lors de la récupération des informations utilisateur : ' . $e->getMessage();
    }
}

$userCredentials = null;
if (isset($_SESSION['user']['id'])) {
    try {
        $stmt = $pdo->prepare('SELECT name, password FROM users WHERE id = :id');
        $stmt->execute(['id' => $_SESSION['user']['id']]);
        $userCredentials = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Erreur lors de la récupération des informations de connexion : ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE name = :name');
        $stmt->execute(['name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newUsername = $_POST['name'] ?? '';
    $newEmail = $_POST['email'] ?? '';
    $newPhone = $_POST['phone'] ?? '';
    $profilePicture = $_FILES['profile_picture'] ?? null;
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['password'] ?? '';

    try {
        // Vérification du mot de passe actuel
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = :user_id');
        $stmt->execute(['user_id' => $_SESSION['user']['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            echo $t['current_password_incorrect'];
            return;
        }

        $updateQuery = 'UPDATE users SET ';
        $params = [];

        if ($newUsername) {
            $updateQuery .= 'name = :new_name, ';
            $params['new_name'] = $newUsername;
        }

        if ($newEmail) {
            $updateQuery .= 'email = :new_email, ';
            $params['new_email'] = $newEmail;
        }

        if ($newPhone) {
            $updateQuery .= 'phone = :new_phone, ';
            $params['new_phone'] = $newPhone;
        }

        if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            $uploadFile = $uploadDir . basename($profilePicture['name']);
            if (move_uploaded_file($profilePicture['tmp_name'], $uploadFile)) {
                if (!empty($currentUser['profile_picture']) && file_exists(__DIR__ . '/' . $currentUser['profile_picture'])) {
                    unlink(__DIR__ . '/' . $currentUser['profile_picture']);
                }
                $updateQuery .= 'profile_picture = :profile_picture, ';
                $params['profile_picture'] = 'uploads/' . basename($profilePicture['name']);
            }
        }

        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery .= 'password = :password, ';
            $params['password'] = $hashedPassword;
        }

        $updateQuery = rtrim($updateQuery, ', ') . ' WHERE id = :user_id';
        $params['user_id'] = $_SESSION['user']['id'];

        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute($params);

        echo $t['profile_updated'];
    } catch (PDOException $e) {
        echo $t['profile_update_error'] . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($t['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
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

        .form-container {
            background-color: #2d3748;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            color: #63b3ed;
        }

        .form-input {
            padding: 0.75rem;
            font-size: 1rem;
        }
    </style>
</head>

<body class="text-gray-100 flex flex-col min-h-screen">
    <div class="flex-grow">
        <div class="max-w-4xl mx-auto mt-10 form-container">
            <h1 class="text-3xl font-bold mb-6 text-center"><?php echo htmlspecialchars($t['statistics']); ?></h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h2 class="text-xl font-bold text-cyan-400"><?php echo htmlspecialchars($t['total_tickets']); ?></h2>
                    <p class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($currentUser['ticket_count'] ?? 0); ?></p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h2 class="text-xl font-bold text-green-400"><?php echo htmlspecialchars($t['open_tickets']); ?></h2>
                    <p class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($currentUser['open_ticket_count'] ?? 0); ?></p>
                </div>
                <div class="bg-gray-800 p-4 rounded-lg shadow-md text-center">
                    <h2 class="text-xl font-bold text-red-400"><?php echo htmlspecialchars($t['closed_tickets']); ?></h2>
                    <p class="text-2xl font-semibold text-white"><?php echo htmlspecialchars($currentUser['closed_ticket_count'] ?? 0); ?></p>
                </div>
            </div>
        </div>
        <div class="max-w-4xl mx-auto mt-10 form-container mb-10">
            <h1 class="text-3xl font-bold mb-6 text-center"><?php echo htmlspecialchars($t['title']); ?></h1>
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['name']); ?></label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" class="form-input mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['email']); ?></label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" class="form-input mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['phone']); ?></label>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" class="form-input mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <label for="profile_picture" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['profile_picture']); ?></label>
                    <?php if (!empty($currentUser['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($currentUser['profile_picture']); ?>" alt="<?php echo htmlspecialchars($t['profile_picture']); ?>" class="w-20 h-20 rounded-full mb-4">
                    <?php endif; ?>
                    <input type="file" name="profile_picture" id="profile_picture" class="form-input mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['current_password']); ?></label>
                    <input type="password" name="current_password" id="current_password" placeholder="<?php echo htmlspecialchars($t['current_password']); ?>" class="form-input mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300"><?php echo htmlspecialchars($t['new_password']); ?></label>
                    <input type="password" name="password" id="password" placeholder="<?php echo htmlspecialchars($t['new_password']); ?>" class="form-input mt-1 block w-full bg-gray-700 text-white border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit" name="update_profile" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600"><?php echo htmlspecialchars($t['update']); ?></button>
                </div>
            </form>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>

</html>