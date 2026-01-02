<?php
$title = htmlspecialchars($annonce['titre']);
ob_start();
?>

<div class="ad-container">

    <!-- Titre + prix -->
    <h1><?= htmlspecialchars($annonce['titre']) ?></h1>
    <p class="ad-price"><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</p>

    <!-- Photos -->
    <div class="ad-photos">
        <?php if (!empty($images)): ?>
            <div class="photo-viewer" data-images='<?= json_encode(array_column($images, "url")) ?>'>
                <img class="viewer-img" src="<?= BASE_URL ?>upload/<?= htmlspecialchars($images[0]['url']) ?>" alt="">
                <button class="viewer-arrow left">‹</button>
                <button class="viewer-arrow right">›</button>
            </div>
        <?php else: ?>
            <img src="<?= BASE_URL ?>assets/no-image.jpg" class="single-photo" alt="Aucune photo">
        <?php endif; ?>
    </div>

    <!-- Description -->
    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($annonce['description'])) ?></p>

    <!-- Livraison -->
    <h2>Modes de livraison</h2>
    <ul>
        <?php foreach ($annonce['livraison'] as $mode): ?>
            <li><?= htmlspecialchars($mode) ?></li>
        <?php endforeach; ?>
    </ul>

    <!-- Actions -->
    <div class="ad-actions">

        <?php if ($annonce['dispo']): ?>
            <a href="<?= BASE_URL ?>index.php?action=buy&id=<?= $annonce['id'] ?>" class="btn-buy">
                Acheter
            </a>
        <?php else: ?>
            <p class="sold">Cette annonce est déjà vendue</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $annonce['auteur']): ?>
            <a href="<?= BASE_URL ?>index.php?action=deleteAd&id=<?= $annonce['id'] ?>" class="btn-delete">
                Supprimer l'annonce
            </a>
        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
