<?php
require_once __DIR__ . '/../config/database.php';

class Task {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function read($user_id, $filter = 'all', $search = '', $sort = false) {
        $query = "SELECT t.*, c.name as category_name 
                 FROM tasks t 
                 LEFT JOIN categories c ON t.category_id = c.category_id 
                 WHERE t.user_id = ?";
        $conditions = [];
        $params = [$user_id];

        if ($filter === 'completed') {
            $conditions[] = "t.status = 'Completed'";
        } elseif ($filter === 'incomplete') {
            $conditions[] = "t.status = 'Pending'";
        }

        if (!empty($search)) {
            $conditions[] = "t.title LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($conditions)) {
            $query .= " AND " . implode(" AND ", $conditions);
        }

        if ($sort) {
            $query .= " ORDER BY t.status ASC";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $description, $user_id, $category_id) {
        $stmt = $this->db->prepare("INSERT INTO tasks (title, description, user_id, category_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $user_id, $category_id]);
    }

    public function update($task_id, $title, $description, $category_id) {
        $stmt = $this->db->prepare("UPDATE tasks SET title = ?, description = ?, category_id = ? WHERE task_id = ?");
        return $stmt->execute([$title, $description, $category_id, $task_id]);
    }

    public function toggle($task_id, $status) {
        $stmt = $this->db->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
        return $stmt->execute([$status, $task_id]);
    }

    public function delete($task_id) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE task_id = ?");
        return $stmt->execute([$task_id]);
    }

    public function clearCompleted($user_id) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE status = 'Completed' AND user_id = ?");
        return $stmt->execute([$user_id]);
    }

    public function getStats($user_id) {
        $totalStmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE user_id = ?");
        $totalStmt->execute([$user_id]);
        $total = $totalStmt->fetchColumn();

        $completedStmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE status = 'Completed' AND user_id = ?");
        $completedStmt->execute([$user_id]);
        $completed = $completedStmt->fetchColumn();

        $incomplete = $total - $completed;
        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $incomplete
        ];
    }
}
?>