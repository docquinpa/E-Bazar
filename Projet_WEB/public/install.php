<?php

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_FILE', BASE_PATH . '/config/config.php');
define('INSTALLED_FLAG', BASE_PATH . '/storage/.installed');

$error = '';

// Déjà installé ?
if (file_exists(CONFIG_FILE) && file_exists(INSTALLED_FLAG)) {
    header('Location: /');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ============================
    // Récupération des champs
    // ============================

    $db_host     = trim($_POST['db_host'] ?? '');
    $db_user     = trim($_POST['db_user'] ?? '');
    $db_pass     = trim($_POST['db_pass'] ?? '');
    $db_name     = trim($_POST['db_name'] ?? '');
    $admin_user  = trim($_POST['admin_user'] ?? '');
    $admin_pass  = trim($_POST['admin_pass'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');

    // ============================
    // Validation basique
    // ============================

    if ($admin_user === '' || $admin_pass === '' || $admin_email === '') {
        $error = "Veuillez saisir email, identifiant et mot de passe administrateur.";
    } elseif ($db_host === '' || $db_user === '' || $db_name === '') {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }

    // ============================
    // Vérification PRIORITAIRE : config.php écrivable
    // ============================

    if (empty($error)) {

        $configDir  = BASE_PATH . '/config';
        $template   = $configDir . '/config.php.example';
        $configFile = CONFIG_FILE;

        // Dossier config
        if (!is_dir($configDir) && !mkdir($configDir, 0755, true)) {
            $error = "Impossible de créer le dossier /config.";
        }

        // Fichier template
        elseif (!file_exists($template)) {
            $error = "Le fichier config.php.example est introuvable.";
        }

        // Test écriture config.php
        else {
            // Si le fichier existe déjà mais n'est pas écrivable
            if (file_exists($configFile) && !is_writable($configFile)) {
                $error = "Le fichier config.php existe mais n'est pas écrivable.";
            }
            // Si le fichier n'existe pas, tester la capacité à le créer
            elseif (!file_exists($configFile)) {
                if (@file_put_contents($configFile, '') === false) {
                    $error = "Impossible de créer le fichier config.php.";
                } else {
                    // On efface le fichier vide, il sera réécrit plus tard
                    unlink($configFile);
                }
            }
        }
    }
    // ============================
    // Vérification du flag installed
    // ============================

    if (empty($error)) {

        $storageDir = BASE_PATH . '/storage';
        $installedFile = INSTALLED_FLAG;

        // Dossier storage
        if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true)) {
            $error = "Impossible de créer le dossier /storage.";
        }

        // Test écriture installed.flag
        elseif (file_exists($installedFile) && !is_writable($installedFile)) {
            $error = "Le fichier installed.flag existe mais n'est pas écrivable.";
        }

        // Si le fichier n'existe pas, tester la capacité à le créer
        elseif (!file_exists($installedFile)) {
            if (@file_put_contents($installedFile, '') === false) {
                $error = "Impossible de créer le fichier installed.flag.";
            } else {
                unlink($installedFile); // On nettoie, il sera réécrit plus tard
            }
        }
    }



    // ============================
    // Connexion DB
    // ============================

    if (empty($error)) {

        mysqli_report(MYSQLI_REPORT_OFF);
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($mysqli->connect_errno) {
            $error = "Impossible de se connecter à la base de données.";
        } else {

            // Vérifier que la base existe
            $check = $mysqli->query(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA
                 WHERE SCHEMA_NAME = '" . $mysqli->real_escape_string($db_name) . "'"
            );

            if (!$check || $check->num_rows === 0) {
                $error = "La base '$db_name' n'existe pas.";
            }
        }
    }

    // ============================
    // Création des tables
    // ============================

    if (empty($error)) {

        $tables = [
            "CREATE TABLE IF NOT EXISTS utilisateur (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                username VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin','user') NOT NULL DEFAULT 'user'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            "CREATE TABLE IF NOT EXISTS categorie (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nom VARCHAR(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            "CREATE TABLE IF NOT EXISTS annonce (
                id INT AUTO_INCREMENT PRIMARY KEY,
                titre VARCHAR(255) NOT NULL,
                `desc` TEXT,
                price DECIMAL(10,2) NOT NULL,
                livraison SET('Colissimo','Main propre','Chronopost') NULL,
                categorie INT,
                dispo TINYINT(1) DEFAULT 1,
                auteur INT NOT NULL,
                acheteur INT NULL,
                FOREIGN KEY (categorie) REFERENCES categorie(id)
                    ON DELETE SET NULL ON UPDATE CASCADE,
                FOREIGN KEY (auteur) REFERENCES utilisateur(id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (acheteur) REFERENCES utilisateur(id)
                    ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

            "CREATE TABLE IF NOT EXISTS imageannonce (
                id INT AUTO_INCREMENT PRIMARY KEY,
                url VARCHAR(255) NOT NULL,
                annonce_id INT NOT NULL,
                FOREIGN KEY (annonce_id) REFERENCES annonce(id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];

        foreach ($tables as $sql) {
            if (!$mysqli->query($sql)) {
                $error = "Erreur lors de la création des tables.";
                break;
            }
        }
    }

    // ============================
    // Création du compte admin
    // ============================

    if (empty($error)) {

        $hashed = password_hash($admin_pass, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare(
            "INSERT INTO utilisateur (email, username, password, role)
             VALUES (?, ?, ?,'admin')"
        );

        if (!$stmt) {
            $error = "Impossible de préparer la création du compte admin.";
        } else {
            $stmt->bind_param("sss", $admin_email, $admin_user,$hashed);

            if (!$stmt->execute()) {
                $error = "Impossible de créer le compte administrateur.";
            }
        }
    }

    // ============================
    // Génération du fichier config
    // ============================

    if (empty($error)) {

        $templateContent = file_get_contents($template);

        $config = str_replace(
            ['{{DB_HOST}}', '{{DB_USER}}', '{{DB_PASS}}', '{{DB_NAME}}'],
            [$db_host, $db_user, $db_pass, $db_name],
            $templateContent
        );

        if (file_put_contents($configFile, $config) === false) {
            $error = "Impossible d'écrire config.php.";
        }
    }

    if (empty($error)) {
       header('Location: /');
       exit;
    }
}

// Valeurs par défaut
$db_host    = $db_host    ?? '';
$db_user    = $db_user    ?? '';
$db_pass    = $db_pass    ?? '';
$db_name    = $db_name    ?? '';
$admin_user = $admin_user ?? '';
$admin_pass = $admin_pass ?? '';
$admin_email = $admin_email ?? '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Installation du site</title>
    <link rel="stylesheet" href="assets/install.css" />
</head>
<body>
    <h1>Installation du site</h1>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">

        <h3>Base de données existante</h3>

        <label>Hôte MySQL/MariaDB :</label><br>
        <input type="text" name="db_host" value="<?= htmlspecialchars($db_host) ?>" required><br>

        <label>Utilisateur :</label><br>
        <input type="text" name="db_user" value="<?= htmlspecialchars($db_user) ?>" required><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="db_pass" value="<?= htmlspecialchars($db_pass) ?>"><br>

        <label>Nom de la base :</label><br>
        <input type="text" name="db_name" value="<?= htmlspecialchars($db_name) ?>" required><br>

        <h3>Compte administrateur</h3>

        <label>Email admin :</label><br>
        <input type="email" name="admin_email" value="<?= htmlspecialchars($admin_email) ?>" required><br>

        <label>Admin user :</label><br>
        <input type="text" name="admin_user" value="<?= htmlspecialchars($admin_user) ?>" required><br>

        <label>Mot de passe admin :</label><br>
        <input type="password" name="admin_pass" value="<?= htmlspecialchars($admin_pass) ?>" required><br>

        <button type="submit">Installer</button>
    </form>
</body>
</html>
