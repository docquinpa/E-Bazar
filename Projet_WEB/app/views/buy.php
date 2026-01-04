<?php
$title = "Confirmer l'achat";
ob_start();
?>

<div class="buy-container">

    <h1 class="page-title">Confirmer l'achat</h1>

    <div class="buy-ad-info">
        <h2><?= htmlspecialchars($annonce['titre']) ?></h2>
        <p class="price"><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</p>
    </div>

    <form action="index.php?action=confirmBuy" method="POST" class="buy-form">

        <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">

        <label for="livraison">Choisissez un mode de livraison :</label>
        <select name="livraison" id="livraison" required>
            <option value="" disabled selected>-- Choisir un mode de livraison--</option>
            <?php foreach ($annonce['livraison'] as $mode): ?>
                <option value="<?= htmlspecialchars($mode) ?>">
                    <?= htmlspecialchars($mode) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn-confirm">Confirmer l'achat</button>
    </form>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
