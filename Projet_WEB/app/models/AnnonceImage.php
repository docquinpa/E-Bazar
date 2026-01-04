<?php

class AnnonceImageModel {

    private $db;

    /* ============================
       CONSTRUCTEUR
       Vérifie que le PDO est valide
    ============================ */
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }


    /* ============================
       AJOUTER UNE IMAGE À UNE ANNONCE
       - max 5 images
       - jpeg uniquement
       - max 200 ko
       - nom unique
    ============================ */
    public function createImage($file, $annonceId) {

        /* --- Vérifier limite d’images --- */
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM AnnonceImage WHERE annonce_id = ?");
        $stmt->execute([$annonceId]);
        $count = $stmt->fetchColumn();

        if ($count >= 5) {
            throw new Exception("Vous ne pouvez pas ajouter plus de 5 images à une annonce.");
        }

        /* --- Vérifier fichier valide --- */
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Aucun fichier valide reçu.");
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors de l'upload.");
        }

        /* --- Vérifier type MIME --- */
        $allowed = ['image/jpeg'];
        if (!in_array($file['type'], $allowed)) {
            throw new Exception("Format d'image non autorisé (JPEG uniquement).");
        }

        /* --- Vérifier taille max --- */
        $maxSize = 200 * 1024; // 200 ko
        if ($file['size'] > $maxSize) {
            throw new Exception("L'image dépasse la taille maximale autorisée (200 ko).");
        }

        /* --- Préparer dossier upload --- */
        $uploadDir = BASE_PATH . "/public/upload/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        /* --- Générer nom unique --- */
        $filename = bin2hex(random_bytes(16)) . "_" . basename($file['name']);
        $filename = truncateFilename($filename);
        $destination = $uploadDir . $filename;

        /* --- Déplacer fichier --- */
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Erreur lors de l'upload de l'image.");
        }

        /* --- Enregistrer en BDD --- */
        $req = $this->db->prepare("INSERT INTO AnnonceImage (url, annonce_id) VALUES (?, ?)");
        return $req->execute([$filename, $annonceId]);
    }


    /* ============================
       SUPPRIMER UNE IMAGE
       - supprime fichier physique
       - supprime entrée BDD
    ============================ */
    public function deleteImage($id) {

        $image = $this->getImageById($id);
        if (!$image) {
            throw new Exception("Cette image n'existe pas.");
        }

        $url = $image["url"];
        $filePath = BASE_PATH . "/public/upload/" . $url;

        /* --- Supprimer fichier physique --- */
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        /* --- Supprimer entrée BDD --- */
        $req = $this->db->prepare("DELETE FROM AnnonceImage WHERE id=?");
        return $req->execute([$id]);
    }


    /* ============================
       RÉCUPÉRER UNE IMAGE PAR ID
    ============================ */
    public function getImageById($id) {
        $req = $this->db->prepare("SELECT * FROM AnnonceImage WHERE id=?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER TOUTES LES IMAGES D’UNE ANNONCE
    ============================ */
    public function getImagesByAnnonce($annonceId) {
        $req = $this->db->prepare("SELECT * FROM AnnonceImage WHERE annonce_id=?");
        $req->execute([$annonceId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ============================
       VALIDATION PDO
    ============================ */
    private function isCorrect($pdo) {
        if (!$pdo) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }
}

?>
