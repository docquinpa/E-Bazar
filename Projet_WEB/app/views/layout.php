<?php ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title><?= isset($title) ? htmlspecialchars($title) : "E-Bazar" ?></title>
        <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
        <link rel="icon" href="<?= BASE_URL ?>assets/icon.svg">
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    </head>

    <body>

        <header class="site-header">
            <div class="logo">
                <img src="<?= BASE_URL ?>assets/icon.svg" alt="Logo Ebazar">
            </div>

            <nav class="main-nav">
                <a href="index.php?action=home">Accueil</a>

                <?php if (!empty($_SESSION['user'])) : ?>

                    <?php if ($_SESSION['user']['role'] !== 'admin') : ?>
                        <a href="index.php?action=profile">Mon espace</a>
                    <?php endif; ?>

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

        <footer class="site-footer">
            <p>E-Bazar | Projet de M1 Informatique | Langages Web</p>
            <p>
                <a href="https://youtu.be/dQw4w9WgXcQ?list=RDdQw4w9WgXcQ&t=45" target="_blank">
                    <img style="border:0;width:88px;height:31px"
                        src="https://jigsaw.w3.org/css-validator/images/vcss"
                        alt="CSS Valide !" />
                </a>
            </p>
        </footer>
        <div id="lightbox" class="lightbox hidden">
            <span class="close-lightbox">×</span>
            <img id="lightbox-img" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="">
            <button class="lightbox-arrow left">‹</button>
            <button class="lightbox-arrow right">›</button>
        </div>

        <script>
        const BASE_URL = "<?= BASE_URL ?>";
        </script>
        <script src="<?= BASE_URL ?>js/homeCarousel.js"></script>
        <script src="<?= BASE_URL ?>js/lightbox.js"></script>
        <script src="<?= BASE_URL ?>js/chooseList.js"></script>
    </body>
</html>
