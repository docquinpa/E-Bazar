<?php

class AnnonceController
{
    private $model;
    private $imageModel;
    private $categorieModel;
    private $venteModel;

    public function __construct($pdo) {
        $this->model = new AnnonceModel($pdo);
        $this->imageModel = new AnnonceImageModel($pdo);
        $this->categorieModel = new Categorie($pdo);
        $this->venteModel = new VenteModel($pdo);
    }


    public function viewAd() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Annonce introuvable");
        }

        $annonce = $this->model->getAnnonceById($id);

        if (!$annonce) {
            die("Annonce introuvable");
        }

        // Charger les images de l'annonce
        $images = $this->imageModel->getImagesByAnnonce($id);

        require BASE_PATH . "/app/views/viewAd.php";
    }



    public function listCategory() {
        $categoryId = $_GET['id'] ?? null;
        if (!$categoryId) die("Catégorie introuvable");

        // Pagination
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        $total = $this->model->getAnnonceCountByCategorie($categoryId);
        $totalPages = ceil($total / $limit);

        $annonces = $this->model->getPaginationByCategorie($categoryId, $limit, $page);

        // Charger les images pour chaque annonce
        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        // Nom de la catégorie
        $cat = $this->categorieModel->getCategorieById($categoryId);
        $categoryName = $cat ? $cat['nom'] : "Catégorie inconnue";


        require BASE_PATH . "/app/views/listCategory.php";
    }


    public function home() {
        // Dernières annonces
        $annonces = array_reverse($this->model->getAllAnnonces());
        $annonces = array_slice($annonces, 0, 4);

        // Charger les images pour chaque annonce
        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        // Catégories dynamiques
        $categories = $this->categorieModel->getAllCategories();

        require BASE_PATH . "/app/views/home.php";
    }



    public function addAd()
    {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("addAd");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titre = $_POST['titre'];
            $desc = $_POST['description'];
            $prix = $_POST['prix'];
            $livraison = $_POST['livraison']; // tableau
            $categorie = $_POST['categorie'];
            $auteur = $_SESSION['user']['id'];

            $this->model->createAnnonce(
                $titre,
                $desc,
                $prix,
                $livraison,
                $categorie,
                true,      // dispo = true par défaut
                $auteur
            );

            header("Location: " . BASE_URL . "index.php?action=myAds");
            exit;
        }

        require BASE_PATH . "/app/views/addAd.php";
    }

    public function deleteAd()
    {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("myAds");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) die("Annonce introuvable");

        $annonce = $this->model->getAnnonceById($id);

        if ($annonce['auteur'] != $_SESSION['user']['id']) {
            die("Accès interdit");
        }

        if ($annonce['dispo'] == false) {
            die("Impossible de supprimer une annonce vendue");
        }

        $this->model->deleteAnnonce($id);

        header("Location: " . BASE_URL . "index.php?action=myAds");
        exit;
    }

    public function buy()
    {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("buy&id=" . $_GET['id']);
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) die("Annonce introuvable");

        $annonce = $this->model->getAnnonceById($id);

        if (!$annonce['dispo']) {
            die("Annonce déjà vendue");
        }

        require BASE_PATH . "/app/views/buy.php";
    }

    public function confirmReception()
    {
        if (!isset($_SESSION['user'])) {
            die("Accès interdit");
        }

        $id = $_GET['id'] ?? null;
        if (!$id) die("Annonce introuvable");
    }

    public function myAds()
    {
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("myAds");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $annonces = $this->model->getAnnoncesByAuteur($_SESSION['user']['id']);

        require BASE_PATH . "/app/views/myAds.php";
    }

    public function profile() {
        if (!isset($_SESSION["user"])) {
            $redirect = urlencode("profile");
            header("Location:". BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        $annoncesEnVente =  $this->model->getAllAnnoncesEnVenteByAuteur($_SESSION["user"]["id"]);
        $annoncesVendues = $this->model->getAllAnnoncesVenduesByAuteur($_SESSION["user"]["id"]);
        $annoncesAchetees = $this->model->getAllAnnoncesAcheteesByAuteur($_SESSION["user"]["id"]);

        require BASE_PATH . "/app/views/profile.php";
    }

}

?>
