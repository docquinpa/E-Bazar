<?php

class AnnonceController
{
    private $model;
    private $imageModel;
    private $categorieModel;
    private $db;

    public function __construct($pdo) {
        $this->model = new AnnonceModel($pdo);
        $this->imageModel = new AnnonceImageModel($pdo);
        $this->categorieModel = new Categorie($pdo);
        $this->db = $pdo;
    }

    /* ============================
       AFFICHER UNE ANNONCE
    ============================ */
    public function viewAd() {
        $id = $_GET['id'] ?? null;
        if (!$id) die("Annonce introuvable");

        $annonce = $this->model->getAnnonceById($id);
        if (!$annonce) die("Annonce introuvable");

        $images = $this->imageModel->getImagesByAnnonce($id);

        require BASE_PATH . "/app/views/viewAd.php";
    }

    /* ============================
       LISTE PAR CATÉGORIE
    ============================ */
    public function listCategory() {
        $categoryId = $_GET['id'] ?? null;
        if (!$categoryId) die("Catégorie introuvable");

        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $total = $this->model->getAnnonceCountByCategorie($categoryId);
        $totalPages = ceil($total / $limit);

        $annonces = $this->model->getPaginationByCategorie($categoryId, $limit, $page);

        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        $cat = $this->categorieModel->getCategorieById($categoryId);
        $categoryName = $cat ? $cat['nom'] : "Catégorie inconnue";

        require BASE_PATH . "/app/views/listCategory.php";
    }

    /* ============================
       PAGE D'ACCUEIL
    ============================ */
    public function home() {
        $annonces = array_reverse($this->model->getAllAnnonces());
        $annonces = array_slice($annonces, 0, 4);

        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        $categories = $this->categorieModel->getAllWithCount();

        require BASE_PATH . "/app/views/home.php";
    }

    /* ============================
       AJOUTER UNE ANNONCE
    ============================ */
    public function addAd() {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("addAd");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titre = $_POST['titre'];
            $desc = $_POST['description'];
            $prix = $_POST['prix'];
            $livraison = isset($_POST['livraison']) ? implode(',', $_POST['livraison']) : '';
            $categorie = $_POST['categorie'];
            $auteur = $_SESSION['user']['id'];

            $this->db->beginTransaction();

            try {
                $annonceId = $this->model->createAnnonce(
                    $titre, $desc, $prix, $livraison, $categorie, true, $auteur
                );

                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['tmp_name'] as $index => $tmp) {
                        $file = [
                            'name'     => $_FILES['images']['name'][$index],
                            'type'     => $_FILES['images']['type'][$index],
                            'tmp_name' => $_FILES['images']['tmp_name'][$index],
                            'error'    => $_FILES['images']['error'][$index],
                            'size'     => $_FILES['images']['size'][$index],
                        ];
                        $this->imageModel->createImage($file, $annonceId);
                    }
                }

                $this->db->commit();
                header("Location: " . BASE_URL . "index.php?action=myAds");
                exit;

            } catch (Exception $e) {
                $this->db->rollBack();
                $error = $e->getMessage();
            }
        }

        $livraisonOptions = $this->model->getLivraisonOptions();
        $categories = $this->categorieModel->getAllCategories();

        require BASE_PATH . "/app/views/addAd.php";
    }

    /* ============================
       SUPPRIMER UNE ANNONCE
    ============================ */
    public function deleteAd() {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("myAds");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) die("Annonce introuvable");

        $annonce = $this->model->getAnnonceById($id);

        if ($annonce['auteur'] != $_SESSION['user']['id'] && $_SESSION["user"]["role"] != "admin") {
            die("Accès interdit");
        }

        if ($annonce['dispo'] == false) {
            die("Impossible de supprimer une annonce vendue");
        }

        $this->model->deleteAnnonce($id);

        header("Location: " . BASE_URL . "index.php?action=myAds");
        exit;
    }

    /* ============================
       MES ANNONCES
    ============================ */
    public function myAds() {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("myAds");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $annonces = $this->model->getAnnoncesByAuteur($_SESSION['user']['id']);

        require BASE_PATH . "/app/views/myAds.php";
    }

    /* ============================
       LISTE TOUTES LES ANNONCES
    ============================ */
    public function listAll() {
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $total = $this->model->getAnnonceCount();
        $totalPages = ceil($total / $limit);

        $annonces = $this->model->getAdsPaginated($limit, $page);

        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        require BASE_PATH . "/app/views/listAll.php";
    }
}

?>
