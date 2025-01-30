<?php
require_once 'bootstrap.php';

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
    $description = substr(strip_tags($_POST['description']), 0, 255);
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

// Pagination logic
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    $sql = 'SELECT * FROM liste LIMIT :limit OFFSET :offset';
    $query = $db->prepare($sql);
    $query->bindValue(':limit', $limit, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    $totalSql = 'SELECT COUNT(*) FROM liste';
    $totalQuery = $db->query($totalSql);
    $totalProducts = $totalQuery->fetchColumn();
    $totalPages = ceil($totalProducts / $limit);
} catch (PDOException $e) {
    error_log('Erreur lors de la récupération des produits : ' . $e->getMessage());
    $_SESSION['erreur'] = 'Une erreur est survenue lors de la récupération des produits.';
    header('Location: add.php');
    exit;
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
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .fa-plus-circle, .fa-list {
            color: rgba(75, 192, 192, 1);
        }
        .fa-box, .fa-align-left, .fa-dollar-sign, .fa-sort-numeric-up, .fa-image, .fa-tag, .fa-percent, .fa-industry {
            color: rgba(153, 102, 255, 1);
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
                <h1 class="text-center" style="color: black;"><i class="fas fa-plus-circle"></i> Ajouter un produit</h1>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="produit"><i class="fas fa-box"></i> Produit</label>
                        <input type="text" id="produit" name="produit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="prix"><i class="fas fa-dollar-sign"></i> Prix</label>
                        <input type="number" step="0.01" id="prix" name="prix" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre"><i class="fas fa-sort-numeric-up"></i> Quantité</label>
                        <input type="number" id="nombre" name="nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="image_produit"><i class="fas fa-image"></i> Image</label>
                        <input type="file" id="image_produit" name="image_produit" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="badge"><i class="fas fa-tag"></i> Badge</label>
                        <select class="form-control" id="badge" name="badge" required>
                            <option value="">Aucun</option>
                            <option value="classic">Classic</option>
                            <option value="ghost">Ghost</option>
                            <option value="shorty">Shorty</option>
                            <option value="frenzy">Frenzy</option>
                            <option value="stinger">Stinger</option>
                            <option value="spectre">Spectre</option>
                            <option value="phantom">Phantom</option>
                            <option value="vandal">Vandal</option>
                            <option value="marshal">Marshal</option>
                            <option value="operator">Operator</option>
                            <option value="bucky">Bucky</option>
                            <option value="judge">Judge</option>
                            <option value="ares">Ares</option>
                            <option value="odin">Odin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo"><i class="fas fa-percent"></i> Promotion (%)</label>
                        <input type="number" id="Promo" name="Promo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="production_company_id"><i class="fas fa-industry"></i> Société de production</label>
                        <select class="form-control" id="production_company_id" name="production_company_id" required>
                            <?php
                            $sql = 'SELECT id, name FROM production_companies';
                            foreach ($db->query($sql) as $company) {
                                echo '<option value="' . htmlspecialchars($company['id']) . '">' . htmlspecialchars($company['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                </form>
                <h2 class="text-center mt-5"><i class="fas fa-list"></i> Liste des produits</h2>
                <ul class="list-group">
                    <?php foreach ($products as $product): ?>
                        <li class="list-group-item">
                            <?php echo htmlspecialchars($product['produit']); ?> - Badge: <?php echo htmlspecialchars($product['badge']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-4">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </section>
        </div>
    </main>
    <script>
        var primaryButtons = document.querySelectorAll('.btn-primary');
        primaryButtons.forEach(function(button) {
            button.style.backgroundColor = 'rgba(153, 102, 255, 1)';
            button.style.borderColor = 'rgba(153, 102, 255, 1)';
            button.addEventListener('mouseover', function() {
                button.style.backgroundColor = 'rgba(153, 102, 255, 0.8)';
                button.style.borderColor = 'rgba(153, 102, 255, 0.8)';
            });
            button.addEventListener('mouseout', function() {
                button.style.backgroundColor = 'rgba(153, 102, 255, 1)';
                button.style.borderColor = 'rgba(153, 102, 255, 1)';
            });
        });
        document.body.style.backgroundColor = 'rgba(75, 192, 192, 0.2)';
        document.querySelector('h1').style.color = 'black';
    </script>
</body>
</html>