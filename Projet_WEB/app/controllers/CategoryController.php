<?php

class CategoryController {

    private $categorieModel;

    public function __construct($pdo) {
        $this->categorieModel = new Categorie($pdo);
    }

    /* ============================
       AJOUTER UNE CATÉGORIE
    ============================ */
    public function addCategory() {

        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
            header("Location: index.php?action=login");
            exit;
        }

        $name = $_POST['name'] ?? null;

        if ($name) {
            try {
                $this->categorieModel->createCategorie($name);
                $success = "Catégorie ajoutée avec succès";
                $categories = $this->categorieModel->getAllCategories();
                require __DIR__ . '/../views/admin.php';
                return;

            } catch (Exception $e) {
                $error = $e->getMessage();
                $categories = $this->categorieModel->getAllCategories();
                require __DIR__ . '/../views/admin.php';
                return;
            }
        }

        header("Location: index.php?action=admin");
        exit;
    }


    /* ============================
       MODIFIER UNE CATÉGORIE
    ============================ */
    public function updateCategory() {

        if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
            header("Location: index.php?action=login");
            exit;
        }

        $cat = $_POST['category'] ?? null;
        $name = $_POST['name'] ?? null;
        $id = $this->categorieModel->getCategorieByNom($cat);
        $id = $id['id'];

        if ($id && $name) {
            try {
                $this->categorieModel->updateCategorie($id, $name);
                $success = "Catégorie modifiée avec succès";
                $categories = $this->categorieModel->getAllCategories();
                require __DIR__ . '/../views/admin.php';
                return;
            } catch (Exception $e) {
                $error = $e->getMessage();
                $categories = $this->categorieModel->getAllCategories();
                require __DIR__ . '/../views/admin.php';
                return;
            }
        }

        header("Location: index.php?action=admin");
        exit;
    }

}

?>
