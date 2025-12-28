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

    $db_host    = trim($_POST['db_host'] ?? '');
    $db_user    = trim($_POST['db_user'] ?? '');
    $db_pass    = trim($_POST['db_pass'] ?? '');
    $db_name    = trim($_POST['db_name'] ?? '');
    $admin_user = trim($_POST['admin_user'] ?? '');
    $admin_pass = trim($_POST['admin_pass'] ?? '');

    if ($admin_user === '' || $admin_pass === '') {
        $error = "Veuillez saisir un identifiant et un mot de passe administrateur.";
    } elseif ($db_host === '' || $db_user === '' || $db_name === '') {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        mysqli_report(MYSQLI_REPORT_OFF);
        // Connexion directe à la base existante
        $mysqli = @new mysqli($db_host, $db_user, $db_pass, $db_name);

        if ($mysqli->connect_errno) {
            // Message propre pour l'utilisateur
            $error = "Impossible de se connecter à la base de données avec ces identifiants.";
        } else {

            // Vérifier que la base existe réellement
            $check = $mysqli->query(
                "SELECT SCHEMA_NAME
                 FROM INFORMATION_SCHEMA.SCHEMATA
                 WHERE SCHEMA_NAME = '" . $mysqli->real_escape_string($db_name) . "'"
            );

            if (!$check || $check->num_rows === 0) {
                $error = "La base '$db_name' n'existe pas. Merci de la créer avant l'installation.";
            } else {

                // Création des tables nécessaires
                $createUsersSql = "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(20) NOT NULL
                )";

                if (!$mysqli->query($createUsersSql)) {
                    $error = "Impossible de créer la table des utilisateurs.";
                } else {
                    // Création du compte admin choisi
                    $hashed = password_hash($admin_pass, PASSWORD_DEFAULT);

                    $stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
                    if (!$stmt) {
                        $error = "Impossible de créer le compte administrateur.";
                    } else {
                        $stmt->bind_param("ss", $admin_user, $hashed);
                        if (!$stmt->execute()) {
                            $error = "Impossible d'enregistrer le compte administrateur.";
                        } else {

                            // Génération du fichier config
                            $templatePath = BASE_PATH . '/config/config.php.example';

                            if (!file_exists($templatePath)) {
                                $error = "Le fichier de configuration modèle est introuvable.";
                            } else {
                                $template = file_get_contents($templatePath);

                                $config = str_replace(
                                    ['{{DB_HOST}}', '{{DB_USER}}', '{{DB_PASS}}', '{{DB_NAME}}'],
                                    [$db_host, $db_user, $db_pass, $db_name],
                                    $template
                                );

                                if (!is_dir(BASE_PATH . '/config')) {
                                    mkdir(BASE_PATH . '/config', 0755, true);
                                }

                                if (file_put_contents(CONFIG_FILE, $config) === false) {
                                    $error = "Impossible d'écrire le fichier de configuration. Vérifiez les permissions du dossier config/.";
                                } else {

                                    // Flag d'installation
                                    if (!is_dir(BASE_PATH . '/storage')) {
                                        mkdir(BASE_PATH . '/storage', 0755, true);
                                    }

                                    if (file_put_contents(INSTALLED_FLAG, date('Y-m-d H:i:s')) === false) {
                                        $error = "Impossible de créer le fichier d'installation. Vérifiez les permissions du dossier storage/.";
                                    } else {
                                        // Redirection vers le site
                                        header('Location: /');
                                        exit;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

// valeurs par défaut pour le premier affichage
$db_host    = $db_host    ?? '';
$db_user    = $db_user    ?? '';
$db_pass    = $db_pass    ?? '';
$db_name    = $db_name    ?? '';
$admin_user = $admin_user ?? '';
$admin_pass = $admin_pass ?? '';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Installation du site</title>
    <link rel="stylesheet" href="assets/install.css" />
    <link rel="icon" href="assets/icon.svg" />
</head>
<body>
    <h1>Installation du site</h1>

    <?php if (!empty($error)): ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">

        <h3>Base de données existante</h3>
        <label>Hôte MySQL/MariaDB :</label><br>
        <input type="text" name="db_host" value="<?php echo htmlspecialchars($db_host); ?>" required><br>

        <label>Utilisateur :</label><br>
        <input type="text" name="db_user" value="<?php echo htmlspecialchars($db_user); ?>" required><br>

        <label>Mot de passe :</label><br>
        <input type="password" name="db_pass" value="<?php echo htmlspecialchars($db_pass); ?>"><br>

        <label>Nom de la base (doit exister) :</label><br>
        <input type="text" name="db_name" value="<?php echo htmlspecialchars($db_name); ?>" required><br>

        <h3>Compte administrateur</h3>
        <label>Identifiant admin :</label><br>
        <input type="text" name="admin_user" value="<?php echo htmlspecialchars($admin_user); ?>" required><br>

        <label>Mot de passe admin :</label><br>
        <input type="password" name="admin_pass" value="<?php echo htmlspecialchars($admin_pass); ?>" required><br>

        <button type="submit">Installer</button>
    </form>
</body>
</html>
