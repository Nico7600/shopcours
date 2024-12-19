<?php
// On démarre une session
session_start();

if ($_POST) {
    if (isset($_POST['produit']) && !empty($_POST['produit'])
        && isset($_POST['description']) && !empty($_POST['description'])
        && isset($_POST['prix']) && is_numeric($_POST['prix']) // Ensure prix is numeric
        && isset($_POST['nombre']) && is_numeric($_POST['nombre'])
        && isset($_FILES['image_produit']) && $_FILES['image_produit']['error'] == 0
        && isset($_POST['badge']) && !empty($_POST['badge'])
        && isset($_POST['Promo']) && is_numeric($_POST['Promo'])
        && isset($_POST['production_company_id']) && is_numeric($_POST['production_company_id'])) {

        require_once('connect.php');

        // On nettoie les données envoyées
        $produit = strip_tags($_POST['produit']);
        $description = strip_tags($_POST['description']);
        $prix = strip_tags($_POST['prix']);
        $nombre = strip_tags($_POST['nombre']);
        $badge = strip_tags($_POST['badge']);
        $promo = strip_tags($_POST['Promo']);
        $production_company_id = strip_tags($_POST['production_company_id']);

        // Ensure the uploads directory exists
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }

        // On gère l'upload de l'image
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image_produit']['name'];
        $filetype = $_FILES['image_produit']['type'];
        $filesize = $_FILES['image_produit']['size'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image_produit']['tmp_name'], 'image_produit/' . $new_filename);
        } else {
            $_SESSION['erreur'] = "Format de fichier non autorisé";
            header('Location: add.php');
            exit;
        }

        // Limit the length of the description
        $description = substr($_POST['description'], 0, 255);

        $sql = 'INSERT INTO `liste` (`produit`, `description`, `prix`, `nombre`, `image_produit`, `badge`, `Promo`, `actif`, `production_company_id`) 
                VALUES (:produit, :description, :prix, :nombre, :image_produit, :badge, :Promo, 1, :production_company_id);';

        $query = $db->prepare($sql);

        $query->bindValue(':produit', $produit, PDO::PARAM_STR);
        $query->bindValue(':description', $description, PDO::PARAM_STR);
        $query->bindValue(':prix', $prix, PDO::PARAM_STR);
        $query->bindValue(':nombre', $nombre, PDO::PARAM_INT);
        $query->bindValue(':image_produit', $new_filename, PDO::PARAM_STR);
        $query->bindValue(':badge', $badge, PDO::PARAM_STR);
        $query->bindValue(':Promo', $promo, PDO::PARAM_INT);
        $query->bindValue(':production_company_id', $production_company_id, PDO::PARAM_INT);

        $query->execute();

        if ($query->rowCount() > 0) {
            $_SESSION['message'] = "Produit ajouté";
        } else {
            $_SESSION['erreur'] = "Erreur lors de l'ajout du produit";
        }

        require_once('close.php');

        header('Location: index.php');
    } else {
        $_SESSION['erreur'] = "Le formulaire est incomplet";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
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
                    if(!empty($_SESSION['erreur'])){
                        echo '<div class="alert alert-danger" role="alert">
                                '. $_SESSION['erreur'].'
                            </div>';
                        $_SESSION['erreur'] = "";
                    }
                ?>
                <h1>Ajouter un produit</h1>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="produit">Produit</label>
                        <input type="text" id="produit" name="produit" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix</label>
                        <input type="text" id="prix" name="prix" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="number" id="nombre" name="nombre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="image_produit">Image</label>
                        <input type="file" id="image_produit" name="image_produit" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="badge">Badge</label>
                        <select class="form-control" id="badge" name="badge">
                            <option value="">Aucun</option>
                            <option value="couteau" class="badge-danger">Couteau</option>
                            <option value="classic" class="badge-primary">Classic</option>
                            <option value="shorty" class="badge-warning">Shorty</option>
                            <option value="Frenzy" class="badge-purple">Frenzy</option>
                            <option value="Ghost" class="badge-yellow">Ghost</option>
                            <option value="sherif" class="badge-success">Sherif</option>
                            <option value="Stinger" class="badge-peach">Stinger</option>
                            <option value="spectre" class="badge-fire">Spectre</option>
                            <option value="Bucky" class="badge-pink">Bucky</option>
                            <option value="Bouldog" class="badge-light-red">Bouldog</option>
                            <option value="guardian" class="badge-dark-green">Guardian</option>
                            <option value="Phantom" class="badge-sea-water">Phantom</option>
                            <option value="Vandal" class="badge-gold">Vandal</option>
                            <option value="marchal" class="badge-cyan">Marchal</option>
                            <option value="opérator" class="badge-brown">Opérator</option>
                            <option value="ares" class="badge-silver">Ares</option>
                            <option value="odin" class="badge-black">Odin</option>
                            <option value="Judges" class="badge-nico">Ensemble</option>
                            <option value="ensemble" class="badge-primary">Ensemble</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo">Promo (%)</label> <!-- Update field name -->
                        <input type="number" id="Promo" name="Promo" class="form-control"> <!-- Update field name -->
                    </div>
                    <div class="form-group">
                        <label for="production_company_id">Société de production</label>
                        <select class="form-control" id="production_company_id" name="production_company_id">
                            <?php
                            require_once('connect.php');
                            $sql = 'SELECT id, name FROM production_companies';
                            $query = $db->query($sql);
                            $companies = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($companies as $company) {
                                echo '<option value="' . $company['id'] . '">' . htmlspecialchars($company['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <button class="btn btn-primary">Envoyer</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>