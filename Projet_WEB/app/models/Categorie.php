<?php
class Categorie {
    private $db;
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createCategorie($nom) {
        if (empty(trim($nom))) {
            throw new Exception("Erreur : le nom ne doit pas être vide");
        }
        if ($this->getCategorieByNom($nom) != null) {
            throw new Exception("Erreur : une catégorie de ce nom existe déjà");
        }
        $req = $this->db->prepare("INSERT INTO Categorie (nom) VALUES (?)");
        return $req->execute([$nom]);
    }
    public function updateCategorie($id, $nom = null) {
        if ($nom == null || empty(trim($nom))) {
            throw new Exception("Erreur : le nom ne doit pas être vide");
        }
        if ($this->getCategorieByNom($nom)) {
            throw new Exception("Erreur : une catégorie de ce nom existe déjà");
        }
        if (!$this->getCategorieById($id)) {
            throw new Exception("Erreur : cette catégorie n'existe pas");
        }
        $req = $this->db->prepare("UPDATE Categorie SET nom=? WHERE id=?");
        return $req->execute([$nom, $id]);
    }

    public function getAllCategories() {
        $req = $this->db->query("SELECT * FROM Categorie");
        $categories = $req->fetchAll(PDO::FETCH_ASSOC);
        return $categories;
    }

    public function getCategorieById($id) {
        $req = $this->db->prepare("SELECT * from Categorie WHERE id=?");
        $req->execute([$id]);
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getCategorieByNom($nom) {
        $req = $this->db->prepare("SELECT * from Categorie WHERE nom=?");
        $req->execute([$nom]);
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    private function isCorrect($pdo) {
        if (is_null($pdo)) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }

}
?>