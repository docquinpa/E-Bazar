<?php
class AnnonceModel {
     private $db;

     public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createAnnonce($titre, $desc, $prix, $livraison, $categorieId, $dispo, $auteurId) {
        $req = $this->db->prepare("INSERT INTO Annonce (titre, description, prix, livraison, categorie, dispo, auteur) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $req->execute([$titre, $desc, $prix, implode(',', $livraison), $categorieId, $dispo, $auteurId]);
    }

    public function updateAnnonce($id, $titre = null, $desc = null, $prix = null, $livraison = null, $categorieId = null, $dispo = null, $auteurId = null) {
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

    public function deleteAnnonce($id) {
        $req = $this->db->prepare("SELECT id FROM Annonce WHERE id=?");
        $req->execute([$id]);
        $annonce = $req->fetch(PDO::FETCH_ASSOC);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }
        $req = $this->db->prepare("DELETE FROM Annonce WHERE id=?");
        return $req->execute([$id]);
    }

    public function getAnnonceById($id) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE id=?");
        $req->execute([$id]);
        $annonce = $req->fetch(PDO::FETCH_ASSOC);
        if ($annonce) {
            $annonce['livraison'] = explode(',', $annonce['livraison']);
        } 
        return $annonce;
    }

    public function getAllAnnonces() {
        $req = $this->db->query("SELECT * FROM Annonce");
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function getAnnoncesByCategorie($categorieId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE categorie=?");
        $req->execute([$categorieId]);
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function getAnnoncesByTitle($titre) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE titre LIKE :titre");
        $req->bindValue(':titre', "%$titre%", PDO::PARAM_STR);
        $req->execute();
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function getAnnonceCountByCategorie($categorieId) {
        $req = $this->db->prepare("SELECT COUNT(id) FROM Annonce WHERE categorie=?");
        $req->execute([$categorieId]);
        return $req->fetchColumn();
    }

    public function getPaginationByCategorie($categorieId, $limit, $page) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE categorie=? ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $req->bindValue(1, $categorieId, PDO::PARAM_INT);
        $req->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $req->bindValue(':offset', (int) ($limit *($page - 1)), PDO::PARAM_INT);
        $req->execute();
        $pagination = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($pagination as &$a) {
           $a['livraison'] = explode(',', $a['livraison']);
        }
        return $pagination;
    } 

    public function getAnnoncesByAuteur($auteurId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE auteur=?");
        $req->execute([$auteurId]);
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function masquerAnnonce($id){
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }
        $req = $this->db->prepare("UPDATE Annonce SET dispo=false WHERE id=?");
        return $req->execute([$id]);
    }

    public function demasquerAnnonce($id){
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }
        $req = $this->db->prepare("UPDATE Annonce SET dispo=true WHERE id=?");
        return $req->execute([$id]);
    }

    private function isCorrect($pdo) {
        if (is_null($pdo)) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }
}
?>
