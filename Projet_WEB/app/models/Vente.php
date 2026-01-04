<?php

class VenteModel {

    private $db;

    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    /* ============================
       1) CRÉER UNE VENTE
    ============================ */
    public function createVente($acheteurId, $vendeurId, $annonceId, $livraison) {
        $sql = "INSERT INTO Vente (id_acheteur, id_vendeur, id_annonce, livraison, estEnvoye, estRecu)
                VALUES (?, ?, ?, ?, 0, 0)";
        $req = $this->db->prepare($sql);
        return $req->execute([$acheteurId, $vendeurId, $annonceId, $livraison]);
    }

    /* ============================
       2) RÉCUPÉRER UNE VENTE
    ============================ */
    public function getVenteByAnnonce($annonceId) {
        $req = $this->db->prepare("SELECT * FROM Vente WHERE id_annonce=?");
        $req->execute([$annonceId]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /* ============================
       3) ANNONCES VENDUES PAR LE VENDEUR
    ============================ */
    public function getAnnoncesVenduesByVendeur($vendeurId) {
        $sql = "
            SELECT Annonce.*, Vente.estEnvoye, Vente.estRecu
            FROM Vente
            JOIN Annonce ON Annonce.id = Vente.id_annonce
            WHERE Vente.id_vendeur = ? AND Vente.estEnvoye=0
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$vendeurId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       4) ANNONCES ACHETÉES PAR L'ACHETEUR
    ============================ */
    public function getAnnoncesAcheteesByAcheteur($acheteurId) {
        $sql = "
            SELECT Annonce.*, Vente.estEnvoye, Vente.estRecu
            FROM Vente
            JOIN Annonce ON Annonce.id = Vente.id_annonce
            WHERE Vente.id_acheteur = ?
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$acheteurId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       5) ANNONCES LIVRÉES (VENDEUR)
    ============================ */
    public function getAnnoncesLivreesByVendeur($vendeurId) {
        $sql = "
            SELECT Annonce.*, Vente.estEnvoye, Vente.estRecu
            FROM Vente
            JOIN Annonce ON Annonce.id = Vente.id_annonce
            WHERE Vente.id_vendeur = ? AND Vente.estEnvoye = 1
        ";
        $req = $this->db->prepare($sql);
        $req->execute([$vendeurId]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
       6) MARQUER COMME ENVOYÉ
    ============================ */
    public function markAsSent($annonceId) {
        $req = $this->db->prepare("UPDATE Vente SET estEnvoye=1 WHERE id_annonce=?");
        return $req->execute([$annonceId]);
    }

    /* ============================
       7) MARQUER COMME REÇU
    ============================ */
    public function markAsReceived($annonceId) {
        $req = $this->db->prepare("UPDATE Vente SET estRecu=1 WHERE id_annonce=?");
        return $req->execute([$annonceId]);
    }

    /* ============================
       8) SUPPRIMER UNE VENTE
    ============================ */
    public function deleteVente($annonceId) {
        $req = $this->db->prepare("DELETE FROM Vente WHERE id_annonce=?");
        return $req->execute([$annonceId]);
    }

    /* ============================
       9) VALIDATION PDO
    ============================ */
    private function isCorrect($pdo) {
        if (!$pdo) {
            throw new Exception("Erreur : PDO invalide");
        }
        return $pdo;
    }
}

?>
