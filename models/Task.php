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
              WHERE tasks.user_id = :user_id AND is_deleted = 0 ";

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
        $stmt = $this->db->prepare("INSERT INTO tasks (title, description, user_id, category_id, start_time, end_time)
VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt->execute([$title, $description, $user_id, $category_id, $start_time, $end_time])) {
            // Nếu có lỗi khi thực thi, throw exception
            throw new Exception("Lỗi execute: " . implode(", ", $stmt->errorInfo()));
        }

        // Lấy ID của task vừa tạo
        $task_id = $this->db->lastInsertId();
        return $task_id;
    }


    public function update($task_id, $title, $description, $category_id, $start_time, $end_time)
    {
        $stmt = $this->db->prepare("UPDATE tasks 
        SET title = ?, description = ?, category_id = ?, start_time = ?, end_time = ? 
        WHERE task_id = ?");
        if (!$stmt->execute([$title, $description, $category_id, $start_time, $end_time, $task_id])) {
            throw new Exception("Lỗi khi cập nhật: " . implode(", ", $stmt->errorInfo()));
        }

        return $stmt->rowCount(); // Trả về số dòng bị ảnh hưởng
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

    public function getTaskById($task_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE task_id = ?");
        $stmt->execute([$task_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($task_id)
    {
        // $stmt = $this->db->prepare("DELETE FROM tasks WHERE task_id = ?");
        $stmt = $this->db->prepare("UPDATE tasks SET is_deleted = 1 WHERE task_id = ?");
        $stmt->execute([$task_id]);

        return $stmt->rowCount(); // Trả về số dòng bị ảnh hưởng (thường là 1 nếu xóa thành công)
    }

    public function restoreTask($task_id)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET is_deleted = 0 WHERE task_id = ?");
        $stmt->execute([$task_id]);
        return $stmt->rowCount();
    }

    public function deleteAllActionsByUser($user_id)
    {
        $stmt = $this->db->prepare("DELETE FROM user_actions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->rowCount();
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

    public function getHistoryAction($user_id)
    {
        // Chuẩn bị câu lệnh SQL để lấy các hành động mới nhất của mỗi task mà người dùng đã thực hiện
        $stmt = $this->db->prepare("
            SELECT ac.*, t.is_deleted
            FROM user_actions ac
            -- Bảng phụ: chọn hành động mới nhất (MAX timestamp) theo từng task_id của người dùng
            JOIN (
                SELECT MAX(timestamp) AS max_time, task_id
                FROM user_actions
                WHERE user_id = ?
                GROUP BY task_id
            ) latest 
            ON ac.task_id = latest.task_id AND ac.timestamp = latest.max_time
    
            -- Nối với bảng tasks để lấy trạng thái is_deleted
            LEFT JOIN tasks t ON ac.task_id = t.task_id
    
            -- Đảm bảo chỉ lấy hành động của user hiện tại
            WHERE ac.user_id = ?
            ORDER BY ac.timestamp DESC -- Sắp xếp theo thời gian mới nhất
        ");

        // Truyền tham số vào câu SQL: $user_id cho cả bảng phụ và điều kiện chính
        $stmt->execute([$user_id, $user_id]);

        // Trả về kết quả dạng mảng kết hợp (associative array)
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function logUserAction($user_id, $action, $task_id = null, $title = null)
    {
        $stmt = $this->db->prepare("INSERT INTO user_actions (user_id, action, task_id, title) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $action, $task_id, $title]);
    }
}
