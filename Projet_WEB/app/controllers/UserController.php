<?php

class UserController {

    private $userModel;
    private $annonceModel;
    private $venteModel;
    private $imageModel;
    private $categorieModel;

    public function __construct($pdo) {
        $this->userModel   = new UserModel($pdo);
        $this->annonceModel = new AnnonceModel($pdo);
        $this->venteModel   = new VenteModel($pdo);
        $this->imageModel   = new AnnonceImageModel($pdo);
        $this->categorieModel   = new Categorie($pdo);
    }

    /* ============================
       FORMULAIRE D'INSCRIPTION
    ============================ */
    public function showRegisterForm() {
        require __DIR__ . '/../views/register.php';
    }

    /* ============================
       INSCRIPTION
    ============================ */
    public function register() {

        $redirect = $_GET['redirect'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $email    = $_POST['email'] ?? null;
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            $redirectPost = $_POST['redirect'] ?? null;

            try {
                $this->userModel->createUser($email, $username, $password, "user");

                $url = "index.php?action=login&success=1";

                if (!empty($redirectPost)) {
                    $url .= "&redirect=" . urlencode($redirectPost);
                }

                header("Location: $url");
                exit;

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        require __DIR__ . '/../views/register.php';
    }


    /* ============================
       FORMULAIRE DE CONNEXION
    ============================ */
    public function showLoginForm() {
        require __DIR__ . '/../views/login.php';
    }

    /* ============================
       CONNEXION
    ============================ */
    public function login() {

        $email    = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        $redirect = $_POST['redirect'] ?? ($_GET['redirect'] ?? null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $user = $this->userModel->authentification($email, $password);

            if ($user) {

                session_regenerate_id(true);
                $_SESSION['user'] = $user;

                if (!empty($redirect)) {
                    header("Location: index.php?action=" . urlencode($redirect));
                    exit;
                }

                header("Location: index.php?action=home");
                exit;

            } else {
                $error = "Identifiants incorrects";
            }
        }

        require __DIR__ . '/../views/login.php';
    }


    /* ============================
       DÉCONNEXION
    ============================ */
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=home");
        exit;
    }


    /* ============================
       PAGE PROFIL UTILISATEUR
       4 LISTES :
       - annonces en vente
       - annonces vendues
       - annonces achetées
       - annonces livrées
    ============================ */
    public function profile() {

        if (!isset($_SESSION["user"])) {
            $redirect = urlencode("profile");
            header("Location: index.php?action=login&redirect=$redirect");
            exit;
        }

        $userId = $_SESSION["user"]["id"];

        /* --- 1) Annonces en vente (pas dans Vente) --- */
        $annoncesEnVente = $this->annonceModel->getAllAnnoncesEnVenteByAuteur($userId);

        /* --- 2) Annonces vendues (dans Vente, vendeur = user) --- */
        $annoncesVendues = $this->venteModel->getAnnoncesVenduesByVendeur($userId);

        /* --- 3) Annonces achetées (dans Vente, acheteur = user) --- */
        $annoncesAchetees = $this->venteModel->getAnnoncesAcheteesByAcheteur($userId);

        /* --- 4) Annonces livrées (envoyées par le vendeur) --- */
        $annoncesLivrees = $this->venteModel->getAnnoncesLivreesByVendeur($userId);

        /* --- Charger les images pour toutes les annonces --- */
        $imagesByAd = [];

        foreach ([$annoncesEnVente, $annoncesVendues, $annoncesAchetees, $annoncesLivrees] as $list) {
            foreach ($list as $ad) {
                $imagesByAd[$ad['id']] = $this->imageModel->getImagesByAnnonce($ad['id']);
            }
        }

        require __DIR__ . '/../views/profile.php';
    }

   public function searchUser() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location: index.php?action=login");
            exit;
        }
        $searchedName = $_GET['search'] ?? null;
        if ($searchedName) {
            $userSearch = $this->userModel->getUserLikeUsername($searchedName);
        }
        $categories = $this->categorieModel->getAllCategories();
        require __DIR__ . "/../views/admin.php";
    }

    public function deleteUser() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location: index.php?action=login");
            exit;
        }
        $id = $_POST["id"] ?? null;
        if ($id) {
            try {
                if (!$this->userModel->deleteUser($id)){
                    $error = "Erreur lors de la suppression";
                } else {
                    $success = "L'utilisateur a bien été supprimé";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->admin($error ?? null, $success ?? null);
    }

    public function admin($error = null, $success = null) {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location:". BASE_URL . "index.php?action=login");
            exit;
        }

        $categories = $this->categorieModel->getAllCategories();
        require __DIR__ . "/../views/admin.php";
    }

}

?>
