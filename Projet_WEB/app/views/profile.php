<?php
$title = "Mon profil";
ob_start();
?>
<h2>Mon profil :</h2>

<h3 class="centerTitle">Mes annonces en ligne :</h3>
<?php if (empty($annoncesEnVente)): ?>
    <p class="info-result">Aucune annonce</p>
<?php else:?>
    <div class="ads-grid">
        <?php foreach ($annoncesEnVente as $ad): ?>

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
<?php endif ?>
<h3 class="centerTitle">Mes achats :</h3>
<?php if (empty($annoncesAchetees)): ?>
    <p class="info-result">Aucune annonce</p>
<?php else:?>
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
<?php endif ?>
<h3 class="centerTitle">Mes Ventes :</h3>
<?php if (empty($annoncesVendues)): ?>
    <p class="info-result">Aucune annonce</p>
<?php else:?>
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
<?php endif ?>
<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
