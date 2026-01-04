<?php
$title = "Toutes les annonces";
ob_start();
?>

<h2 class="page-title">Toutes les annonces</h2>

<div class="ad-list">

    <?php foreach ($annonces as $ad): ?>

        <?php
        // Récupération des images de l'annonce
        $images = $imagesByAd[$ad['id']] ?? [];
        $urls = array_column($images, 'url');
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-row">

            <img class="ad-thumb"
                 src="<?= BASE_URL ?>upload/<?= $thumbnail ?>"
                 alt="">

            <div class="ad-info">
                <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>
            </div>

            <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>"
               class="btn-view">
                Voir
            </a>

        </div>

    <?php endforeach; ?>

</div>


<!-- PAGINATION -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">

        <?php if ($page > 1): ?>
            <a class="page-link"
               href="<?= BASE_URL ?>index.php?action=listAll&page=<?= $page - 1 ?>">
                « Précédent
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="page-link <?= ($i == $page) ? 'active' : '' ?>"
               href="<?= BASE_URL ?>index.php?action=listAll&page=<?= $i ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a class="page-link"
               href="<?= BASE_URL ?>index.php?action=listAll&page=<?= $page + 1 ?>">
                Suivant »
            </a>
        <?php endif; ?>

    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
