<?php
require_once 'bootstrap.php';

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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<style>
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
		body {
			font-family: 'Ubuntu', sans-serif;
			background-color: #1a1a1a; /* New background color */
			background-image: url('images/background.jpg');
			background-size: cover;
			background-position: center;
			animation: backgroundAnimation 20s infinite alternate;
			color: #ffffff; /* Set text color to white */
		}
		h4 {
			font-size: 1.5rem;
			font-weight: 700;
			background: linear-gradient(to right, #007bff 48%, #ff5733 52%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			position: relative;
		}
		h4::before, h4::after {
			content: "\f007"; /* Font Awesome user icon */
			font-family: "Font Awesome 5 Free";
			font-weight: 900;
			position: absolute;
			top: 50%;
			transform: translateY(-50%);
		}
		h4::before {
			left: -25px;
		}
		h4::after {
			right: -25px;
		}
		label {
			font-size: 1rem;
			font-weight: 400;
		}
		.btn {
			font-size: 1rem;
			font-weight: 700;
			background-color: #007bff;
			border-color: #007bff;
		}
		.text-secondary {
			font-size: 0.875rem;
			font-weight: 400;
		}
		.form-container {
			background: #2f4f4f;
			padding: 2rem;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			width: 500px; 
		}
		.form-control {
			background-color: #343a40;
			color: #ffffff;
			border: 1px solid #007bff;
		}
		.form-control:focus {
			background-color: #343a40;
			color: #ffffff;
			border-color: #ff5733;
			box-shadow: 0 0 0 0.2rem rgba(255, 87, 51, 0.25);
		}
		.form-control::placeholder {
			color: #cccccc;
		}
		.form-control:not(:placeholder-shown) {
			background-color: #343a40;
			color: #ffffff;
		}
		footer {
			position: fixed;
			bottom: 0;
			width: 100%;
			text-align: center;
			padding: 1rem;
			background: #343a40;
			color: #ffffff; /* Set footer text color to white */
		}
	</style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
    	<form class="form-container shadow w-450 p-3 rounded" 
    	      action="login_verify.php" 
    	      method="post">

    		<h4 class="text-center mb-4">
    		    <i class="fas fa-user-circle"></i> Connexion <i class="fas fa-user-circle"></i>
    		</h4>

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
		    <div class="input-group">
		      <span class="input-group-text"><i class="fas fa-user"></i></span>
		      <input type="text" 
		             class="form-control" 
		             id="uname" 
		             name="uname" 
		             placeholder="Entrez votre nom d'utilisateur"
		             value="<?php echo isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; ?>">
		    </div>
		  </div>

		  <div class="mb-3">
		    <label for="pass" class="form-label">Mot de passe</label>
		    <div class="input-group">
		      <span class="input-group-text"><i class="fas fa-lock"></i></span>
		      <input type="password" 
		             class="form-control" 
		             id="pass" 
		             name="pass" 
		             placeholder="Entrez votre mot de passe">
		    </div>
		  </div>
		  
		  <div class="d-flex justify-content-between align-items-center">
		      <button type="submit" class="btn btn-primary">
		          <i class="fas fa-sign-in-alt"></i> Se connecter
		      </button>
		      <a href="register.php" class="text-secondary">Créer un compte</a>
		  </div>
		</form>
    </div>

    <footer>
    	<p>&copy; 2024-2025 Valomazone. Tous droits réservés.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
