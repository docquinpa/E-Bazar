<?php

class VenteController {

    private $venteModel;
    private $annonceModel;

    public function __construct($pdo) {
        $this->venteModel   = new VenteModel($pdo);
        $this->annonceModel = new AnnonceModel($pdo);
    }

    /* ============================
       ACHETER UNE ANNONCE
    ============================ */
    public function buy() {

        if (!isset($_SESSION['user'])) {
            $redirect = urlencode("buy&id=" . ($_GET['id'] ?? ''));
            header("Location: index.php?action=login&redirect=$redirect");
            exit;
        }

        $annonceId = $_GET['id'] ?? null;
        if (!$annonceId) die("Annonce introuvable");

        $annonce = $this->annonceModel->getAnnonceById($annonceId);
        if (!$annonce || !$annonce['dispo']) {
            die("Annonce déjà vendue ou introuvable");
        }

        require __DIR__ . '/../views/buy.php';
    }

    /* ============================
       CONFIRMER L'ACHAT
       (création de la vente)
    ============================ */
    public function confirmBuy() {

        if (!isset($_SESSION['user'])) {
            die("Accès interdit");
        }

        $annonceId = $_POST['annonce_id'] ?? null;
        $livraison = $_POST['livraison'] ?? null;

        if (!$annonceId || !$livraison) {
            die("Paramètres manquants");
        }

        $annonce = $this->annonceModel->getAnnonceById($annonceId);
        if (!$annonce || !$annonce['dispo']) {
            die("Annonce introuvable ou déjà vendue");
        }

        $acheteurId = $_SESSION['user']['id'];
        $vendeurId  = $annonce['auteur'];

        // Créer la vente
        $this->venteModel->createVente($acheteurId, $vendeurId, $annonceId, $livraison);

        // Rendre l’annonce indisponible
        $this->annonceModel->masquerAnnonce($annonceId);

        header("Location: index.php?action=profile");
        exit;
    }

    /* ============================
       MARQUER COMME ENVOYÉ (VENDEUR)
    ============================ */
    public function markSent() {

        if (!isset($_SESSION['user'])) {
            die("Accès interdit");
        }

        $annonceId = $_GET['id'] ?? null;
        if (!$annonceId) die("Annonce introuvable");

        $annonce = $this->annonceModel->getAnnonceById($annonceId);
        if (!$annonce) die("Annonce introuvable");

        if ($annonce['auteur'] != $_SESSION['user']['id']) {
            die("Accès interdit");
        }

        $this->venteModel->markAsSent($annonceId);

        header("Location: index.php?action=profile");
        exit;
    }

    /* ============================
       MARQUER COMME REÇU (ACHETEUR)
    ============================ */
    public function markReceived() {

        if (!isset($_SESSION['user'])) {
            die("Accès interdit");
        }

        $annonceId = $_GET['id'] ?? null;
        if (!$annonceId) die("Annonce introuvable");

        $vente = $this->venteModel->getVenteByAnnonce($annonceId);
        if (!$vente) die("Vente introuvable");

        if ($vente['id_acheteur'] != $_SESSION['user']['id']) {
            die("Accès interdit");
        }

        $this->venteModel->markAsReceived($annonceId);

        // Une fois reçu → supprimer la vente
        $this->venteModel->deleteVente($annonceId);

        header("Location: index.php?action=profile");
        exit;
    }
}

?>
