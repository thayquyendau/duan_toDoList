<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->userModel->login($username);

            if ($user && password_verify($password, $user['password'])) {
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

    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            if (empty($email)) {
                $error = "Please enter your email!";
                require_once __DIR__ . '/../views/auth/forgot_password.php';
                return;
            }

            $user = $this->userModel->getUserByEmail($email);
            if (!$user) {
                $error = "Email not found!";
                require_once __DIR__ . '/../views/auth/forgot_password.php';
                return;
            }

            $token = bin2hex(random_bytes(32));
            $this->userModel->storeResetToken($email, $token);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'lehieuphuoc35205@gmail.com'; // Thay bằng email của bạn
                $mail->Password = 'lioxvftnrihvyibe'; // Thay bằng App Password của Gmail
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('lehieuphuoc35205@gmail.com', 'To-Do List App');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Reset Your Password';
                $mail->Body = "Click the link to reset your password: <a href='http://localhost/todolist/public/?action=resetPassword&token=$token'>Reset Password</a>";
                $mail->AltBody = "Copy this link to reset your password: http://localhost/todolist/public/?action=resetPassword&token=$token";

                $mail->send();
                $success = "A password reset link has been sent to your email.";
                require_once __DIR__ . '/../views/auth/forgot_password.php';
            } catch (Exception $e) {
                $error = "Failed to send email. Please try again.";
                require_once __DIR__ . '/../views/auth/forgot_password.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/forgot_password.php';
        }
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $password = trim($_POST['password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($password) || empty($confirm_password)) {
                $error = "All fields are required!";
                require_once __DIR__ . '/../views/auth/reset_password.php';
                return;
            }

            if ($password !== $confirm_password) {
                $error = "Passwords do not match!";
                require_once __DIR__ . '/../views/auth/reset_password.php';
                return;
            }

            $reset = $this->userModel->getResetToken($token);
            if (!$reset) {
                $error = "Invalid or expired token!";
                require_once __DIR__ . '/../views/auth/reset_password.php';
                return;
            }

            $user = $this->userModel->getUserByEmail($reset['email']);
            if (!$user) {
                $error = "User not found!";
                require_once __DIR__ . '/../views/auth/reset_password.php';
                return;
            }

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            if ($this->userModel->updatePassword($user['user_id'], $hashed_password)) {
                $this->userModel->deleteResetToken($reset['email']);
                $success = "Password reset successfully. Please login.";
                require_once __DIR__ . '/../views/auth/reset_password.php';
            } else {
                $error = "Failed to reset password. Please try again.";
                require_once __DIR__ . '/../views/auth/reset_password.php';
            }
        } else {
            $token = $_GET['token'] ?? '';
            require_once __DIR__ . '/../views/auth/reset_password.php';
        }
    }

    public function changePassword() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /todolist/public/?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = trim($_POST['current_password']);
            $new_password = trim($_POST['new_password']);
            $confirm_password = trim($_POST['confirm_password']);

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error = "All fields are required!";
                require_once __DIR__ . '/../views/auth/change_password.php';
                return;
            }

            if ($new_password !== $confirm_password) {
                $error = "New passwords do not match!";
                require_once __DIR__ . '/../views/auth/change_password.php';
                return;
            }

            $user = $this->userModel->getUserById($_SESSION['user_id']);
            if (!$user || !password_verify($current_password, $user['password'])) {
                $error = "Current password is incorrect!";
                require_once __DIR__ . '/../views/auth/change_password.php';
                return;
            }

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            if ($this->userModel->updatePassword($_SESSION['user_id'], $hashed_password)) {
                $success = "Password changed successfully!";
                require_once __DIR__ . '/../views/auth/change_password.php';
            } else {
                $error = "Failed to change password. Please try again.";
                require_once __DIR__ . '/../views/auth/change_password.php';
            }
        } else {
            require_once __DIR__ . '/../views/auth/change_password.php';
        }
    }
}
?>