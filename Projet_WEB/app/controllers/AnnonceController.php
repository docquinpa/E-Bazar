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

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titre = $_POST['titre'];
            $desc = $_POST['description'];
            $prix = $_POST['prix'];
            $livraison = isset($_POST['livraison']) ? implode(',', $_POST['livraison']) : '';
            $categorie = $_POST['categorie'];
            $auteur = $_SESSION['user']['id'];

            try {

                // 1) Création de l'annonce
                $annonceId = $this->model->createAnnonce(
                    $titre,
                    $desc,
                    $prix,
                    $livraison,
                    $categorie,
                    true,
                    $auteur
                );

                // 2) Upload des images
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

                header("Location: " . BASE_URL . "index.php?action=myAds");
                exit;

            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }


        $livraisonOptions = $this->model->getLivraisonOptions();
        $categories = $this->categorieModel->getAllCategories();

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

    $userId = $_SESSION["user"]["id"];

    // Récupération des annonces
    $annoncesEnVente =  $this->model->getAllAnnoncesEnVenteByAuteur($userId);
    $annoncesVendues = $this->model->getAllAnnoncesVenduesByAuteur($userId);
    $annoncesAchetees = $this->model->getAllAnnoncesAcheteesByAuteur($userId);

    $imagesByAd = [];

    foreach ($annoncesEnVente as $ad) {
        $imagesByAd[$ad['id']] = $this->imageModel->getImagesByAnnonce($ad['id']);
    }

    foreach ($annoncesAchetees as $ad) {
        $imagesByAd[$ad['id']] = $this->imageModel->getImagesByAnnonce($ad['id']);
    }

    foreach ($annoncesVendues as $ad) {
        $imagesByAd[$ad['id']] = $this->imageModel->getImagesByAnnonce($ad['id']);
    }

    require BASE_PATH . "/app/views/profile.php";
}


}

?>
