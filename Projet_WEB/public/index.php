<?php

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_FILE', BASE_PATH . '/config/config.php');

// 1. Vérification : installation faite ?
if (!file_exists(CONFIG_FILE)) {
    header('Location: /install.php');
    exit;
}

// 2. Chargement configuration
$config = require CONFIG_FILE;


// 4. Session
session_start();
?>
