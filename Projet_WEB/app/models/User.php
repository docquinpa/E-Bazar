<?php
class UserModel {
    private $db;
    public function __construct($pdo) {
        $this->db = $this->isCorrect($pdo);
    }

    public function createUser($email, $username, $password, $role) {
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Format d'email invalide");
        }
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
            throw new InvalidArgumentException( "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre" );
        }
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $req = $this->db->prepare("INSERT INTO Users (email, username, password, role) VALUES (?, ?, ?, ?)");
        return $req->execute([$email, $username, $hashedPass, $role]);
    }

    public function updateUser($id, $email = null, $username = null, $password = null, $role = null) {
        $user = $this->getUserById($id);
        if (!$user) {
            throw new Exception("Utilisateur introuvable");
        }
        $tabreq = [];
        $params = [];
        if ($email !== null) {
            if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Format d'email invalide");
            }
            $tabreq[] = "email = ?";
            $params[] = $email;
        }
        if ($username !== null) {
            $tabreq[] = "username = ?";
            $params[] = $username;
        }
        if ($password !== null && trim($password) !== "") {
            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
                throw new InvalidArgumentException( "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre" );
            }
            $tabreq[] = "password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }
        if ($role !== null) {
            $tabreq[] = "role = ?";
            $params[] = $role;
        }
         if (empty($tabreq)) {
            throw new Exception("Aucune donnée à mettre à jour");;
        }
        $params[] = $id;
        $sql = "UPDATE Users SET " . implode(", ", $tabreq) . " WHERE id = ?";
        $req = $this->db->prepare($sql);
        return $req->execute($params);
    }

    public function deleteUser($id) {
        $user = $this->getUserById($id);
        if (!$user) {
            throw new Exception("Utilisateur introuvable");
        }
        if ($user["role"] == "admin") {
            throw new Exception("Erreur : impossible de supprimer un administrateur");
        }
        $req = $this->db->prepare("DELETE FROM Users WHERE id=?");
        return $req->execute([$id]);
    }

    public function getAllUsers() {
        $req = $this->db->query("SELECT * from Users");
        $users = $req->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    public function getUserById($id) {
        $req = $this->db->prepare("SELECT * FROM Users WHERE id=?");
        $req->execute([$id]);
        $user = $req->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function getUserByEmail($email) {
        $req = $this->db->prepare("SELECT * FROM Users WHERE email=?");
        $req->execute([$email]);
        $user = $req->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    public function authentification($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user) {
            if (password_verify($password, $user["password"])) {
                return $user;
            }
        }
        return null;
    }


    private function isCorrect($pdo) {
        if (is_null($pdo)) {
            throw new Exception("Erreur : le PDO donné est null");
        }
        return $pdo;
    }
}
?>