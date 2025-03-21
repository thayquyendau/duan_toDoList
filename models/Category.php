<?php
require_once __DIR__ . '/../config/database.php';

class Category {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($category_id) {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE category_id = ?");
        $stmt->execute([$category_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>