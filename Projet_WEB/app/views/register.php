<?php ob_start(); ?>

<h2>Inscription</h2>

<?php if (!empty($error)) : ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<div class="form-container">
    <form method="post" action="index.php?action=register">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Nom d'utilisateur :</label><br>
        <input type="text" name="username" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}"
        title="Au moins 8 caractères, une majuscule, une minuscule et un chiffre" required><br><br>

        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">

        <button type="submit">Créer mon compte</button>
    </form>
</div>
<?php
$content = ob_get_clean();
$title = "Inscription";
require __DIR__ . "/layout.php";
?>
