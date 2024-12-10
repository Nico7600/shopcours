<?php
$image_path = 'image_produit/' . $produit['image_produit'];
$promoPrice = $produit['prix'];
if (is_numeric($produit['Promo']) && $produit['Promo'] > 0) {
    $promoPrice *= (1 - $produit['Promo'] / 100);
}
if ($isPrime && $produit['production_company'] === 'Amazon') {
    $promoPrice *= 0.9;
}
?>
<div class="col-md-4 col-sm-6 mb-4">
    <div class="card" onclick="window.location.href='details.php?id=<?= $produit['id'] ?>'">
        <img src="<?= htmlspecialchars($image_path); ?>" class="card-img-top" alt="<?= htmlspecialchars($produit['produit']); ?>">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($produit['produit']); ?></h5>
            <p class="card-text"><strong>Produit par :</strong> <?= htmlspecialchars($produit['production_company'] ?? 'Inconnu'); ?></p>
            <p class="card-price"><?= number_format($promoPrice, 2); ?> €</p>
        </div>
    </div>
</div>
