<?php

class AnnonceController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new AnnonceModel($pdo);
    }

    public function viewAd()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Annonce introuvable");
        }

        $annonce = $this->model->getAnnonceById($id);

        if (!$annonce) {
            die("Annonce introuvable");
        }

        require BASE_PATH . "/app/views/viewAd.php";
    }

    public function listCategory()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            die("Catégorie introuvable");
        }

        $annonces = $this->model->getAnnoncesByCategorie($id);

        require BASE_PATH . "/app/views/listCategory.php";
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

        // Ici tu feras la logique pour marquer comme reçu
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

}

?>
