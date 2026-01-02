<?php
class VenteModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createVente($acheteurId, $vendeurId, $annonceId, $livraison) {
        $req = $this->db->prepare("INSERT INTO Vente (id_acheteur, id_vendeur, id_annonce, livraison, estEnvoye, estRecu) VALUES (?, ?, ?, ?, false, false)");
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
        if ($vente["estEnvoye"]) {
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
        $req = $this->db->prepare("UPDATE Vente SET estEnvoye=? WHERE id=?");
        return $req->execute([true, $id]);
    }

    public function RecevoirColis($id) {
        $vente = $this->getVenteById($id);
        if (!$vente) {
            throw new Exception("Erreur : cette vente n'existe pas");
        }
        if (!$vente["estEnvoye"]) {
            throw new Exception("Erreur : le colis n'a pas encore été envoyé");
        }
        $req = $this->db->prepare("UPDATE Vente SET estRecu=? WHERE id=?");
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
        $req = $this->db->prepare("SELECT * FROM Vente WHERE id_acheteur=?");
        $req->execute([$acheteurId]);
        $res = $req->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getAllVentesByVendeur($vendeurId) {
        $req = $this->db->prepare("SELECT * FROM Vente WHERE id_vendeur=?");
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