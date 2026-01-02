<?php
$title = "Catégorie";
ob_start();
?>

<h2>Annonces de la catégorie : <?= htmlspecialchars($categoryName) ?></h2>

<div class="ads-grid">
    <?php foreach ($annonces as $ad): ?>
        <div class="ad-card">

            <!-- Container image avec data-images -->
            <div class="ad-image"
                 data-images='<?= json_encode($ad["photos"]) ?>'>
                <img src="<?= BASE_URL ?>uploads/<?= $ad["photos"][0] ?>" alt="">
            </div>

            <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
            <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>

            <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>"
               class="btn">Voir l'annonce</a>
        </div>
    <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
