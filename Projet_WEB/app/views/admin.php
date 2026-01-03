<?php
$title = "Admin";
ob_start();
?>
<h2>Panneau d'administration :</h2>
<h3>Ajouter une catégorie :</h3>
<div class="form-container">
    <form action="<?= BASE_URL ?>index.php?action=addCategory" method="POST">
        <label for="name_cat">Donnez le nom de votre Catégorie :</label>
        <input id="name_cat" type="text" name="name" required>
        <input type="submit" value="Ajouter">
    </form>
</div>
<div class="form-container">
    <form action="<?= BASE_URL ?>index.php?action=modifyCategory" method="POST">
        <label for="choice_cat">Choisir la Catégorie à modifier:</label>
        <select id="choice_cat" name="category">
            <option value="">Choisir une option</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?=$cat["nom"]?>"><?=$cat["nom"]?></option>
            <?php endforeach; ?>
        </select>
        <label for="name_cat2">Donnez le nouveau nom de votre Catégorie :</label>
        <input id="name_cat2" type="text" name="name" required>
        <input type ="submit" value="Modifier">
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>