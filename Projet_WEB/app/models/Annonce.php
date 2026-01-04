<?php

class AnnonceModel {

    private $db;

    /* ============================
       CONSTRUCTEUR
       Vérifie que le PDO est valide
    ============================ */
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }


    /* ============================
       CRÉATION D'UNE ANNONCE
    ============================ */
    public function createAnnonce($titre, $desc, $prix, $livraison, $categorieId, $dispo, $auteurId) {
        $sql = "INSERT INTO Annonce (titre, description, prix, livraison, categorie, dispo, auteur)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $req = $this->db->prepare($sql);
        $req->execute([$titre, $desc, $prix, $livraison, $categorieId, $dispo, $auteurId]);

        return $this->db->lastInsertId();
    }


    /* ============================
       MISE À JOUR D'UNE ANNONCE
       Mise à jour partielle (seuls les champs fournis changent)
    ============================ */
    public function updateAnnonce($id, $titre = null, $desc = null, $prix = null, $livraison = null, $categorieId = null, $dispo = null, $auteurId = null) {

        // Vérifier que l'annonce existe
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }

        $tabreq = [];
        $params = [];

        if ($titre !== null) {
            $tabreq[] = "titre = ?";
            $params[] = $titre;
        }

        if ($desc !== null) {
            $tabreq[] = "description = ?";
            $params[] = $desc;
        }

        if ($prix !== null) {
            $tabreq[] = "prix = ?";
            $params[] = $prix;
        }

        if ($livraison !== null) {
            if (!is_array($livraison)) {
                throw new Exception("Le champ livraison doit être un tableau");
            }
            $tabreq[] = "livraison = ?";
            $params[] = implode(',', $livraison);
        }

        if ($categorieId !== null) {
            $tabreq[] = "categorie = ?";
            $params[] = $categorieId;
        }

        if ($dispo !== null) {
            $tabreq[] = "dispo = ?";
            $params[] = $dispo;
        }

        if ($auteurId !== null) {
            $tabreq[] = "auteur = ?";
            $params[] = $auteurId;
        }

        if (empty($tabreq)) {
            throw new Exception("Aucune donnée à mettre à jour");
        }

        $sql = "UPDATE Annonce SET " . implode(", ", $tabreq) . " WHERE id = ?";
        $params[] = $id;

        $req = $this->db->prepare($sql);
        return $req->execute($params);
    }


    /* ============================
       SUPPRESSION D'UNE ANNONCE
    ============================ */
    public function deleteAnnonce($id) {

        // Vérifier existence
        $req = $this->db->prepare("SELECT id FROM Annonce WHERE id=?");
        $req->execute([$id]);
        if (!$req->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }

        // Supprimer
        $req = $this->db->prepare("DELETE FROM Annonce WHERE id=?");
        return $req->execute([$id]);
    }


    /* ============================
       RÉCUPÉRER UNE ANNONCE PAR ID
    ============================ */
    public function getAnnonceById($id) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE id=?");
        $req->execute([$id]);

        $annonce = $req->fetch(PDO::FETCH_ASSOC);

        if ($annonce) {
            $annonce['livraison'] = explode(',', $annonce['livraison']);
        }

        return $annonce;
    }


    /* ============================
       RÉCUPÉRER TOUTES LES ANNONCES
    ============================ */
    public function getAllAnnonces() {
        $req = $this->db->query("SELECT * FROM Annonce WHERE dispo=1");
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $annonces;
    }


    /* ============================
       ANNONCES PAR CATÉGORIE
    ============================ */
    public function getAnnoncesByCategorie($categorieId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE categorie=? AND dispo=1");
        $req->execute([$categorieId]);

        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $annonces;
    }


    /* ============================
       RECHERCHE PAR TITRE
    ============================ */
    public function getAnnoncesByTitle($titre) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE titre LIKE :titre AND dispo=1");
        $req->bindValue(':titre', "%$titre%", PDO::PARAM_STR);
        $req->execute();

        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $annonces;
    }


    /* ============================
       COMPTER LES ANNONCES D'UNE CATÉGORIE
    ============================ */
    public function getAnnonceCountByCategorie($categorieId) {
        $req = $this->db->prepare("SELECT COUNT(id) FROM Annonce WHERE categorie=? AND dispo=1");
        $req->execute([$categorieId]);
        return $req->fetchColumn();
    }


    /* ============================
       PAGINATION PAR CATÉGORIE
    ============================ */
    public function getPaginationByCategorie($categorieId, $limit, $page) {

        $sql = "
            SELECT * FROM Annonce
            WHERE categorie = :cat AND dispo=1
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ";

        $req = $this->db->prepare($sql);
        $req->bindValue(':cat', $categorieId, PDO::PARAM_INT);
        $req->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $req->bindValue(':offset', (int)($limit * ($page - 1)), PDO::PARAM_INT);

        $req->execute();
        $pagination = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pagination as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $pagination;
    }


    /* ============================
       ANNONCES PAR AUTEUR
    ============================ */
    public function getAnnoncesByAuteur($auteurId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE auteur=?");
        $req->execute([$auteurId]);

        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $annonces;
    }


    /* ============================
       ANNONCES NON VENDUES (pas dans Vente)
    ============================ */
    public function getAllAnnoncesEnVenteByAuteur($auteurId) {
        $sql = "SELECT * FROM Annonce
                WHERE auteur=?
                AND id NOT IN (SELECT id_annonce FROM Vente)";

        $req = $this->db->prepare($sql);
        $req->execute([$auteurId]);

        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);

        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }

        return $annonces;
    }


    /* ============================
       MASQUER / DÉMASQUER UNE ANNONCE
    ============================ */
    public function masquerAnnonce($id) {
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }

        $req = $this->db->prepare("UPDATE Annonce SET dispo=0 WHERE id=?");
        return $req->execute([$id]);
    }

    public function demasquerAnnonce($id) {
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }

        $req = $this->db->prepare("UPDATE Annonce SET dispo=1 WHERE id=?");
        return $req->execute([$id]);
    }


    /* ============================
       OPTIONS DE LIVRAISON (SET SQL)
    ============================ */
    public function getLivraisonOptions() {
        $sql = "SHOW COLUMNS FROM Annonce LIKE 'livraison'";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return [];

        if (preg_match("/^set\((.*)\)$/i", $row['Type'], $matches)) {
            return str_getcsv($matches[1], ',', "'", "\\");
        }

        return [];
    }


    /* ============================
       PAGINATION GLOBALE
    ============================ */
    public function getAdsPaginated($limit, $page) {
        $sql = "SELECT * FROM Annonce WHERE dispo=1 ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)($limit * ($page - 1)), PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ============================
       COMPTER TOUTES LES ANNONCES
    ============================ */
    public function getAnnonceCount() {
        return $this->db->query("SELECT COUNT(*) FROM Annonce WHERE dispo=1")->fetchColumn();
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
