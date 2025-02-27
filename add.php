<?php
require_once 'bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debugging: Log the POST data
    error_log('POST Data: ' . json_encode($_POST));

    $requiredFields = ['produit', 'description', 'prix', 'nombre', 'badge', 'production_company_id'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['erreur'] = 'Le champ ' . $field . ' est obligatoire.';
            header('Location: add.php');
            exit;
        }
        if (!is_numeric($_POST[$field]) && in_array($field, ['prix', 'nombre', 'production_company_id'])) {
            $_SESSION['erreur'] = 'Le champ ' . $field . ' doit être un nombre.';
            header('Location: add.php');
            exit;
        }
    }

    if (isset($_POST['Promo']) && !is_numeric($_POST['Promo'])) {
        $_SESSION['erreur'] = 'Le champ Promo doit être un nombre.';
        header('Location: add.php');
        exit;
    }

    error_log('Produit: ' . $_POST['produit']);
    error_log('Description: ' . $_POST['description']);
    error_log('Prix: ' . $_POST['prix']);
    error_log('Nombre: ' . $_POST['nombre']);
    error_log('Badge: ' . $_POST['badge']);
    error_log('Promo: ' . $_POST['Promo']);
    error_log('Production Company ID: ' . $_POST['production_company_id']);

    if (!isset($_FILES['image_produit']) || $_FILES['image_produit']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['erreur'] = 'Erreur lors du téléchargement de l’image.';
        header('Location: add.php');
        exit;
    }

    $produit = strip_tags($_POST['produit']);
    $description = substr(strip_tags($_POST['description']), 0, 255);
    $prix = (float)$_POST['prix'];
    $nombre = (int)$_POST['nombre'];
    $badge = strip_tags($_POST['badge']);
    $promo = isset($_POST['Promo']) ? (int)$_POST['Promo'] : 0;
    $production_company_id = (int)$_POST['production_company_id'];

    $uploadsDir = 'image_produit';
    if (!is_dir($uploadsDir)) {
        if (!mkdir($uploadsDir, 0777, true)) {
            error_log('Erreur lors de la création du répertoire ' . $uploadsDir);
            $_SESSION['erreur'] = 'Erreur lors de la création du répertoire pour les images.';
            header('Location: add.php');
            exit;
        }
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
    $imagePath = $uploadsDir . '/' . $newFilename;
    if (!move_uploaded_file($_FILES['image_produit']['tmp_name'], $imagePath)) {
        error_log('Erreur lors du déplacement du fichier téléchargé vers ' . $imagePath);
        $_SESSION['erreur'] = 'Erreur lors de l’enregistrement de l’image.';
        header('Location: add.php');
        exit;
    }

    try {
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

        error_log('SQL Query: ' . $sql);
        error_log('Bound Values: ' . json_encode([
            'produit' => $produit,
            'description' => $description,
            'prix' => $prix,
            'nombre' => $nombre,
            'image_produit' => $newFilename,
            'badge' => $badge,
            'Promo' => $promo,
            'production_company_id' => $production_company_id
        ]));

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
    <main class="container text-center">
        <div class="row justify-content-center">
            <section class="col-12">
                <?php
                if (!empty($_SESSION['erreur'])) {
                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['erreur']) . '</div>';
                    unset($_SESSION['erreur']);
                }
                ?>
                <h1 class="text-center" style="color: black;"><i class="fas fa-plus-circle"></i> Ajouter un produit</h1>
                <form method="post" enctype="multipart/form-data" class="text-left">
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
                            <option value="ensemble"><i class="fas fa-layer-group"></i> Ensemble</option>
                            <option value="ares"><i class="fas fa-archway"></i> Ares</option>
                            <option value="bouldog"><i class="fas fa-dog"></i> Bouldog</option>
                            <option value="bucky"><i class="fas fa-bullseye"></i> Bucky</option>
                            <option value="classic"><i class="fas fa-gun"></i> Classic</option>
                            <option value="couteau"><i class="fas fa-knife"></i> Couteau</option>
                            <option value="frenzy"><i class="fas fa-fire"></i> Frenzy</option>
                            <option value="ghost"><i class="fas fa-ghost"></i> Ghost</option>
                            <option value="guardian"><i class="fas fa-shield-alt"></i> Guardian</option>
                            <option value="judge"><i class="fas fa-gavel"></i> Judge</option>
                            <option value="marshal"><i class="fas fa-shield-alt"></i> Marshal</option>
                            <option value="odin"><i class="fas fa-hammer"></i> Odin</option>
                            <option value="operator"><i class="fas fa-crosshairs"></i> Operator</option>
                            <option value="phantom"><i class="fas fa-mask"></i> Phantom</option>
                            <option value="sherif"><i class="fas fa-star"></i> Sherif</option>
                            <option value="shorty"><i class="fas fa-bolt"></i> Shorty</option>
                            <option value="spectre"><i class="fas fa-eye"></i> Spectre</option>
                            <option value="stinger"><i class="fas fa-bug"></i> Stinger</option>
                            <option value="vandal"><i class="fas fa-skull"></i> Vandal</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Promo"><i class="fas fa-percent"></i> Promotion (%)</label>
                        <input type="number" id="Promo" name="Promo" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="production_company_id"><i class="fas fa-industry"></i> Société de production</label>
                        <select class="form-control" id="production_company_id" name="production_company_id" required>
                            <option value="" disabled selected>Choisissez une société de production</option>
                            <?php
                            $sql = 'SELECT id, name FROM production_companies';
                            try {
                                $companies = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                                if (!$companies) {
                                    throw new Exception('No companies found');
                                }
                                foreach ($companies as $company) {
                                    echo '<option value="' . htmlspecialchars($company['id']) . '">' . htmlspecialchars($company['name']) . '</option>';
                                }
                            } catch (Exception $e) {
                                error_log('Erreur lors de la récupération des sociétés de production : ' . $e->getMessage());
                                error_log('SQL Query: ' . $sql);
                                echo '<option value="" disabled>Erreur lors de la récupération des sociétés</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                </form>
                <h2 class="text-center mt-5"><i class="fas fa-list"></i> Liste des produits</h2>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Produit</th>
                                <th>Badge</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['produit']); ?></td>
                                    <td><?php echo htmlspecialchars($product['badge']); ?></td>
                                    <td>
                                        <?php if (!empty($product['image_produit'])): ?>
                                            <img src="image_produit/<?php echo htmlspecialchars($product['image_produit']); ?>" alt="Image du produit" style="max-width: 100px;">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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