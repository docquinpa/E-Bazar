<?php ob_start(); ?>
<h2>Connexion</h2>

<?php if (!empty($error)) : ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!empty($_GET['success'])) : ?>
    <p style="color:green;">Compte créé avec succès, vous pouvez vous connecter.</p>
<?php endif; ?>
<div class="form-container">
    <form method="post" action="<?= BASE_URL ?>index.php?action=login">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Connexion</button>
    </form>
</div>
<?php
$content = ob_get_clean();
$title = "Connexion";
require __DIR__ . "/layout.php";
?>
