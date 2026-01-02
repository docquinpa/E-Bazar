<?php
$title = "Catégorie";
ob_start();
?>

<h2>Annonces de la catégorie : <?= htmlspecialchars($categoryName) ?></h2>

<div class="ads-grid">
    <?php foreach ($annonces as $ad): ?>

        <?php
        // Récupération des images de l'annonce
        $images = $imagesByAd[$ad['id']] ?? [];

        // Liste des URLs
        $urls = array_column($images, 'url');

        // Vignette (première image ou fallback)
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-card">

            <div class="ad-image"
                 data-images='<?= json_encode($urls) ?>'>
                <img src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">
            </div>

            <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
            <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>

            <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>"
               class="btn">Voir l'annonce</a>
        </div>

    <?php endforeach; ?>
</div>

<!-- PAGINATION -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="page-link <?= ($i == $page) ? 'active' : '' ?>"
               href="<?= BASE_URL ?>index.php?action=listCategory&id=<?= $categoryId ?>&page=<?= $i ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
