<?php
class AnnonceModel {
     private $db;

     public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createAnnonce($titre, $desc, $prix, $livraison, $categorieId, $dispo, $auteurId, $acheteurId) {
        $req = $this->db->prepare("INSERT INTO Annonce (titre, description, prix, livraison, categorie, dispo, auteur, acheteur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $req->execute([$titre, $desc, $prix, implode(',', $livraison), $categorieId, $dispo, $auteurId, $acheteurId]);
    }

    public function updateAnnonce($id, $titre = null, $desc = null, $prix = null, $livraison = null, $categorieId = null, $dispo = null, $auteurId = null, $acheteurId = null) {
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
        if ($acheteurId !== null) {
            $tabreq[] = "acheteur = ?";
            $params[] = $acheteurId;
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

    public function getAnnoncesByAuteur($auteurId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE auteur=?");
        $req->execute([$auteurId]);
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function getAnnoncesByAcheteur($acheteurId) {
        $req = $this->db->prepare("SELECT * FROM Annonce WHERE acheteur=?");
        $req->execute([$acheteurId]);
        $annonces = $req->fetchAll(PDO::FETCH_ASSOC);
        foreach ($annonces as &$a) {
            $a['livraison'] = explode(',', $a['livraison']);
        }
        return $annonces;
    }

    public function VenteAnnonce($id, $acheteurId) {
        $annonce = $this->getAnnonceById($id);
        if (!$annonce) {
            throw new Exception("Erreur : cette annonce n'existe pas");
        }
        if (!$annonce["dispo"]) {
            throw new Exception("L'annonce ne peut pas être vendue");
        }
        $req = $this->db->prepare("UPDATE Annonce SET dispo=false, acheteur=? WHERE id=?");
        return $req->execute([$acheteurId, $id]);
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