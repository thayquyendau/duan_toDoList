<?php

use LDAP\Result;

require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Category.php';

class TaskController
{
    private $taskModel;
    private $categoryModel;

    public function __construct()
    {
        $this->taskModel = new Task();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /todolist/public/?action=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'incomplete';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $sort = isset($_GET['sort']) && $_GET['sort'] === 'true';

        $categories = $this->categoryModel->getAll();

        $tasksByCategory = [];
        foreach ($categories as $category) {
            $category_id = $category['id'];
            $tasks = $this->taskModel->read($user_id, $filter, $search, $sort, $category_id);
            $tasksByCategory[$category_id] = [
                'name' => $category['name'],
                'tasks' => $tasks
            ];
        }

        $tasksWithoutCategory = $this->taskModel->read($user_id, $filter, $search, $sort, 'none');

        $stats = $this->taskModel->getStats($user_id);

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function createCategory()
    {
        $name = $_POST['name'];
        // Kiểm tra dữ liệu đầu vào
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Tên danh mục không được để trống.']);
            exit;
        }
        // Kiểm tra danh mục đã tồn tại chưa
        if ($this->categoryModel->categoryExists($name)) {
            echo json_encode(['success' => false, 'message' => 'Tên danh mục đã tồn tại.']);
            exit;
        }
        // Thêm danh mục mới
        $result = $this->categoryModel->createCategory($name);
        echo json_encode([
            'success' => true,
            'message' => 'Thêm danh mục thành công.'
        ]);
    }

    public function updateCategory()
    {
        // Lấy dữ liệu từ POST
        $id = $_POST['id'] ?? null;
        $name = $_POST['name'] ?? '';

        // Kiểm tra dữ liệu đầu vào
        if (empty($id) || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            exit;
        }

        // Kiểm tra danh mục đã tồn tại chưa (trừ danh mục hiện tại)    
        if ($this->categoryModel->categoryExists($name, $id)) {
            echo json_encode(['success' => false, 'message' => 'Tên danh mục đã tồn tại.']);
            exit;
        }

        // Cập nhật danh mục
        try {
            $result = $this->categoryModel->updateCategory($id, $name);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi server: ' . $e->getMessage()]);
        }
        exit;
    }
    public function deleteCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $category_id = $_POST['id'];
            // Xóa tất cả các task thuộc danh mục này
            $this->taskModel->deleteTasksByCategory($category_id);
            $this->categoryModel->deleteCategory($category_id);
            echo json_encode(['success' => true]);
        }
    }

    public function createTask()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $user_id = $_SESSION['user_id'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            if (!empty($title) && !empty($description) && !empty($category_id) && !empty($start_time) && !empty($end_time)) {

                $task_id = $this->taskModel->create($title, $description, $user_id, $category_id, $start_time, $end_time);
                $task = $this->taskModel->getTaskById($task_id);
                $action = $this->taskModel->logUserAction($user_id, "Đã thêm nhiệm vụ", $task_id, $title);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            }
        }
    }

    public function updateTask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $task_id = $_POST['task_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            if ($title !== '' && $description !== '' && $category_id !== '' && $start_time !== '' && $end_time !== '') {
                $result = $this->taskModel->update($task_id, $title, $description, $category_id, $start_time, $end_time);
                $task = $this->taskModel->getTaskById($task_id);
                // Nếu có bản ghi bị ảnh hưởng
                if ($result > 0) {

                    $this->taskModel->logUserAction($user_id, "Đã sửa nhiệm vụ", $task_id, $title);
                    echo json_encode(['success' => true, 'message' => 'Sửa nhiệm vụ thành công!']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Không có nhiệm vụ nào được sửa']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
            }
        }
    }


    public function toggle()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $status = $_POST['status'];
            $this->taskModel->toggle($task_id, $status);
            echo json_encode(['success' => true]);
        }
    }

    public function deleteTask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $task_id = $_POST['task_id'];

            if (!empty($task_id)) {
                // Lấy tiêu đề nhiệm vụ trước khi xóa
                $task = $this->taskModel->getTaskById($task_id);
                $title = $task['title'] ?? 'Không rõ';

                $result = $this->taskModel->delete($task_id);

                if ($result > 0) {
                    $this->taskModel->logUserAction($user_id, "Đã xóa nhiệm vụ", $task_id, $title);
                    echo json_encode(['success' => true, 'message' => 'Xóa nhiệm vụ thành công!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy nhiệm vụ cần xóa.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID nhiệm vụ cần xóa.']);
            }
        }
    }
    public function restoreTask()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $user_id = $_SESSION['user_id'];
            $task = $this->taskModel->getTaskById($task_id);
            $result = $this->taskModel->restoreTask($task_id);
            if ($result>0) {
                $this->taskModel->logUserAction($user_id, "Đã khôi phục nhiệm vụ", $task_id, $task['title']);
                echo json_encode(['success' => true,  'message' => 'Khôi phục nhiệm vụ thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể khôi phục nhiệm vụ!']);
            }
        }
    }


    public function deleteAllAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];

            $result = $this->taskModel->deleteAllActionsByUser($user_id);
            if ($result > 0) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa toàn bộ lịch sử']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có lịch sử để xóa']);
            }
        }
    }


    public function clearCompleted()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $this->taskModel->clearCompleted($user_id);
            echo json_encode(['success' => true]);
        }
    }

    public function export()
    {

        if (!isset($_SESSION['user_id'])) {
            header('Location: /todolist/public/?action=login');
            exit;
        }

        require_once __DIR__ . '/../vendor/autoload.php';
        $user_id = $_SESSION['user_id'];
        $tasks = $this->taskModel->read($user_id);

        $pdf = new \TCPDF();
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->Cell(0, 10, 'To-Do List', 0, 1, 'C');

        foreach ($tasks as $index => $task) {
            $status = $task['status'] === 'Completed' ? '(Completed)' : '';
            $pdf->Cell(0, 10, ($index + 1) . ". {$task['title']} $status", 0, 1);
        }

        $pdf->Output('todolist.pdf', 'D');
    }

    public function historyAction()
    {

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'User not logged in']);
            return;
        }
        // Lấy ID người dùng từ session
        $user_id = $_SESSION['user_id'];

        // Gọi phương thức trong model để lấy lịch sử thao tác
        $history = $this->taskModel->getHistoryAction($user_id);

        if (empty($history)) {
            echo json_encode(['error' => 'Không có bản ghi nào']);
        } else {

            echo json_encode($history);
        }
    }
}
