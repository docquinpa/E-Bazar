<?php
$title = "Catégorie";
ob_start();
?>

<h2 class="page-title">Annonces de la catégorie : <?= htmlspecialchars($categoryName) ?></h2>

<div class="ad-list">

    <?php foreach ($annonces as $ad): ?>

        <?php
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
               class="btn-view">Voir</a>

        </div>

    <?php endforeach; ?>

</div>


<!-- PAGINATION -->
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">

        <?php if ($page > 1): ?>
            <a class="page-link"
               href="<?= BASE_URL ?>index.php?action=listCategory&id=<?= $categoryId ?>&page=<?= $page - 1 ?>">
                « Précédent
            </a>
        <?php endif; ?>

        <?php
        // Helper pour afficher un lien
        function pageLinkCat($i, $page, $categoryId) {
            $active = ($i == $page) ? 'active' : '';
            return '<a class="page-link ' . $active . '" href="index.php?action=listCategory&id=' . $categoryId . '&page=' . $i . '">' . $i . '</a>';
        }

        // 1) Toujours afficher les 2 premières pages
        echo pageLinkCat(1, $page, $categoryId);
        if ($totalPages >= 2) {
            echo pageLinkCat(2, $page, $categoryId);
        }

        // 2) Ellipse si on est loin du début
        if ($page > 4) {
            echo '<span class="page-ellipsis">...</span>';
        }

        // 3) Pages autour de la page actuelle
        for ($i = $page - 1; $i <= $page + 1; $i++) {
            if ($i > 2 && $i < $totalPages - 1) {
                echo pageLinkCat($i, $page, $categoryId);
            }
        }

        // 4) Ellipse si on est loin de la fin
        if ($page < $totalPages - 3) {
            echo '<span class="page-ellipsis">...</span>';
        }

        // 5) Toujours afficher les 2 dernières pages
        if ($totalPages > 3) {
            echo pageLinkCat($totalPages - 1, $page, $categoryId);
        }
        if ($totalPages > 2) {
            echo pageLinkCat($totalPages, $page, $categoryId);
        }
        ?>

        <?php if ($page < $totalPages): ?>
            <a class="page-link"
               href="<?= BASE_URL ?>index.php?action=listCategory&id=<?= $categoryId ?>&page=<?= $page + 1 ?>">
                Suivant »
            </a>
        <?php endif; ?>

    </div>
<?php endif; ?>


<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
