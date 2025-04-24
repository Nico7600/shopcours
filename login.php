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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE name = :name');
        $stmt->execute(['name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Tous les identifiants sont corrects
            $_SESSION['logged_in'] = true;
$_SESSION['user'] = [
        'id' => $user['id'], // <-- ajoute ça !
        'name' => $user['name'],
        'id_perm' => $user['id_perm']
    ];            header('Location: index.php');
            exit;
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    } catch (PDOException $e) {
        if ($_ENV['APP_ENV'] === 'development') {
            $error = 'Erreur lors de la vérification des identifiants : ' . $e->getMessage();
        } else {
            $error = 'Erreur lors de la vérification des identifiants.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-900">
    <div class="max-w-md w-full bg-gray-800 p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center mb-6 text-cyan-400">Connexion</h1>
        <?php if (!empty($error)): ?>
            <div class="bg-red-500 text-white p-4 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-300">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="mt-1 block w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Mot de passe</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full px-4 py-2 bg-gray-700 text-white border border-gray-600 rounded-md shadow-sm focus:ring-cyan-500 focus:border-cyan-500" required>
            </div>
            <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">Se connecter</button>
        </form>
        <p class="mt-4 text-center text-sm text-gray-400">
            Pas encore inscrit ? <a href="register.php" class="text-cyan-400 hover:underline">Créer un compte</a>
        </p>
    </div>
</body>

</html>		