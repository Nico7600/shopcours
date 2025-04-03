<?php
try {
    $pdo = new PDO('mysql:host=nicolavnicolas.mysql.db;dbname=nicolavnicolas', 'nicolavnicolas', 'Rex220405');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$search = $search ?? '';
$tag = $tag ?? '';

$query = $pdo->query('SELECT badge, COUNT(*) as count FROM liste GROUP BY badge ORDER BY badge ASC');
$tags = [];
$productCounts = [];
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $tags[$row['badge']] = 'fas fa-tag'; // Remplacez par les icônes appropriées
    $productCounts[$row['badge']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
            background-color: #343a40;
            color: white;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .navbar-brand .valo {
            color: #ff5733;
        }

        .navbar-brand .mazone {
            color: #33c1ff;
        }

        .nav-link, .dropdown-item, .btn-outline-light, .btn-outline-success, .search-container input[type="search"], .search-container select, .search-container button {
            color: #ffffff; /* Set text color to white */
        }

        .nav-link {
            font-size: 1rem;
            font-weight: 400;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 0.5rem;
        }

        .nav-link i {
            color: #33c1ff;
        }

        .nav-link:hover {
            background-color: #ff5733;
            color: #ff5733;
            border-radius: 0.5rem;
            animation: bounce 1.5s; /* Slower bounce animation */
        }

        .navbar-nav .nav-item .nav-link:hover {
            background-color: #ff5733;
            color: #ffffff;
            border-radius: 0.5rem;
            animation: bounce 1.5s; /* Slower bounce animation */
        }

        .dropdown-item {
            font-size: 0.9rem;
            font-weight: 400;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 0.5rem;
        }

        .dropdown-item i {
            color: #33c1ff;
            margin-right: 5px; /* Add some spacing between the icon and text */
        }

        .dropdown-item:hover {
            background-color: #ff5733; /* Match hover color */
            color: white; /* Ensure text is visible */
            border-radius: 0.5rem;
            animation: bounce 1.5s; /* Slower bounce animation */
        }

        .btn-outline-light, .btn-outline-success {
            font-size: 1rem;
            font-weight: 700;
            border-color: #33c1ff;
            transition: background-color 0.3s ease, color 0.3s ease;
            border-radius: 0.5rem;
            margin-left: 5px; /* Reduce spacing between buttons */
        }

        .btn-outline-light:hover, .btn-outline-success:hover {
            background-color: #33c1ff;
            color: #ffffff;
            border-radius: 0.5rem;
        }

        .dropdown-item-admin {
            color: blue;
        }

        .dropdown-menu-end {
            right: 0;
            left: auto;
            transform: translateX(-10%);
            background-color: #343a40;
        }

        .search-container {
            display: flex;
            align-items: center;
            margin-right: 10px; /* Add spacing to the right of the search container */
        }

        .search-container input[type="search"] {
            margin-right: 10px;
            padding-left: 30px; /* Add padding to make space for the icon */
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" class="bi bi-search" viewBox="0 0 16 16"> <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.442 1.398a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/> </svg>');
            background-repeat: no-repeat;
            background-position: 10px center;
        }

        .search-container select {
            background-color: #343a40;
        }

        .search-container select option {
            display: flex;
            align-items: center;
        }

        .search-container select option i {
            margin-right: 5px;
            color: #33c1ff;
        }

        .search-container select:focus {
            background-color: #343a40;
        }

        .search-container button {
            margin-left: 5px; 
        }

        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }

            .search-container input[type="search"],
            .search-container select,
            .search-container button {
                margin-bottom: 0.5rem;
                width: 100%;
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .navbar,
        .dropdown-menu,
        .search-container select,
        .search-container input[type="search"],
        .search-container button {
            color: black; /* Ensure text is visible on these elements */
        }

        .dropdown-menu {
            background-color: #343a40; 
            color: white;
        }

        .search-container select,
        .search-container input[type="search"] {
            background-color: #ffffff; /* Light background for better contrast */
        }

        .search-container button {
            background-color: #ffffff; /* Light background for better contrast */
        }

        .form-control {
            transition: background-color 0.5s ease, color 0.5s ease; /* Slower transition for form fields */
        }

        .form-control:focus {
            background-color: #ffffff;
            color: #000000;
        }

        .navbar-nav .nav-item {
            margin-left: 5px; /* Reduce spacing between navigation links */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-uppercase" href="index.php">
                <span class="valo">Valo</span><span class="mazone">mazone</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="cart_view.php">
                            <i class="fas fa-shopping-cart"></i> Panier
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">
                            <i class="fas fa-headset"></i> Support
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="patchnote.php">
                            <i class="fas fa-scroll"></i> Patch Notes
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="primeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-crown"></i> Prime
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="primeDropdown">
                            <li><a class="dropdown-item" href="prime.php"><i class="fas fa-gem"></i> Prime</a></li>
                            <li><a class="dropdown-item" href="prime_advantages.php"><i class="fas fa-star"></i> Avantage</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="privacy_policy.php">
                            <i class="fas fa-file-alt"></i> CGU
                        </a>
                    </li>
                </ul>
            </div>

            <div class="search-container ms-auto me-2">
                <form class="form-inline my-2 my-lg-0" method="GET" action="index.php">
                    <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher" name="search" value="<?= htmlspecialchars($search ?? ''); ?>">
                    <select class="form-control mr-sm-2" name="tag">
                        <option value="">Type</option>
                        <?php foreach ($tags as $tagOption => $iconClass): ?>
                            <option value="<?= htmlspecialchars($tagOption); ?>" <?= $tag == $tagOption ? 'selected' : ''; ?> <?= $productCounts[$tagOption] == 0 ? 'disabled' : ''; ?>>
                                <i class="<?= htmlspecialchars($iconClass); ?>"></i> <?= htmlspecialchars(ucfirst($tagOption)); ?> (<?= $productCounts[$tagOption]; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit" <?= array_sum($productCounts) == 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="dropdown ms-auto me-2">
                <?php if (isset($userName)): ?>
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($userName); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="order_history.php">
                                <i class="fas fa-history"></i> Historique d'achat</a></li>
                        <li><a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user"></i> Profile</a></li>
                        <?php if (isset($_SESSION['id']) && isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                            <li><a class="dropdown-item dropdown-item-admin" href="admin.php">
                                    <i class="fas fa-user-shield"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Se déconnecter</a></li>
                    </ul>
                <?php else: ?>
                    <button type="button" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-sign-in-alt"></i> Connecter
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>