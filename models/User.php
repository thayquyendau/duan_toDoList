<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userExists($username, $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch();
    }

    public function createUser($username, $email, $password) {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $password]);
    }

    public function getUserById($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function storeResetToken($email, $token) {
        $stmt = $this->db->prepare("INSERT INTO password_resets (email, token, created_at) VALUES (?, ?, NOW()) 
                                    ON DUPLICATE KEY UPDATE token = ?, created_at = NOW()");
        return $stmt->execute([$email, $token, $token]);
    }

    public function getResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($user_id, $password) {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        return $stmt->execute([$password, $user_id]);
    }

    public function deleteResetToken($email) {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = ?");
        return $stmt->execute([$email]);
    }
}
?>