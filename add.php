<?php
require_once 'bootstrap.php'; // Charge les sessions, la connexion et les variables d'environnement

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des champs obligatoires
    $requiredFields = ['produit', 'description', 'prix', 'nombre', 'badge', 'Promo', 'production_company_id'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field]) || (!is_numeric($_POST[$field]) && in_array($field, ['prix', 'nombre', 'Promo', 'production_company_id']))) {
            $_SESSION['erreur'] = 'Tous les champs obligatoires doivent être remplis correctement.';
            header('Location: add.php');
            exit;
        }
    }

    if (!isset($_FILES['image_produit']) || $_FILES['image_produit']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['erreur'] = 'Erreur lors du téléchargement de l’image.';
        header('Location: add.php');
        exit;
    }

    // Nettoyage des données utilisateur
    $produit = strip_tags($_POST['produit']);
    $description = substr(strip_tags($_POST['description']), 0, 255); // Limiter la description à 255 caractères
    $prix = (float)$_POST['prix'];
    $nombre = (int)$_POST['nombre'];
    $badge = strip_tags($_POST['badge']);
    $promo = (int)$_POST['Promo'];
    $production_company_id = (int)$_POST['production_company_id'];

    // Gestion de l'upload de l'image
    $uploadsDir = 'uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileInfo = pathinfo($_FILES['image_produit']['name']);
    $fileExtension = strtolower($fileInfo['extension']);

    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION['erreur'] = 'Format de fichier non autorisé. Seuls JPG, JPEG, PNG et GIF sont acceptés.';
        header('Location: add.php');
        exit;
    }

    $newFilename = uniqid() . '.' . $fileExtension;
    $filePath = $uploadsDir . '/' . $newFilename;
    if (!move_uploaded_file($_FILES['image_produit']['tmp_name'], $filePath)) {
        $_SESSION['erreur'] = 'Erreur lors de l’enregistrement de l’image.';
        header('Location: add.php');
        exit;
    }

    try {
        // Insertion dans la base de données
        $sql = 'INSERT INTO liste (produit, description, prix, nombre, image_produit, badge, Promo, actif, production_company_id) 
                VALUES (:produit, :description, :prix, :nombre, :image_produit, :badge, :Promo, 1, :production_company_id)';
        $query = $db->prepare($sql);

        $query->bindValue(':produit', $produit, PDO::PARAM_STR);
        $query->bindValue(':description', $description, PDO::PARAM_STR);
        $query->bindValue(':prix', $prix, PDO::PARAM_STR);
        $query->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $query->bindValue(':image_produit', $newFilename, PDO::PARAM_STR);
        $query->bindValue(':badge', $badge, PDO::PARAM_STR);
        $query->bindValue(':Promo', $promo, PDO::PARAM_INT);
        $query->bindValue(':production_company_id', $production_company_id, PDO::PARAM_INT);

        $query->execute();

        $_SESSION['message'] = 'Produit ajouté avec succès.';
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        error_log('Erreur lors de l’ajout du produit : ' . $e->getMessage());
        $_SESSION['erreur'] = 'Une erreur est survenue lors de l’ajout du produit.';
        header('Location: add.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }
        h1 {
            font-size: 2.5rem;
            font-weight: 700;
        }
        label {
            font-size: 1.2rem;
            font-weight: 400;
        }
        .btn {
            font-size: 1rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <?php
                if (!empty($_SESSION['erreur'])) {
                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['erreur']) . '</div>';
                    unset($_SESSION['erreur']);
                }
                ?>
                <h1>Ajouter un produit</h1>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="produit">Produit</label>
                        <input type="text" id="produit" name="produit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix</label>
                        <input type="number" step="0.01" id="prix" name="prix" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Quantité</label>
                        <input type="number" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="image_produit">Image</label>
                        <input type="file" id="image_produit" name="image_produit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="badge">Badge</label>
                        <select class="form-control" id="badge" name="badge" required>
                            <option value="">Aucun</option>
                            <option value="classic">Classic</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo">Promotion (%)</label>
                        <input type="number" id="Promo" name="Promo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="production_company_id">Société de production</label>
                        <select class="form-control" id="production_company_id" name="production_company_id" required>
                            <?php
                            $sql = 'SELECT id, name FROM production_companies';
                            foreach ($db->query($sql) as $company) {
                                echo '<option value="' . htmlspecialchars($company['id']) . '">' . htmlspecialchars($company['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
