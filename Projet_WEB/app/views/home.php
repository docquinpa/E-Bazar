<?php
$title = "Accueil";
ob_start();
?>
<div class="category-bar">
    <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>index.php?action=listCategory&id=<?= $cat['id'] ?>" class="category-pill">
            <?= htmlspecialchars($cat['nom']) ?>
            <span class="count"><?= $cat['count'] ?? 0 ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="home-container">

    <!-- BARRE DE RECHERCHE -->
    <div class="search-bar">
        <div class="search-box">
        <form action="<?= BASE_URL ?>index.php" method="GET" class="search-form">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" placeholder="Rechercher une annonce...">
            <button type="submit" class="search-btn">
                <ion-icon name="search-outline"></ion-icon>
            </button>
        </form>

    </div>
</div>


    <!-- BOUTON DEPOSER UNE ANNONCE -->
    <div class="post-ad">
        <a href="<?= BASE_URL ?>index.php?action=addAd" class="btn-post">Déposer une annonce</a>
    </div>

    <!-- CARROUSEL DES 4 DERNIÈRES ANNONCES -->
    <h2>Dernières annonces</h2>

    <div class="carousel">
        <div class="carousel-track">

            <?php foreach ($annonces as $ad): ?>

                <?php
                // Récupération des images
                $images = $imagesByAd[$ad['id']] ?? [];
                $urls = array_column($images, 'url');
                $thumbnail = $urls[0] ?? "no-image.jpg";
                ?>
                <a href="<?= BASE_URL ?>index.php?action=viewAd&id=<?= $ad["id"] ?>" class="btn">
                    <div class="carousel-item">
                        <img src="<?= BASE_URL ?>upload/<?= $thumbnail ?>" alt="">
                        <h3><?= htmlspecialchars($ad["titre"]) ?></h3>
                        <p><?= number_format($ad["prix"], 2, ',', ' ') ?> €</p>

                    </div>
                </a>

            <?php endforeach; ?>

        </div>
    </div>

    <!-- BOUTON VOIR PLUS -->
    <div class="see-more">
        <a href="<?= BASE_URL ?>index.php?action=listAll" class="btn-more">Voir plus d'annonces</a>
    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
