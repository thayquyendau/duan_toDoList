<?php
require_once __DIR__ . '/../config/database.php';

class Task
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function read($user_id, $filter = 'all', $search = '', $sort = false, $category_id = null)
    {
        $query = "SELECT tasks.*, categories.name AS category_name 
              FROM tasks 
              LEFT JOIN categories ON tasks.category_id = categories.id 
              WHERE tasks.user_id = :user_id";

        $params = [':user_id' => $user_id];

        if ($filter === 'completed') {
            $query .= " AND tasks.status = 'Completed'";
        } elseif ($filter === 'incomplete') {
            $query .= " AND tasks.status = 'Pending'";
        }

        if (isset($category_id)) {
            $query .= " AND tasks.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        if (!empty($search)) {
            $query .= " AND (tasks.title LIKE :search OR tasks.description LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($sort) {
            $query .= " ORDER BY tasks.status ASC";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // var_dump($result); // Kiểm tra dữ liệu trả về
        // die();
        return $result;
    }
    
    

    public function create($title, $description, $user_id, $category_id, $start_time, $end_time)
    {
        $stmt = $this->db->prepare("INSERT INTO tasks (title, description, user_id, category_id, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $user_id, $category_id, $start_time, $end_time]);
    }

    public function update($task_id, $title, $description, $category_id, $start_time, $end_time)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET title = ?, description = ?, category_id = ?, start_time = ?, end_time = ? WHERE task_id = ?");
        return $stmt->execute([$task_id, $title, $description, $category_id, $start_time, $end_time]);
    }

    public function toggle($task_id, $status)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET status = ? WHERE task_id = ?");
        return $stmt->execute([$status, $task_id]);
    }

    public function deleteTasksByCategory($category_id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE category_id = ?");
        return $stmt->execute([$category_id]);
    }

    public function delete($task_id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE task_id = ?");
        return $stmt->execute([$task_id]);
    }

    public function clearCompleted($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE status = 'Completed' AND user_id = ?");
        return $stmt->execute([$user_id]);
    }

    public function getStats($user_id)
    {
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
