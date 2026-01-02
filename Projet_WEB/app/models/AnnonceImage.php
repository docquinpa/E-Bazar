<?php
class AnnonceImageModel {
    private $db;
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createImage($file, $annonceId) {
        // Vérifier que le fichier est valide
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            throw new Exception("Aucun fichier valide reçu.");
        }
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors de l'upload.");
        }
        // Vérifier le type MIME
        $allowed = ['image/jpeg'];
        if (!in_array($file['type'], $allowed)) {
            throw new Exception("Format non autorisé.");
        }

        // Taille max : 200 ko
        $maxSize = 200 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception("L'image dépasse la taille maximale autorisée (200 ko).");
        }


        // Récupération du dossier
        $uploadDir = __DIR__ . "/../../public/upload/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        //on donne un nom unique à l'image
        $filename = bin2hex(random_bytes(16)) . "_" . basename($file['name']);
        $filename = truncateFilename($filename);
        $destination = $uploadDir . $filename;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Erreur lors de l'upload de l'image");
        }

        // Insertion en BDD
        $req = $this->db->prepare("INSERT INTO AnnonceImage (url, annonce_id) VALUES (?, ?)");
        return $req->execute([$filename, $annonceId]);
    }

    public function deleteImage($id) {
        $image = $this->getImageById($id);
        if (!$image) {
            throw new Exception("Cette image n'existe pas");
        }
        $url = $image["url"];
        $filePath = __DIR__ . "/../../public/upload/" . $url;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $req = $this->db->prepare("DELETE FROM AnnonceImage WHERE id=?");
        return $req->execute([$id]);
    }

    public function getImageById($id) {
        $req = $this->db->prepare("SELECT * FROM AnnonceImage WHERE id=?");
        $req->execute([$id]);
        $image = $req->fetch(PDO::FETCH_ASSOC);
        return $image;
    }

    public function getImagesByAnnonce($annonceId) {
        $req = $this->db->prepare("SELECT * FROM AnnonceImage WHERE annonce_id=?");
        $req->execute([$annonceId]);
        $images = $req->fetchAll(PDO::FETCH_ASSOC);
        return $images;
    }
    private function isCorrect($pdo) {
        if (is_null($pdo)) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }
}
?>
