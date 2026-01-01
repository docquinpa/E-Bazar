<?php ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= isset($title) ? htmlspecialchars($title) : "Petites Annonces" ?></title>
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="icon" href=<?php ?>"/assets/icon.svg">
</head>

<body>

<header class="site-header">
    <div class="logo">
        <img src="/assets/icon.svg" alt="Logo Ebazar">
    </div>

    <nav class="main-nav">
        <a href="index.php?action=home">Accueil</a>

        <?php if (!empty($_SESSION['user'])) : ?>
            <a href="index.php?action=dashboard">Mon espace</a>

            <?php if ($_SESSION['user']['role'] === 'admin') : ?>
                <a href="index.php?action=admin">Admin</a>
            <?php endif; ?>

            <a href="index.php?action=logout">Déconnexion</a>
        <?php else : ?>
            <a href="index.php?action=login">Connexion</a>
            <a href="index.php?action=register">Inscription</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <?php
        // Ici on injecte le contenu de la vue
        if (isset($content)) {
            echo $content;
        }
    ?>
</main>

<footer>
    <p>Projet Web — Plateforme de petites annonces</p>
</footer>

</body>
</html>
