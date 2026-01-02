<?php
$title = "Mon profil";
ob_start();
?>
<h2>Mon profil :</h2>

<h3>Mes annonces en ligne :</h3>
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
<h3>Mes achats :</h3>
<div class="ads-grid">
    <?php foreach ($annoncesAchetees as $ad): ?>

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
<h3>Mes Ventes :</h3>
<div class="ads-grid">
    <?php foreach ($annoncesVendues as $ad): ?>

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
<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>