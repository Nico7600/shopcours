<?php
require_once 'bootstrap.php'; // Charge les sessions et la connexion à la base

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Connexion</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Ubuntu', sans-serif;
			background-image: url('images/background.jpg');
			background-size: cover;
			background-position: center;
			animation: backgroundAnimation 20s infinite alternate;
		}
		@keyframes backgroundAnimation {
			0% {
				background-position: center;
			}
			50% {
				background-position: top;
			}
			100% {
				background-position: center;
			}
		}
		h4 {
			font-size: 1.5rem;
			font-weight: 700;
		}
		label {
			font-size: 1rem;
			font-weight: 400;
		}
		.btn {
			font-size: 1rem;
			font-weight: 700;
		}
		.text-secondary {
			font-size: 0.875rem;
			font-weight: 400;
		}
		.form-container {
			background: rgba(255, 255, 255, 0.9);
			padding: 2rem;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			width: 500px; /* Increased width */
		}
		footer {
			position: fixed;
			bottom: 0;
			width: 100%;
			text-align: center;
			padding: 1rem;
			background: rgba(255, 255, 255, 0.8);
		}
	</style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
    	<form class="form-container shadow w-450 p-3 rounded" 
    	      action="login_verify.php" 
    	      method="post">

    		<h4 class="text-center mb-4">Connexion</h4>

    		<?php 
            if (isset($_SESSION['error'])): ?>
    		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			  <?php echo $_SESSION['error']; ?>
			  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
			</div>
		    <?php 
            unset($_SESSION['error']); 
            endif; ?>

		  <div class="mb-3">
		    <label for="uname" class="form-label">Nom d'utilisateur</label>
		    <input type="text" 
		           class="form-control" 
		           id="uname" 
		           name="uname" 
		           placeholder="Entrez votre nom d'utilisateur"
		           value="<?php echo isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; ?>">
		  </div>

		  <div class="mb-3">
		    <label for="pass" class="form-label">Mot de passe</label>
		    <input type="password" 
		           class="form-control" 
		           id="pass" 
		           name="pass" 
		           placeholder="Entrez votre mot de passe">
		  </div>
		  
		  <div class="d-flex justify-content-between align-items-center">
		      <button type="submit" class="btn btn-primary">Se connecter</button>
		      <a href="register.php" class="text-secondary">Créer un compte</a>
		  </div>
		</form>
    </div>

    <footer>
    	<p>&copy; 2024 Valomazone. Tous droits réservés.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
