<head>
    <!-- ...existing code... -->
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .modal-title {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
        }
        .modal-body p {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.2rem;
            font-weight: 400;
        }
        .btn {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1rem;
            font-weight: 700;
        }
        .btn-close {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1rem;
            font-weight: 400;
        }
    </style>
</head>
<body>
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
</body>
