<?php
$title = "Admin";
ob_start();
?>
<h2 class="page-title">Panneau d'administration :</h2>
<?php if (isset($error)): ?>
    <p class="error-message"><?= $error ?></p>
<?php endif ?>
<?php if (isset($success)): ?>
    <p class="success-message"><?= $success ?></p>
<?php endif ?>
<h3 class="centerTitle">Ajouter une catégorie :</h3>
<div class="form-container">
    <form action="<?= BASE_URL ?>index.php?action=addCategory" method="POST">
        <label for="name_cat">Donnez le nom de votre Catégorie :</label>
        <input id="name_cat" type="text" name="name" required>
        <input type="submit" value="Ajouter">
    </form>
</div>
<div class="form-container">
    <form action="<?= BASE_URL ?>index.php?action=updateCategory" method="POST">
        <label for="choice_cat">Choisir la Catégorie à modifier:</label>
        <select id="choice_cat" name="category">
            <option value="" disabled selected>Choisir une option</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?=$cat["nom"]?>"><?=$cat["nom"]?></option>
            <?php endforeach; ?>
        </select>
        <label for="name_cat2">Donnez le nouveau nom de votre Catégorie :</label>
        <input id="name_cat2" type="text" name="name" required>
        <input type ="submit" value="Modifier">
    </form>
</div>
<h3 class="centerTitle">Rechercher un utilisateur :</h3> 
<div class="form-container">
    <form method="GET" action="<?= BASE_URL ?>index.php?action=searchUser">
        <input type="hidden" name="action" value="searchUser">
        <input type="text" name="search" placeholder="Nom" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Rechercher</button>
    </form> 
</div>
<?php if (!isset($userSearch) || empty($userSearch)): ?>
    <p class="info-result">Aucun résultat</p>
<?php else: ?>
    <table class="tableSearch">
        <tr> <th>ID</th> <th>Email</th> <th>Pseudo</th> <th>Rôle</th> <th>Action</th></tr>
        <?php foreach ($userSearch as $u): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td> <?php if ($u['role'] !== 'admin'): ?>
                <form action="<?= BASE_URL ?>index.php?action=deleteUser" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit">Supprimer</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif;?>
<?php
$content = ob_get_clean();
require __DIR__ . "/layout.php";
?>
