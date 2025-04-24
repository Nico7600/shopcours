<?php
session_start();

// Charger les variables d'environnement depuis le fichier .env
require_once 'vendor/autoload.php'; // Assurez-vous que le package vlucas/phpdotenv est installé
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    // Connexion à la base de données avec les informations du fichier .env
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s', $_ENV['DB_HOST'], $_ENV['DB_NAME']),
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $profile_picture = null;

    if ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (!empty($_FILES['profile_picture']['name'])) {
        // Gestion de l'upload de l'image
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        // Vérifiez si le fichier a été correctement téléchargé
        if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Erreur lors du téléchargement de l\'image. Code d\'erreur : ' . $_FILES['profile_picture']['error'];
        } elseif (!in_array($imageFileType, $allowed_types)) {
            $error = 'Seules les images JPG, JPEG et PNG sont autorisées.';
        } elseif (!is_uploaded_file($_FILES['profile_picture']['tmp_name'])) {
            $error = 'Le fichier téléchargé est invalide.';
        } elseif (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $error = 'Erreur lors du déplacement de l\'image vers le dossier de destination.';
        } else {
            $profile_picture = $target_file;
        }
    }

    if (empty($error)) {
        try {
            // Vérifiez si l'utilisateur ou l'email existe déjà
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE name = :name OR email = :email');
            $stmt->execute(['name' => $name, 'email' => $email]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Nom ou email déjà utilisé.';
            } else {
                // Insérez l'utilisateur dans la base de données
                $stmt = $pdo->prepare('
                    INSERT INTO users (name, email, phone, id_perm, password, ticket_count, open_ticket_count, closed_ticket_count, profile_picture, created_at, updated_at)
                    VALUES (:name, :email, :phone, :id_perm, :password, :ticket_count, :open_ticket_count, :closed_ticket_count, :profile_picture, NOW(), NOW())
                ');
                $stmt->execute([
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'id_perm' => 1, // Par défaut, l'utilisateur a un rôle de base
                    'password' => password_hash($password, PASSWORD_BCRYPT),
                    'ticket_count' => 0,
                    'open_ticket_count' => 0,
                    'closed_ticket_count' => 0,
                    'profile_picture' => $profile_picture ?: 'uploads/default.png', // Par défaut, une image générique
                ]);

                // Connectez l'utilisateur après l'inscription
                $_SESSION['logged_in'] = true;
$_SESSION['user'] = [
    'id' => $userId,
    'name' => $name,
    'id_perm' => 1
];                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Erreur lors de l\'inscription : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #1a202c;
            /* Couleur de fond de l'index */
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.05);
            /* Couleur des sections de l'index */
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .form-title {
            color: #a5f3fc;
            /* Couleur cyan clair utilisée dans l'index */
        }

        .form-label {
            color: #e5e5e5;
            /* Couleur gris clair */
        }

        .form-input {
            background-color: #2d3748;
            /* Couleur de fond des champs */
            border-color: rgba(255, 255, 255, 0.1);
            /* Bordure légère */
            color: #ffffff;
            /* Texte blanc */
        }

        .form-button {
            background-color: #22c55e;
            /* Vert utilisé dans l'index */
        }

        .form-button:hover {
            background-color: #16a34a;
            /* Vert plus foncé pour le hover */
        }

        .form-link {
            color: #6d28d9;
            /* Violet utilisé dans l'index */
        }

        .form-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body class="flex items-center justify-center h-screen">
    <div class="form-container p-8 w-96">
        <h1 class="form-title text-2xl font-bold mb-4">Inscription</h1>
        <?php if (!empty($error)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="register.php" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="form-label block">Nom</label>
                <input type="text" id="name" name="name" class="form-input w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="email" class="form-label block">Adresse email</label>
                <input type="email" id="email" name="email" class="form-input w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="form-label block">Numéro de téléphone</label>
                <input type="text" id="phone" name="phone" class="form-input w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label block">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-input w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="form-label block">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-input w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="profile_picture" class="form-label block">Image de profil</label>
                <input type="file" id="profile_picture" name="profile_picture" class="form-input w-full px-4 py-2 border rounded-lg">
            </div>
            <button type="submit" class="form-button w-full text-white py-2 px-4 rounded-lg">S'inscrire</button>
        </form>
        <p class="mt-4 text-gray-400">Déjà inscrit ? <a href="login.php" class="form-link">Se connecter</a></p>
    </div>
</body>

</html>	