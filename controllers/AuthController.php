<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            // var_dump($_POST);
            $user = $this->userModel->login($username, $password);
            // var_dump($user);

            if ($user) {
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /todolist/public/');
                exit;
            } else {
                $error = "Invalid username or password";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout() {
        
        session_destroy();
        header('Location: /todolist/public/?action=login');
        exit;
    }
}
?>
