<?php
class VenteModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createVente($acheteurId, $vendeurId, $annonceId, $livraison) {
        $req = $this->db->prepare("INSERT INTO Vente (acheteurId, vendeurId, annonceId, livraison, isEnvoye, isRecu) VALUES (?, ?, ?, ?, false, false)");
        return $req->execute([$acheteurId, $vendeurId, $annonceId, $livraison]);
    }

    public function deleteVente($id) {
        $vente = $this->getVenteById($id);
        if (!$vente) {
            throw new Exception("Erreur : cette vente n'existe pas");
        }
        $req = $this->db->prepare("DELETE FROM Vente WHERE id=?");
        return $req->execute([$id]);
    }

    public function updateVente($id, $livraison) {
        $vente = $this->getVenteById($id);
        if (!$vente) {
            throw new Exception("Erreur : cette vente n'existe pas");
        }
        if ($vente["isEnvoye"]) {
            throw new Exception("Erreur : le colis a déjà été envoyé");
        }
        $req = $this->db->prepare("UPDATE Vente SET livraison=? WHERE id=?");
        return $req->execute([$livraison, $id]);
    }

    public function envoyerColis($id) {
        $vente = $this->getVenteById($id);
        if (!$vente) {
            throw new Exception("Erreur : cette vente n'existe pas");
        }
        $req = $this->db->prepare("UPDATE Vente SET isEnvoye=? WHERE id=?");
        return $req->execute([true, $id]);
    }

    public function RecevoirColis($id) {
        $vente = $this->getVenteById($id);
        if (!$vente) {
            throw new Exception("Erreur : cette vente n'existe pas");
        }
        if (!$vente["isEnvoye"]) {
            throw new Exception("Erreur : le colis n'a pas encore été envoyé");
        }
        $req = $this->db->prepare("UPDATE Vente SET isRecu=? WHERE id=?");
        return $req->execute([true, $id]);
    }

    public function getVenteById($id) {
        $req = $this->db->prepare("SELECT * FROM Vente WHERE id=?");
        $req->execute([$id]);
        $user = $req->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getAllVentes() {
        $req = $this->db->query("SELECT * FROM Vente");
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getAllVentesByAcheteur($acheteurId) {
        $req = $this->db->prepare("SELECT * FROM Vente WHERE acheteurId=?");
        $req->execute([$acheteurId]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getAllVentesByVendeur($vendeurId) {
        $req = $this->db->prepare("SELECT * FROM Vente WHERE vendeurId=?");
        $req->execute([$vendeurId]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
    private function isCorrect($pdo) {
        if (is_null($pdo)) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }
}
?>