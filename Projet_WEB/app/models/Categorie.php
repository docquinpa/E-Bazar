<?php

class Categorie {

    private $db;

    /* ============================
       CONSTRUCTEUR
       Vérifie que le PDO est valide
    ============================ */
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }


    /* ============================
       CRÉER UNE CATÉGORIE
       - nom obligatoire
       - nom unique
    ============================ */
    public function createCategorie($nom) {

        if (empty(trim($nom))) {
            throw new Exception("Erreur : le nom ne doit pas être vide");
        }

        if ($this->getCategorieByNom($nom) !== false) {
            throw new Exception("Erreur : une catégorie de ce nom existe déjà");
        }

        $sql = "INSERT INTO Categorie (nom) VALUES (?)";
        $req = $this->db->prepare($sql);


        return $req->execute([$nom]);

    }


    /* ============================
       METTRE À JOUR UNE CATÉGORIE
       - nom obligatoire
       - nom unique
       - catégorie doit exister
    ============================ */
    public function updateCategorie($id, $nom = null) {

        if ($nom === null || empty(trim($nom))) {
            throw new Exception("Erreur : le nom ne doit pas être vide");
        }

        if ($this->getCategorieByNom($nom)) {
            throw new Exception("Erreur : une catégorie de ce nom existe déjà");
        }
        if (!$this->getCategorieById($id)) {
            throw new Exception("Erreur : cette catégorie n'existe pas");
        }

        $sql = "UPDATE Categorie SET nom=? WHERE id=?";
        $req = $this->db->prepare($sql);

        return $req->execute([$nom, $id]);
    }


    /* ============================
       RÉCUPÉRER TOUTES LES CATÉGORIES
    ============================ */
    public function getAllCategories() {
        $req = $this->db->query("SELECT * FROM Categorie");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER UNE CATÉGORIE PAR ID
    ============================ */
    public function getCategorieById($id) {
        $req = $this->db->prepare("SELECT * FROM Categorie WHERE id=?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER UNE CATÉGORIE PAR NOM
    ============================ */
    public function getCategorieByNom($nom) {
        $req = $this->db->prepare("SELECT * FROM Categorie WHERE nom=?");
        $req->execute([$nom]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER CATÉGORIES + NOMBRE D'ANNONCES
       (pour affichage sur la home)
    ============================ */
    public function getAllWithCount() {

        $sql = "
            SELECT c.id, c.nom, COUNT(a.id) AS count
            FROM Categorie c
            LEFT JOIN Annonce a
                ON a.categorie = c.id
                AND a.dispo = 1
            GROUP BY c.id, c.nom
            ORDER BY c.nom ASC
        ";


        $req = $this->db->prepare($sql);
        $req->execute();

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
