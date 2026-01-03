<?php

class UserController {

    private $userModel;

    private $categorieModel;

    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
        $this->categorieModel = new Categorie($pdo);
    }

    public function showRegisterForm() {
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        try {
            $email = $_POST['email'] ?? null;
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            // Tous les utilisateurs créés via register sont "user"
            $this->userModel->createUser($email, $username, $password, "user");

            header("Location: index.php?action=login&success=1");
            exit;

        } catch (Exception $e) {
            $error = $e->getMessage();
            require __DIR__ . '/../views/register.php';
        }
    }

    public function showLoginForm() {
        require __DIR__ . '/../views/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        $user = $this->userModel->authentification($email, $password);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            header("Location: index.php?action=home");
            exit;
        } else {
            $error = "Identifiants incorrects";
            require __DIR__ . '/../views/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=home");
        exit;
    }

    public function admin($error = null, $success = null) {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location:". BASE_URL . "index.php?action=login");
            exit;
        }

        $categories = $this->categorieModel->getAllCategories();
        require __DIR__ . "/../views/admin.php";
    }

    public function addCategory() { 
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location: index.php?action=login");
            exit;
        }
        $name = $_POST['name'] ?? null;
        if ($name) {
            try {
                if (!$this->categorieModel->createCategorie($name)) {
                    $error = "Erreur lors de l'ajout";
                } else {
                    $success = "La catégorie a bien été ajoutée";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->admin($error ?? null, $success ?? null);
    }
    public function modifyCategory() {
        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] != "admin") {
            header("Location: index.php?action=login");
            exit;
        }
        $oldName = $_POST['category'] ?? null;
        $newName = $_POST['name'] ?? null;
        if ($oldName && $newName) {
            try {
                if (!$this->categorieModel->updateCategorie($this->categorieModel->getCategorieByNom($oldName)['id'], $newName)){
                    $error = "Erreur lors de la modification";
                } else {
                    $success = "La modification a été effectuée";
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        $this->admin($error ?? null, $success ?? null);
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

}
?>
