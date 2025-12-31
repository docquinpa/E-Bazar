<?php

class UserController {

    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
    }

    public function showRegisterForm() {
        require __DIR__ . '/../views/register.php';
    }

    public function register() {
        try {
            $email = $_POST['email'] ?? null;
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            // Tous les utilisateurs créés via register sont "user"
            $this->userModel->createUser($email, $username, $password, "user");

            header("Location: index.php?action=login&success=1");
            exit;

        } catch (Exception $e) {
            $error = $e->getMessage();
            require __DIR__ . '/../views/register.php';
        }
    }

    public function showLoginForm() {
        require __DIR__ . '/../views/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        $user = $this->userModel->authentification($email, $password);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            header("Location: index.php?action=home");
            exit;
        } else {
            $error = "Identifiants incorrects";
            require __DIR__ . '/../views/login.php';
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=home");
        exit;
    }
}
?>
