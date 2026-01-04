<?php ob_start(); ?>
<h2 class="login-title">Connexion</h2>

<?php if (!empty($error)) : ?>
    <p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if (!empty($_GET['success'])) : ?>
    <p class="success-message">Compte créé avec succès, vous pouvez vous connecter.</p>

    <?php if (!empty($_GET['redirect'])) : ?>
        <script>
            setTimeout(function() {
                window.location.href = "index.php?action=<?= htmlspecialchars($_GET['redirect']) ?>";
            }, 2000);
        </script>
    <?php endif; ?>
<?php endif; ?>

<div class="form-container">
    <form method="post" action="<?= BASE_URL ?>index.php?action=login">
        <label>Email :</label><br>
        <input type="email" name="email" required><br><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>

        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">

        <button type="submit">Connexion</button>
    </form>

    <a class="register-link"
       href="index.php?action=register&redirect=<?= urlencode($_GET['redirect'] ?? '') ?>">
        Pas encore de compte ?
    </a>
</div>

<?php
$content = ob_get_clean();
$title = "Connexion";
require __DIR__ . "/layout.php";
?>
