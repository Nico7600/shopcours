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
                    <a class="nav-link active" href="#">Promotions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart_view.php">
                        <i class="fas fa-shopping-cart"></i> Panier
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-headset"></i> Support
                    </a>
                </li>
            </ul>

            <!-- Barre de recherche -->
            <form class="d-flex me-3" role="search">
                <input class="form-control me-2" type="search" placeholder="Chercher un produit" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <!-- Profil utilisateur -->
            <div class="dropdown ms-auto">
                <?php if (isset($userName)): ?>
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($userName); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="order_history.php">
                            <i class="fas fa-history"></i> Historique d'achat</a></li>
                        <li><a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user"></i> Profile</a></li>
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
    </div>
</nav>
