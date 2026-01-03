<?php
$title = "Créer une annonce";
ob_start();
?>

<div class="page-container">

    <h2 class="page-title">Créer une annonce</h2>

    <form action="<?= BASE_URL ?>index.php?action=addAd"
          method="POST"
          enctype="multipart/form-data"
          class="ad-form">

        <!-- Titre -->
        <div class="form-group">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" id="titre" name="titre" class="form-input" required>
        </div>

        <!-- Description -->
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="5" class="form-textarea" required></textarea>
        </div>

        <!-- Prix -->
        <div class="form-group">
            <label for="prix" class="form-label">Prix (€)</label>
            <input type="number" id="prix" name="prix" step="0.01" min="0" class="form-input" required>
        </div>

        <!-- Catégorie -->
        <div class="form-group">
            <label for="categorie" class="form-label">Catégorie</label>
            <select name="categorie" id="categorie" class="form-select" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>">
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Livraison -->
        <div class="form-group">
            <label class="form-label">Modes de livraison</label>
            <div class="checkbox-group">
                <?php foreach ($livraisonOptions as $option): ?>
                    <label class="checkbox-item">
                        <input type="checkbox" name="livraison[]" value="<?= $option ?>" class="checkbox-input">
                        <span class="checkbox-label"><?= htmlspecialchars($option) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upload images -->
        <div class="form-group">
            <label for="images" class="form-label">Images (max 5, JPG uniquement)</label>
            <input type="file" name="images[]" id="images" accept="image/jpeg" multiple class="form-file">
        </div>

        <button type="submit" class="btn btn-primary">Créer l'annonce</button>

    </form>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
