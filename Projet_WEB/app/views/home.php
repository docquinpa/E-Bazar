<?php ob_start(); ?>

<h2>Catégories</h2>

<?php if (!empty($categories)) : ?>
    <ul class="category-list">
        <?php foreach ($categories as $cat) : ?>
            <li>
                <a href="index.php?action=listCategory&id=<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
                (<?= $cat['count'] ?> annonces)
            </li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Aucune catégorie disponible.</p>
<?php endif; ?>


<h2>Dernières annonces</h2>

<div class="latest-ads">
    <?php if (!empty($latestAds)) : ?>
        <?php foreach ($latestAds as $ad) : ?>
            <div class="ad-card">
                <a href="index.php?action=viewAd&id=<?= $ad['id'] ?>">
                    <?php if (!empty($ad['thumbnail'])) : ?>
                        <img src="/uploads/<?= htmlspecialchars($ad['thumbnail']) ?>" alt="Photo">
                    <?php else : ?>
                        <img src="/assets/no-image.jpg" alt="Pas d'image">
                    <?php endif; ?>
                </a>

                <h3><?= htmlspecialchars($ad['title']) ?></h3>
                <p><?= number_format($ad['price'], 2, ',', ' ') ?> €</p>

                <a href="index.php?action=viewAd&id=<?= $ad['id'] ?>">Voir l'annonce</a>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucune annonce pour le moment.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = "Accueil";
require __DIR__ . "/layout.php";
?>
