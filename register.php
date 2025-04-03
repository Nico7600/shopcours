<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inscription</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'Ubuntu', sans-serif;
		}
		h4 {
			font-size: 2rem;
			font-weight: 700;
		}
		label {
			font-size: 1.2rem;
			font-weight: 400;
		}
		.form-control {
			font-size: 1.1rem;
			font-weight: 300;
		}
		.btn {
			font-size: 1.2rem;
			font-weight: 700;
		}
		.text-secondary {
			font-size: 1.1rem;
			font-weight: 400;
		}
		.w-450 {
			width: 600px !important;
		}
	</style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    	
    	<form class="shadow w-450 p-3 bg-white rounded" 
    	      action="register.php" 
    	      method="post">

    		<h4 class="text-center mb-4">Créer un compte</h4>

    		<?php 
            session_start(); 
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $fname = $_POST['fname'];
                $uname = $_POST['uname'];
                $pass = $_POST['pass'];
                $cpass = $_POST['cpass'];

                if (empty($fname) || empty($uname) || empty($pass) || empty($cpass)) {
                    $_SESSION['error'] = "Tous les champs sont obligatoires.";
                } elseif ($pass !== $cpass) {
                    $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                } else {
                    try {
						require_once 'bootstrap.php';                        
                        $stmt = $db->prepare("INSERT INTO users (fname, username, password, date, last_ip) VALUES (?, ?, ?, NOW(), ?)");
                        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
                        $last_ip = $_SERVER['REMOTE_ADDR'];
                        $stmt->execute([$fname, $uname, $hashed_pass, $last_ip]);

                        $_SESSION['success'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                        header("Location: login.php");
                        exit();
                    } catch (PDOException $e) {
                        $_SESSION['error'] = "Erreur de connexion à la base de données: " . $e->getMessage();
                    }
                }
            }
            if (isset($_SESSION['error'])): ?>
    		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			  <?php echo $_SESSION['error']; ?>
			  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		    <?php 
            unset($_SESSION['error']);
            endif; ?>

            <div class="mb-3">
		        <label for="fname" class="form-label">Prénom</label>
		        <input type="text" 
		               class="form-control" 
		               id="fname" 
		               name="fname" 
		               placeholder="Entrez votre prénom"
		               value="<?php echo isset($_SESSION['fname']) ? $_SESSION['fname'] : ''; ?>">
		    </div>

		  <div class="mb-3">
		    <label for="uname" class="form-label">Nom d'utilisateur</label>
		    <input type="text" 
		           class="form-control" 
		           id="uname" 
		           name="uname" 
		           placeholder="Choisissez un nom d'utilisateur"
		           value="<?php echo isset($_SESSION['uname']) ? $_SESSION['uname'] : ''; ?>">
		  </div>

		  <div class="mb-3">
		    <label for="pass" class="form-label">Mot de passe</label>
		    <input type="password" 
		           class="form-control" 
		           id="pass" 
		           name="pass" 
		           placeholder="Choisissez un mot de passe">
		  </div>

		  <div class="mb-3">
		    <label for="cpass" class="form-label">Confirmer le mot de passe</label>
		    <input type="password" 
		           class="form-control" 
		           id="cpass" 
		           name="cpass" 
		           placeholder="Confirmez votre mot de passe">
		  </div>
		  
		  <div class="d-flex justify-content-between align-items-center">
		      <button type="submit" class="btn btn-primary">S'inscrire</button>
		      <a href="login.php" class="text-secondary">Connexion</a>
		  </div>
		</form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>
</html>