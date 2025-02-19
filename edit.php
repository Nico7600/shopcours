<?php
session_start();

if($_POST){
    if(isset($_POST['produit']) && !empty($_POST['produit'])
    && isset($_POST['Description']) && !empty($_POST['Description'])
    && isset($_POST['prix']) && !empty($_POST['prix'])
    && isset($_POST['nombre']) && is_numeric($_POST['nombre'])
    && isset($_POST['badge']) && !empty($_POST['badge'])
    && isset($_POST['Promo']) && is_numeric($_POST['Promo'])){
        require_once('connect.php');
        $db->exec("SET NAMES 'utf8mb4'");

        $produit = strip_tags($_POST['produit']);
        $Description = strip_tags($_POST['Description']);
        $prix = strip_tags($_POST['prix']);
        $nombre = strip_tags($_POST['nombre']);
        $badge = strip_tags($_POST['badge']);
        $promo = strip_tags($_POST['Promo']);

        try {
            $sql = 'UPDATE `liste` SET `produit`=:produit, `Description`=:Description, `prix`=:prix, `nombre`=:nombre, `badge`=:badge, `Promo`=:Promo WHERE `id`=:id;';
            $query = $db->prepare($sql);

            $query->bindValue(':produit', $produit, PDO::PARAM_STR);
            $query->bindValue(':Description', $Description, PDO::PARAM_STR);
            $query->bindValue(':prix', $prix, PDO::PARAM_STR);
            $query->bindValue(':nombre', $nombre, PDO::PARAM_INT);
            $query->bindValue(':badge', $badge, PDO::PARAM_STR);
            $query->bindValue(':Promo', $promo, PDO::PARAM_INT);
            $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

            $query->execute();

            $_SESSION['message'] = "Produit modifié";
            require_once('close.php');

            header('Location: index.php');
        } catch (PDOException $e) {
            $_SESSION['erreur'] = "Erreur : " . $e->getMessage();
            header('Location: edit.php?id=' . $_GET['id']);
            exit;
        }
    }else{
        $_SESSION['erreur'] = "Le formulaire est incomplet";
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
                <form method="post" action="edit.php?id=<?= $produit['id'] ?>">
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
                            <option value="couteau" <?= $produit['badge'] == 'couteau' ? 'selected' : '' ?> class="badge-danger">Couteau</option>
                            <option value="classic" <?= $produit['badge'] == 'classic' ? 'selected' : '' ?> class="badge-primary">Classic</option>
                            <option value="shorty" <?= $produit['badge'] == 'shorty' ? 'selected' : '' ?> class="badge-warning">Shorty</option>
                            <option value="Frenzy" <?= $produit['badge'] == 'Frenzy' ? 'selected' : '' ?> class="badge-purple">Frenzy</option>
                            <option value="Ghost" <?= $produit['badge'] == 'Ghost' ? 'selected' : '' ?> class="badge-yellow">Ghost</option>
                            <option value="sherif" <?= $produit['badge'] == 'sherif' ? 'selected' : '' ?> class="badge-success">Sherif</option>
                            <option value="Stinger" <?= $produit['badge'] == 'Stinger' ? 'selected' : '' ?> class="badge-peach">Stinger</option>
                            <option value="spectre" <?= $produit['badge'] == 'spectre' ? 'selected' : '' ?> class="badge-fire">Spectre</option>
                            <option value="Bucky" <?= $produit['badge'] == 'Bucky' ? 'selected' : '' ?> class="badge-pink">Bucky</option>
                            <option value="Bouldog" <?= $produit['badge'] == 'Bouldog' ? 'selected' : '' ?> class="badge-light-red">Bouldog</option>
                            <option value="guardian" <?= $produit['badge'] == 'guardian' ? 'selected' : '' ?> class="badge-dark-green">Guardian</option>
                            <option value="Phantom" <?= $produit['badge'] == 'Phantom' ? 'selected' : '' ?> class="badge-sea-water">Phantom</option>
                            <option value="Vandal" <?= $produit['badge'] == 'Vandal' ? 'selected' : '' ?> class="badge-gold">Vandal</option>
                            <option value="marchal" <?= $produit['badge'] == 'marchal' ? 'selected' : '' ?> class="badge-cyan">Marchal</option>
                            <option value="opérator" <?= $produit['badge'] == 'opérator' ? 'selected' : '' ?> class="badge-brown">Opérator</option>
                            <option value="ares" <?= $produit['badge'] == 'ares' ? 'selected' : '' ?> class="badge-silver">Ares</option>
                            <option value="odin" <?= $produit['badge'] == 'odin' ? 'selected' : '' ?> class="badge-black">Odin</option>
                            <option value="Judges" <?= $produit['badge'] == 'Judges' ? 'selected' : '' ?> class="badge-white">Judges</option>
                            <option value="ensemble" <?= $produit['badge'] == 'ensemble' ? 'selected' : '' ?> class="badge-primary">Ensemble</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo">Promo (%)</label>
                        <input type="number" id="Promo" name="Promo" class="form-control" value="<?= $produit['Promo'] ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>