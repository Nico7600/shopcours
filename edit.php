<?php
session_start();

if($_POST){
    if(isset($_POST['produit']) && !empty($_POST['produit'])
    && isset($_POST['Description']) && !empty($_POST['Description'])
    && isset($_POST['prix']) && !empty($_POST['prix'])
    && isset($_POST['nombre']) && is_numeric($_POST['nombre'])
    && isset($_POST['badge']) && !empty($_POST['badge'])
    && (isset($_POST['Promo']) && is_numeric($_POST['Promo']) || $_POST['Promo'] == '')){
        require_once('connect.php');
        $db->exec("SET NAMES 'utf8mb4'");

        $produit = strip_tags($_POST['produit']);
        $Description = strip_tags($_POST['Description']);
        $prix = strip_tags($_POST['prix']);
        $nombre = strip_tags($_POST['nombre']);
        $badge = strip_tags($_POST['badge']);
        $promo = isset($_POST['Promo']) ? strip_tags($_POST['Promo']) : 0;

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            $image = $_FILES['image'];
            $imagePath = basename($image['name']);
            move_uploaded_file($image['tmp_name'], "image_produit/" . $imagePath);
        } else {
            $imagePath = isset($_POST['current_image']) ? $_POST['current_image'] : '';
        }

        try {
            $sql = 'UPDATE `liste` SET `produit`=:produit, `Description`=:Description, `prix`=:prix, `nombre`=:nombre, `badge`=:badge, `Promo`=:Promo, `image_produit`=:image WHERE `id`=:id;';
            $query = $db->prepare($sql);

            $query->bindValue(':produit', $produit, PDO::PARAM_STR);
            $query->bindValue(':Description', $Description, PDO::PARAM_STR);
            $query->bindValue(':prix', $prix, PDO::PARAM_STR);
            $query->bindValue(':nombre', $nombre, PDO::PARAM_INT);
            $query->bindValue(':badge', $badge, PDO::PARAM_STR);
            $query->bindValue(':Promo', $promo, PDO::PARAM_INT);
            $query->bindValue(':image', $imagePath, PDO::PARAM_STR);
            $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

            $query->execute();

            $_SESSION['message'] = "Produit modifié";
            require_once('close.php');

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['erreur'] = "Erreur : " . $e->getMessage();
            header('Location: edit.php?id=' . $_GET['id']);
            exit;
        }
    }else{
        $_SESSION['erreur'] = "Le formulaire est incomplet ou le champ Promo doit être un nombre.";
    }
}else{
    if(isset($_GET['id']) && !empty($_GET['id'])){
        require_once('connect.php');
        $db->exec("SET NAMES 'utf8mb4'");

        $id = strip_tags($_GET['id']);

        $sql = 'SELECT * FROM `liste` WHERE `id`=:id;';
        $query = $db->prepare($sql);

        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $produit = $query->fetch();

        if(!$produit){
            $_SESSION['erreur'] = "Cet id n'existe pas";
            header('Location: index.php');
            exit;
        }
    }else{
        $_SESSION['erreur'] = "URL invalide";
        header('Location: index.php');
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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
        .form-control {
            font-size: 1rem;
            font-weight: 300;
        }
        .btn {
            font-size: 1.1rem;
            font-weight: 400;
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
                <h1>Modifier un Produit</h1>
                <form method="post" action="edit.php?id=<?= $produit['id'] ?>" enctype="multipart/form-data">
                    <input type="hidden" name="current_image" value="<?= $produit['image_produit'] ?>">
                    <div class="form-group">
                        <label for="produit">Produit</label>
                        <input type="text" id="produit" name="produit" class="form-control" value="<?= $produit['produit'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="Description">Description</label>
                        <textarea class="form-control" id="Description" name="Description" rows="5" required><?= $produit['Description'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix</label>
                        <input type="text" id="prix" name="prix" class="form-control" value="<?= $produit['prix'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="number" id="nombre" name="nombre" class="form-control" value="<?= $produit['nombre'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="badge">Badge</label>
                        <select class="form-control" id="badge" name="badge">
                            <option value="">Aucun</option>
                            <option value="couteau" <?= $produit['badge'] == 'couteau' ? 'selected' : '' ?> class="badge-danger"><i class="fas fa-knife"></i> Couteau</option>
                            <option value="classic" <?= $produit['badge'] == 'classic' ? 'selected' : '' ?> class="badge-primary"><i class="fas fa-gun"></i> Classic</option>
                            <option value="shorty" <?= $produit['badge'] == 'shorty' ? 'selected' : '' ?> class="badge-warning"><i class="fas fa-bolt"></i> Shorty</option>
                            <option value="Frenzy" <?= $produit['badge'] == 'Frenzy' ? 'selected' : '' ?> class="badge-purple"><i class="fas fa-fire"></i> Frenzy</option>
                            <option value="Ghost" <?= $produit['badge'] == 'Ghost' ? 'selected' : '' ?> class="badge-yellow"><i class="fas fa-ghost"></i> Ghost</option>
                            <option value="sherif" <?= $produit['badge'] == 'sherif' ? 'selected' : '' ?> class="badge-success"><i class="fas fa-star"></i> Sherif</option>
                            <option value="Stinger" <?= $produit['badge'] == 'Stinger' ? 'selected' : '' ?> class="badge-peach"><i class="fas fa-bug"></i> Stinger</option>
                            <option value="spectre" <?= $produit['badge'] == 'spectre' ? 'selected' : '' ?> class="badge-fire"><i class="fas fa-eye"></i> Spectre</option>
                            <option value="Bucky" <?= $produit['badge'] == 'Bucky' ? 'selected' : '' ?> class="badge-pink"><i class="fas fa-bullseye"></i> Bucky</option>
                            <option value="Bouldog" <?= $produit['badge'] == 'Bouldog' ? 'selected' : '' ?> class="badge-light-red"><i class="fas fa-dog"></i> Bouldog</option>
                            <option value="guardian" <?= $produit['badge'] == 'guardian' ? 'selected' : '' ?> class="badge-dark-green"><i class="fas fa-shield-alt"></i> Guardian</option>
                            <option value="Phantom" <?= $produit['badge'] == 'Phantom' ? 'selected' : '' ?> class="badge-sea-water"><i class="fas fa-mask"></i> Phantom</option>
                            <option value="Vandal" <?= $produit['badge'] == 'Vandal' ? 'selected' : '' ?> class="badge-gold"><i class="fas fa-skull"></i> Vandal</option>
                            <option value="marchal" <?= $produit['badge'] == 'marchal' ? 'selected' : '' ?> class="badge-cyan"><i class="fas fa-shield-alt"></i> Marchal</option>
                            <option value="op��rator" <?= $produit['badge'] == 'opérator' ? 'selected' : '' ?> class="badge-brown"><i class="fas fa-crosshairs"></i> Opérator</option>
                            <option value="ares" <?= $produit['badge'] == 'ares' ? 'selected' : '' ?> class="badge-silver"><i class="fas fa-archway"></i> Ares</option>
                            <option value="odin" <?= $produit['badge'] == 'odin' ? 'selected' : '' ?> class="badge-black"><i class="fas fa-hammer"></i> Odin</option>
                            <option value="Judges" <?= $produit['badge'] == 'Judges' ? 'selected' : '' ?> class="badge-white"><i class="fas fa-gavel"></i> Judges</option>
                            <option value="ensemble" <?= $produit['badge'] == 'ensemble' ? 'selected' : '' ?> class="badge-primary"><i class="fas fa-users"></i> Ensemble</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo">Promo (%)</label>
                        <input type="number" id="Promo" name="Promo" class="form-control" value="<?= $produit['Promo'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">Image (optionnel)</label>
                        <input type="file" id="image" name="image" class="form-control">
                        <?php if (!empty($produit['image_produit'])): ?>
                            <img src="<?= $produit['image_produit'] ?>" alt="Image actuelle" style="max-width: 100px; margin-top: 10px;">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>