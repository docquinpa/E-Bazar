<?php

class UserModel {

    private $db;

    /* ============================
       CONSTRUCTEUR
       Vérifie que le PDO est valide
    ============================ */
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }


    /* ============================
       CRÉER UN UTILISATEUR
       - email valide
       - mot de passe sécurisé
       - rôle fourni
    ============================ */
    public function createUser($email, $username, $password, $role) {

        /* --- Vérification email --- */
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Format d'email invalide");
        }

        /* --- Vérification mot de passe --- */
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
            throw new InvalidArgumentException(
                "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre"
            );
        }

        /* --- Hash du mot de passe --- */
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);

        /* --- Insertion --- */
        $sql = "INSERT INTO Utilisateur (email, username, password, role)
                VALUES (?, ?, ?, ?)";

        $req = $this->db->prepare($sql);
        return $req->execute([$email, $username, $hashedPass, $role]);
    }


    /* ============================
       METTRE À JOUR UN UTILISATEUR
       Mise à jour partielle
    ============================ */
    public function updateUser($id, $email = null, $username = null, $password = null, $role = null) {

        /* --- Vérifier existence --- */
        $user = $this->getUserById($id);
        if (!$user) {
            throw new Exception("Utilisateur introuvable");
        }

        $tabreq = [];
        $params = [];

        /* --- Email --- */
        if ($email !== null) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Format d'email invalide");
            }
            $tabreq[] = "email = ?";
            $params[] = $email;
        }

        /* --- Username --- */
        if ($username !== null) {
            $tabreq[] = "username = ?";
            $params[] = $username;
        }

        /* --- Mot de passe --- */
        if ($password !== null && trim($password) !== "") {

            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
                throw new InvalidArgumentException(
                    "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre"
                );
            }

            $tabreq[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        /* --- Rôle --- */
        if ($role !== null) {
            $tabreq[] = "role = ?";
            $params[] = $role;
        }

        if (empty($tabreq)) {
            throw new Exception("Aucune donnée à mettre à jour");
        }

        /* --- Exécuter update --- */
        $params[] = $id;
        $sql = "UPDATE Utilisateur SET " . implode(", ", $tabreq) . " WHERE id = ?";

        $req = $this->db->prepare($sql);
        return $req->execute($params);
    }


    /* ============================
       SUPPRIMER UN UTILISATEUR
       - impossible de supprimer un admin
    ============================ */
    public function deleteUser($id) {

        $user = $this->getUserById($id);
        if (!$user) {
            throw new Exception("Utilisateur introuvable");
        }

        if ($user["role"] === "admin") {
            throw new Exception("Erreur : impossible de supprimer un administrateur");
        }

        $req = $this->db->prepare("DELETE FROM Utilisateur WHERE id=?");
        return $req->execute([$id]);
    }


    /* ============================
       RÉCUPÉRER TOUS LES UTILISATEURS
    ============================ */
    public function getAllUsers() {
        $req = $this->db->query("SELECT * FROM Utilisateur");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER UN UTILISATEUR PAR ID
    ============================ */
    public function getUserById($id) {
        $req = $this->db->prepare("SELECT * FROM Utilisateur WHERE id=?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       RÉCUPÉRER UN UTILISATEUR PAR EMAIL
    ============================ */
    public function getUserByEmail($email) {
        $req = $this->db->prepare("SELECT * FROM Utilisateur WHERE email=?");
        $req->execute([$email]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }


    /* ============================
       RECHERCHE PAR USERNAME (LIKE)
    ============================ */
    public function getUserLikeUsername($username) {
        $req = $this->db->prepare("SELECT * FROM Utilisateur WHERE username LIKE ?");
        $req->execute(["%{$username}%"]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }


    /* ============================
       AUTHENTIFICATION
       - vérifie email
       - vérifie mot de passe
    ============================ */
    public function authentification($email, $password) {

        $user = $this->getUserByEmail($email);

        if ($user && password_verify($password, $user["password"])) {
            return $user;
        }

        return null;
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
