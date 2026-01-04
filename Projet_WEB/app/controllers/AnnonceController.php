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

        // --- Vérifier connexion utilisateur ---
        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("addAd");
            header("Location: " . BASE_URL . "index.php?action=login&redirect=$redirect");
            exit;
        }

        if (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === "admin") {
            $_SESSION['error'] = "Vous ne pouvez pas créer d'annonce avec le compte admin.";
            header("Location: " . BASE_URL . "index.php?action=home");
            exit;
        }


        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Récupération des champs
            $titre      = trim($_POST['titre'] ?? '');
            $desc       = trim($_POST['description'] ?? '');
            $prix       = $_POST['prix'] ?? null;
            $categorie  = $_POST['categorie'] ?? null;
            $livraison  = $_POST['livraison'] ?? [];
            $auteur     = $_SESSION['user']['id'];

            try {

                /* ----------------------------------------------------
                VALIDATIONS SERVEUR (obligatoires)
                ---------------------------------------------------- */

                // --- Titre ---
                if (strlen($titre) < 5 || strlen($titre) > 30) {
                    throw new Exception("Le titre doit contenir entre 5 et 30 caractères.");
                }

                // --- Description ---
                if (strlen($desc) < 5 || strlen($desc) > 200) {
                    throw new Exception("La description doit contenir entre 5 et 200 caractères.");
                }

                // --- Prix ---
                if (!is_numeric($prix) || $prix < 0) {
                    throw new Exception("Le prix doit être un nombre positif ou nul.");
                }

                // --- Catégorie valide ---
                if (!$this->categorieModel->getCategorieById($categorie)) {
                    throw new Exception("Catégorie invalide.");
                }

                // --- Livraison : au moins 1 mode ---
                if (empty($livraison)) {
                    throw new Exception("Veuillez sélectionner au moins un mode de livraison.");
                }

                // --- Livraison : valeurs autorisées ---
                $allowedLivraison = $this->model->getLivraisonOptions(); // ex: ["Mondial Relay", "Colissimo", ...]
                foreach ($livraison as $l) {
                    if (!in_array($l, $allowedLivraison)) {
                        throw new Exception("Mode de livraison invalide.");
                    }
                }

                // Convertir en chaîne SET
                $livraisonStr = implode(',', $livraison);


                /* ----------------------------------------------------
                INSERTION EN BDD
                ---------------------------------------------------- */

                $this->db->beginTransaction();

                // Créer l'annonce
                $annonceId = $this->model->createAnnonce(
                    $titre,
                    $desc,
                    $prix,
                    $livraisonStr,
                    $categorie,
                    true,       // dispo = 1
                    $auteur
                );

                // Upload images (optionnel)
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

                // Redirection
                header("Location: " . BASE_URL . "index.php?action=myAds");
                exit;

            } catch (Exception $e) {

                // Annuler transaction
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

                // Message d’erreur affiché dans la vue
                $error = $e->getMessage();
            }
        }

        // Données pour le formulaire
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

    public function listSearch() {

        // Récupération du terme recherché
        $titre = $_GET['titre'] ?? '';
        if ($titre === '') {
            header("Location: index.php?action=listAll");
            exit;
        }

        // Pagination
        $page = $_GET['page'] ?? 1;
        $limit = 10;

        // Nombre total de résultats
        $total = $this->model->getAnnonceCountByTitle($titre);
        $totalPages = ceil($total / $limit);

        // Résultats paginés
        $annonces = $this->model->getAdsPaginatedByTitle($titre, $limit, $page);

        // Images associées
        $imagesByAd = [];
        foreach ($annonces as $a) {
            $imagesByAd[$a['id']] = $this->imageModel->getImagesByAnnonce($a['id']);
        }

        // Vue
        require BASE_PATH . "/app/views/listSearch.php";
    }

    public function masquer() {

        if (!isset($_SESSION['user'])) {
            die("Non autorisé");
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID manquant");
        }

        $annonce = $this->model->getAnnonceById($id);

        if (!$annonce) {
            die("Annonce introuvable");
        }

        // Vérification propriétaire
        if ($annonce['auteur'] != $_SESSION['user']['id']) {
            die("Action interdite");
        }

        $this->model->masquerAnnonce($id);

        header("Location: index.php?action=profile");
        exit;
    }

    public function demasquer() {

        if (!isset($_SESSION['user'])) {
            die("Non autorisé");
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID manquant");
        }

        $annonce = $this->model->getAnnonceById($id);

        if (!$annonce) {
            die("Annonce introuvable");
        }

        if ($annonce['auteur'] != $_SESSION['user']['id']) {
            die("Action interdite");
        }

        $this->model->demasquerAnnonce($id);

        header("Location: index.php?action=profile");
        exit;
    }



}

?>
