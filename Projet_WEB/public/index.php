<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_FILE', BASE_PATH . '/config/config.php');
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');

// 1. Vérification installation
if (!file_exists(CONFIG_FILE)) {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    header('Location: ' . $base . '/install.php');
    exit;
}

// 2. Chargement config
$config = require CONFIG_FILE;

// 3. Session
session_start();

// 4. Connexion PDO
require_once BASE_PATH . '/app/core/Database.php';
$pdo = Database::getInstance();

// 5. Chargement modèles
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Annonce.php';
require_once BASE_PATH . '/app/models/AnnonceImage.php';
require_once BASE_PATH . '/app/models/Categorie.php';
require_once BASE_PATH . '/app/models/Vente.php';

// 6. Chargement contrôleurs
require_once BASE_PATH . '/app/controllers/UserController.php';
require_once BASE_PATH . '/app/controllers/AnnonceController.php';
require_once BASE_PATH . '/app/controllers/VenteController.php';
require_once BASE_PATH . '/app/controllers/CategoryController.php';

// 7. Utils
require_once BASE_PATH . '/app/utils/utils.php';

// 8. Router
$action = $_GET['action'] ?? 'home';

$userController     = new UserController($pdo);
$annonceController  = new AnnonceController($pdo);
$venteController    = new VenteController($pdo);
$categoryController = new CategoryController($pdo);

switch ($action) {

    /* ============================
       AUTHENTIFICATION
    ============================ */
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


    /* ============================
       PROFIL
    ============================ */
    case 'profile':
        $userController->profile();
        break;


    /* ============================
       ANNONCES
    ============================ */
    case 'viewAd':
        $annonceController->viewAd();
        break;

    case 'listCategory':
        $annonceController->listCategory();
        break;

    case 'addAd':
        $annonceController->addAd();
        break;

    case 'listAll':
        $annonceController->listAll();
        break;

    case 'listSearch':
        $annonceController->listSearch();
        break;

    case 'deleteAd':
        $annonceController->deleteAd();
        break;

    case 'masquer':
        $annonceController->masquer();
        break;

    case 'demasquer':
        $annonceController->demasquer();
        break;



    /* ============================
       VENTES
    ============================ */
    case 'buy':
        $venteController->buy();
        break;

    case 'confirmBuy':
        $venteController->confirmBuy();
        break;

    case 'markSent':
        $venteController->markSent();
        break;

    case 'markReceived':
        $venteController->markReceived();
        break;


    /* ============================
       ADMIN CATÉGORIES
    ============================ */
    case 'admin':
        $userController->admin();
        break;

    case 'addCategory':
        $categoryController->addCategory();
        break;

    case 'updateCategory':
        $categoryController->updateCategory();
        break;


    case 'searchUser' :
        $userController->searchUser();
        break;
        
    case 'deleteUser':
        $userController->deleteUser();
        break;
    


    /* ============================
       HOME
    ============================ */

    case 'home':
    default:
        $annonceController->home();
        break;
}

?>
