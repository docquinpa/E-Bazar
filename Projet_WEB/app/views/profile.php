<?php
$title = "Mon profil";
ob_start();
?>
<h2 class="page-title">Mon profil</h2>


<!-- ============================
     1) ANNONCES EN VENTE
============================ -->
<h3 class="page-title">Mes annonces en ligne :</h3>

<div class="ad-list">

    <?php foreach ($annoncesEnVente as $ad): ?>
        <?php
        $images = $imagesByAd[$ad['id']] ?? [];
        $urls = array_column($images, 'url');
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-row">

            <img class="ad-thumb" src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">

            <div class="ad-info">
                <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>
            </div>

            <div class="ad-actions">
                <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>" class="btn-view">Voir</a>

                <a href="<?= BASE_URL ?>index.php?action=deleteAd&id=<?= $ad["id"] ?>"
                   class="btn-delete"
                   onclick="return confirm('Supprimer cette annonce ?');">
                    Supprimer
                </a>

                <a href="<?= BASE_URL ?>index.php?action=hideAd&id=<?= $ad["id"] ?>"
                   class="btn-hide">
                    Masquer
                </a>
            </div>

        </div>

    <?php endforeach; ?>

</div>



<!-- ============================
     2) ANNONCES VENDUES (À LIVRER)
============================ -->
<h3 class="page-title">Mes ventes (à livrer) :</h3>

<div class="ad-list">

    <?php foreach ($annoncesVendues as $ad): ?>
        <?php
        $images = $imagesByAd[$ad['id']] ?? [];
        $urls = array_column($images, 'url');
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-row">

            <img class="ad-thumb" src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">

            <div class="ad-info">
                <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>
            </div>

            <div class="ad-actions">
                <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>" class="btn-view">Voir</a>

                <a href="<?= BASE_URL ?>index.php?action=markDelivered&id=<?= $ad["id"] ?>"
                   class="btn-primary"
                   onclick="return confirm('Confirmer la livraison ?');">
                    Marquer comme livré
                </a>
            </div>

        </div>

    <?php endforeach; ?>

</div>



<!-- ============================
     3) ANNONCES ACHETÉES
============================ -->
<h3 class="page-title">Mes achats :</h3>

<div class="ad-list">

    <?php foreach ($annoncesAchetees as $ad): ?>
        <?php
        $images = $imagesByAd[$ad['id']] ?? [];
        $urls = array_column($images, 'url');
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-row">

            <img class="ad-thumb" src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">

            <div class="ad-info">
                <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>
            </div>

            <div class="ad-actions">
                <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>" class="btn-view">Voir</a>

                <a href="<?= BASE_URL ?>index.php?action=confirmReception&id=<?= $ad["id"] ?>"
                   class="btn-primary"
                   onclick="return confirm('Confirmer la réception du bien ?');">
                    J'ai reçu le bien
                </a>
            </div>

        </div>

    <?php endforeach; ?>

</div>



<!-- ============================
     4) ANNONCES LIVRÉES (TA CONDITION)
============================ -->
<h3 class="page-title">Biens livrés :</h3>

<div class="ad-list">

    <?php foreach ($annoncesLivrees as $ad): ?>
        <?php
        $images = $imagesByAd[$ad['id']] ?? [];
        $urls = array_column($images, 'url');
        $thumbnail = $urls[0] ?? "no-image.jpg";
        ?>

        <div class="ad-row">

            <img class="ad-thumb" src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">

            <div class="ad-info">
                <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                <p class="price"><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>
            </div>

            <div class="ad-actions">
                <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>" class="btn-view">Voir</a>
            </div>

        </div>

    <?php endforeach; ?>

</div>


<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
