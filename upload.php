
<?php
// On démarre une session
session_start();

// On inclut la connexion à la base
require_once('connect.php');

// Check if a category is selected
$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category) {
    $sql = 'SELECT * FROM `liste` WHERE `badge` = :category';
    $query = $db->prepare($sql);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
} else {
    $sql = 'SELECT * FROM `liste`';
    $query = $db->prepare($sql);
}

// On exécute la requête
$query->execute();

// On stocke le résultat dans un tableau associatif
$result = $query->fetchAll(PDO::FETCH_ASSOC);

// Mélanger les résultats pour un affichage aléatoire
shuffle($result);

require_once('close.php');

// ...existing code...

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image_produit']) && $_FILES['image_produit']['error'] == 0) {
        $uploadDir = 'image_produit/';
        $uploadFile = $uploadDir . basename($_FILES['image_produit']['name']);

        // Check if file already exists
        if (file_exists($uploadFile)) {
            echo "Sorry, file already exists.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['image_produit']['tmp_name'], $uploadFile)) {
                echo "The file " . htmlspecialchars(basename($_FILES['image_produit']['name'])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// ...existing code...
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #343a40;
            padding: 5px 20px; /* Reduce padding to decrease height */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            color: #ffffff;
            font-size: 1.5rem; /* Slightly smaller font size */
            font-weight: bold;
            transition: color 0.3s;
        }
        .navbar-brand:hover {
            color: #ff9900;
        }
        .nav-link {
            color: #ffffff;
            font-size: 1rem; /* Slightly smaller font size */
            margin-right: 10px; /* Reduce margin */
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #ff9900;
        }
        .btn-outline-success, .btn-outline-light, .dark-mode-toggle button {
            color: #ffffff;
            border-color: #ff9900;
            padding: 5px 10px; /* Reduce padding */
            cursor: pointer;
            transition: background-color 0.4s, color 0.4s;
            font-size: 1rem; /* Ensure same font size */
        }
        .btn-outline-success:hover, .btn-outline-light:hover, .dark-mode-toggle button:hover {
            background-color: #ff9900;
            border-color: #ff9900;
            color: #343a40;
        }
        .form-control {
            border-radius: 0;
            width: 100%; /* Make the search bar responsive */
            max-width: 500px; /* Augmenter la longueur maximale */
            transition: width 0.4s;
        }
        .form-control:focus {
            max-width: 550px; /* Agrandir au focus */
        }
        .navbar-center {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .navbar-center .nav-item {
            margin-left: 10px; /* Reduce margin */
        }
        .navbar-center .nav-link {
            padding-top: 5px; /* Align with other elements */
        }
        .navbar-center .dropdown-menu {
            font-size: 1rem; /* Ensure same font size */
        }
        .navbar-center .form-control, .navbar-center .btn-outline-success {
            height: 30px; /* Ensure same height */
        }
        .carousel-item img {
            width: 100%;
            height: 400px; /* Réduire la hauteur des images */
            object-fit: cover; /* Maintenir le ratio d'aspect */
        }
        .carousel-caption {
            background-color: rgba(0, 0, 0, 0.7); /* Darker background for better readability */
            color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 3.5rem;
            height: 3.5rem;
            background-color: black;
            border-radius: 50%;
            padding: 10px;
        }
        .carousel-control-prev-icon::before {
            content: '\f053'; /* FontAwesome left arrow */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: white;
            font-size: 1.75rem;
        }
        .carousel-control-next-icon::before {
            content: '\f054'; /* FontAwesome right arrow */
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            color: white;
            font-size: 1.75rem;
        }
        .card {
            margin-bottom: 20px;
            cursor: pointer;
            transition: box-shadow 0.3s;
            height: 100%; /* Ensure cards have the same height */
            display: flex;
            flex-direction: column;
        }
        .card-body {
            flex: 1; /* Make card body take up remaining space */
            display: flex;
            flex-direction: column;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
            flex: 1; /* Make card text take up remaining space */
        }
        .card-price, .card-quantity, .btn-primary, .btn-danger {
            margin-top: auto; /* Push these elements to the bottom */
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1rem;
        }
        .card-price {
            font-size: 1.5rem;
            color: #ff9900;
        }
        .card-quantity {
            font-size: 1rem;
            color: #555;
            font-weight: bold;
        }
        .table {
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #ff9900;
            border-color: #ff9900;
        }
        .btn-primary:hover {
            background-color: #e68a00;
            border-color: #e68a00;
        }
        .alert {
            margin-top: 20px;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .dark-mode .navbar {
            background-color: #1f1f1f;
        }
        .dark-mode .table {
            background-color: #1f1f1f;
            color: #ffffff;
        }
        .dark-mode .card {
            background-color: #1f1f1f;
            color: #ffffff;
        }
        .dark-mode .card-title,
        .dark-mode .card-text,
        .dark-mode .card-price,
        .dark-mode .card-quantity {
            color: #ffffff;
        }
        .dark-mode .carousel-caption {
            background-color: rgba(0, 0, 0, 0.7); /* Darker background for dark mode */
        }
        .dark-mode-toggle {
            display: flex;
            align-items: center;
            margin-left: 15px; /* Adjust spacing */
        }
        .dark-mode-toggle button {
            margin-right: 10px; /* Add spacing between buttons */
        }
        .low-quantity {
            color: red;
            font-weight: bold;
        }
        .medium-quantity {
            color: orange;
        }
        .high-quantity {
            color: green;
        }
        .very-high-quantity {
            color: black;
        }
        .dark-mode .low-quantity {
            color: red;
        }
        .dark-mode .medium-quantity {
            color: orange;
        }
        .dark-mode .high-quantity {
            color: green;
        }
        .dark-mode .very-high-quantity {
            color: white;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
        .dark-mode .out-of-stock {
            color: red;
        }
        .mt-5 {
            margin-top: 3rem !important;
        }
        .carousel-item {
            transition: transform 0.5s ease-in-out;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
            margin-left: 0.5em;
        }
        .badge-danger { background-color: #dc3545; }
        .badge-primary { background-color: #007bff; }
        .badge-warning { background-color: #ffc107; }
        .badge-purple { background-color: #6f42c1; }
        .badge-yellow { background-color: #ffc107; }
        .badge-success { background-color: #28a745; }
        .badge-peach { background-color: #ffcccb; }
        .badge-fire { background-color: #fd7e14; }
        .badge-pink { background-color: #e83e8c; }
        .badge-light-red { background-color: #f8d7da; }
        .badge-dark-green { background-color: #155724; }
        .badge-sea-water { background-color: #17a2b8; }
        .badge-gold { background-color: #ffd700; }
        .badge-cyan { background-color: #17a2b8; }
        .badge-brown { background-color: #795548; }
        .badge-silver { background-color: #c0c0c0; }
        .badge-black { background-color: #343a40; }
        .dark-mode .badge {
            color: #ffffff;
        }
        .badge-bottom-right {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 0.5em 0.75em;
        }
        .out-of-stock {
            color: red;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .navbar-center {
                flex-direction: column;
            }
            .navbar-center .nav-item {
                margin-left: 0;
                margin-bottom: 10px;
            }
            .navbar-center .form-control {
                width: 100%;
                max-width: none;
            }
            .navbar-center .btn-outline-success {
                width: 100%;
                margin-top: 10px;
            }
            .carousel-caption {
                font-size: 0.9rem; /* Adjust font size for smaller screens */
                padding: 10px; /* Adjust padding for smaller screens */
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Valomazone</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Promotions</a>
                    </li>
                    <li class="nav-item">
                    <li class="nav-item"><a class="nav-link" href="cart_view.php">Panier</a></li>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">SAV</a>
                    </li>
                </ul>
                <form class="d-flex me-3" role="search">
                    <input class="form-control me-2" type="search" placeholder="Chercher un produit" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit"><i class="fas fa-search" style="font-size: 1.1rem;"></i></button>
                </form>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Catégories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="index.php?category=couteau">Couteaux</a></li>
                            <li><a class="dropdown-item" href="index.php?category=classic">Classics</a></li>
                            <li><a class="dropdown-item" href="index.php?category=shorty">Shortys</a></li>
                            <li><a class="dropdown-item" href="index.php?category=frenzy">Frenzys</a></li>
                            <li><a class="dropdown-item" href="index.php?category=ghost">Ghosts</a></li>
                            <li><a class="dropdown-item" href="index.php?category=sherif">Sherifs</a></li>
                            <li><a class="dropdown-item" href="index.php?category=stinger">Stingers</a></li>
                            <li><a class="dropdown-item" href="index.php?category=spectre">Spectres</a></li>
                            <li><a class="dropdown-item" href="index.php?category=bucky">Buckys</a></li>
                            <li><a class="dropdown-item" href="index.php?category=bouldog">Bouldogs</a></li>
                            <li><a class="dropdown-item" href="index.php?category=guardian">Guardians</a></li>
                            <li><a class="dropdown-item" href="index.php?category=phantom">Phantoms</a></li>
                            <li><a class="dropdown-item" href="index.php?category=vandal">Vandals</a></li>
                            <li><a class="dropdown-item" href="index.php?category=marchal">Marchals</a></li>
                            <li><a class="dropdown-item" href="index.php?category=operator">Operators</a></li>
                            <li><a class="dropdown-item" href="index.php?category=ares">Ares</a></li>
                            <li><a class="dropdown-item" href="index.php?category=odin">Odins</a></li>
                            <li><a class="dropdown-item" href="index.php?category=ensemble">Ensemble</a></li> <!-- Nouvelle catégorie ajoutée -->
                        </ul>
                    </li>
                </ul>
                <div class="dark-mode-toggle ms-3">
                    <button id="darkModeToggle" class="btn btn-outline-light">Dark Mode</button>
                </div>
                <button type="button" class="btn btn-outline-light ms-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Connecter
                </button>
            </div>
        </div>
    </nav>
    <main class="container mt-5">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="10000">
            <div class="carousel-inner">
                <?php
                $active = 'active';
                foreach($result as $produit){
                    $image_path = 'image_produit/' . $produit['image_produit'];
                    if (!file_exists($image_path)) {
                        echo '<div class="alert alert-danger" role="alert">Image non trouvée : ' . $image_path . '</div>';
                    }
                ?>
                <div class="carousel-item <?= $active ?>">
                    <img src="<?= $image_path ?>" class="d-block w-100" alt="<?= $produit['produit'] ?>" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
                    <div class="carousel-caption d-md-block">
                        <h5><?= $produit['produit'] ?></h5>
                        <p>A seulement : <?= $produit['prix'] ?> €</p>
                    </div>
                </div>
                <?php
                $active = '';
                }
                ?>
            </div>
            <a class="carousel-control-prev" href="#productCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </a>
            <a class="carousel-control-next" href="#productCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </a>
        </div>
        <div class="row mt-4">
            <?php
            foreach($result as $produit){
                $image_path = 'image_produit/' . $produit['image_produit'];
                if (!file_exists($image_path)) {
                    echo '<div class="alert alert-danger" role="alert">Image non trouvée : ' . $image_path . '</div>';
                }
                $badgeClass = '';
                if ($produit['nombre'] == 0) {
                    $quantityText = '<i class="fas fa-exclamation-triangle"></i> Victime de son succès';
                    $quantityClass = 'out-of-stock';
                } elseif ($produit['nombre'] <= 20) {
                    $quantityClass = 'low-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } elseif ($produit['nombre'] <= 50) {
                    $quantityClass = 'medium-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } elseif ($produit['nombre'] <= 100) {
                    $quantityClass = 'high-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                } else {
                    $quantityClass = 'very-high-quantity';
                    $quantityText = 'Quantité restante: ' . $produit['nombre'];
                }
                $badgeClass = 'badge-primary'; // Default badge class
                if ($produit['badge'] == 'danger') {
                    $badgeClass = 'badge-danger';
                } elseif ($produit['badge'] == 'warning') {
                    $badgeClass = 'badge-warning';
                } elseif ($produit['badge'] == 'success') {
                    $badgeClass = 'badge-success';
                } elseif ($produit['badge'] == 'info') {
                    $badgeClass = 'badge-info';
                } elseif ($produit['badge'] == 'dark') {
                    $badgeClass = 'badge-dark';
                } elseif ($produit['badge'] == 'secondary') {
                    $badgeClass = 'badge-secondary';
                } elseif ($produit['badge'] == 'purple') {
                    $badgeClass = 'badge-purple';
                } elseif ($produit['badge'] == 'yellow') {
                    $badgeClass = 'badge-yellow';
                } elseif ($produit['badge'] == 'peach') {
                    $badgeClass = 'badge-peach';
                } elseif ($produit['badge'] == 'fire') {
                    $badgeClass = 'badge-fire';
                } elseif ($produit['badge'] == 'pink') {
                    $badgeClass = 'badge-pink';
                } elseif ($produit['badge'] == 'light-red') {
                    $badgeClass = 'badge-light-red';
                } elseif ($produit['badge'] == 'dark-green') {
                    $badgeClass = 'badge-dark-green';
                } elseif ($produit['badge'] == 'sea-water') {
                    $badgeClass = 'badge-sea-water';
                } elseif ($produit['badge'] == 'gold') {
                    $badgeClass = 'badge-gold';
                } elseif ($produit['badge'] == 'cyan') {
                    $badgeClass = 'badge-cyan';
                } elseif ($produit['badge'] == 'brown') {
                    $badgeClass = 'badge-brown';
                } elseif ($produit['badge'] == 'silver') {
                    $badgeClass = 'badge-silver';
                } elseif ($produit['badge'] == 'black') {
                    $badgeClass = 'badge-black';
                }

                // Limit description to first three lines
                $description = implode(' ', array_slice(explode(' ', $produit['Description']), 0, 30)) . '...';
            ?>
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
                    <div class="position-relative">
                        <img src="<?= $image_path ?>" class="card-img-top" alt="<?= $produit['produit'] ?>">
                        <span class="badge <?= $badgeClass ?> badge-bottom-right"><?= $produit['badge'] ?></span>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $produit['produit'] ?></h5>
                        <p class="card-text"><?= $description ?></p>
                        <p class="card-price"><?= $produit['prix'] ?> €</p>
                        <p class="card-quantity <?= $quantityClass ?>"><?= $quantityText ?></p>
                        <a href="edit.php?id=<?= $produit['id'] ?>" class="btn btn-primary me-2">Modifier</a> 
                        <a href="delete.php?id=<?= $produit['id'] ?>" class="btn btn-danger">Supprimer</a>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </main>
    
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Se connecter ou créer un compte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bienvenue sur Valomazone !</p>
                    <div class="d-grid gap-2">
                        <a href="login.php" class="btn btn-primary">Se connecter</a>
                        <a href="register.php" class="btn btn-secondary">Créer un compte</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                this.textContent = 'Light Mode';
            } else {
                this.textContent = 'Dark Mode';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>