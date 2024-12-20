<?php
$search = $search ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .nav-link {
            font-size: 1rem;
            font-weight: 400;
        }
        .dropdown-item {
            font-size: 0.9rem;
            font-weight: 400;
        }
        .btn-outline-light {
            font-size: 1rem;
            font-weight: 700;
        }
        .dropdown-item-admin {
            color: blue;
        }
        .dropdown-menu-end {
            right: 0;
            left: auto;
            transform: translateX(-10%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <!-- Logo -->
            <a class="navbar-brand fw-bold text-uppercase" href="index.php">
                Valomazone
            </a>

            <!-- Bouton pour affichage mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu de la navbar -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Liens principaux -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="404.php">Promotions</a>
                    </li>
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
                    
                </ul>

                <!-- Barre de recherche -->
                <form class="form-inline my-2 my-lg-0" method="GET" action="index.php">
                    <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher" name="search" value="<?= htmlspecialchars($search ?? ''); ?>">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
                </form>
            </div>

            <!-- Profil utilisateur -->
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
                        <?php if (isset($_SESSION['id'])): ?>
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
</body>
</html>
