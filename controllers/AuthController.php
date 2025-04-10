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

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);
    
            if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = "All fields are required!";
                require_once __DIR__ . '/../views/auth/register.php';
                return;
            }
    
            if ($password !== $confirm_password) {
                $error = "Passwords do not match!";
                require_once __DIR__ . '/../views/auth/register.php';
                return;
            }
    
            if ($this->userModel->userExists($username, $email)) {
                $error = "Username or Email already taken!";
                require_once __DIR__ . '/../views/auth/register.php';
                return;
            }
    
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
            if ($this->userModel->createUser($username, $email, $hashed_password)) {
                header('Location: ?action=login'); 
                exit;
            } else {
                $error = "Registration failed, please try again!";
                require_once __DIR__ . '/../views/auth/register.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }
    

    public function logout() {
        
        session_destroy();
        header('Location: /todolist/public/?action=login');
        exit;
    }
}
?>