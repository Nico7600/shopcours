<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Connexion</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    	<form class="shadow w-450 p-3 bg-white rounded" 
    	      action="login_verify.php" 
    	      method="post">

    		<h4 class="text-center mb-4">Connexion</h4>

    		<?php 
            session_start(); 
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
