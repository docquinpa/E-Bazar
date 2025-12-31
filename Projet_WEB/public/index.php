<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_FILE', BASE_PATH . '/config/config.php');

// 1. Vérification installation
if (!file_exists(CONFIG_FILE)) {
    header('Location: /install.php');
    exit;
}

// 2. Chargement config
$config = require CONFIG_FILE;

// 3. Session
session_start();

// 4. Connexion PDO
require_once BASE_PATH . '/app/core/Database.php';

$pdo = Database::getInstance();

// 5. Chargement MVC
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/controllers/UserController.php';

// 6. Router
$action = $_GET['action'] ?? 'home';
$userController = new UserController($pdo);

switch ($action) {

    case 'login':
        $_SERVER['REQUEST_METHOD'] === 'POST'
            ? $userController->login()
            : $userController->showLoginForm();
        break;

    case 'register':
        $_SERVER['REQUEST_METHOD'] === 'POST'
            ? $userController->register()
            : $userController->showRegisterForm();
        break;

    case 'logout':
        $userController->logout();
        break;

    default:
        require BASE_PATH . '/app/views/home.php';
        break;
}
?>
