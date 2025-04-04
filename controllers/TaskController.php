<?php
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

    public function createCategory() { 
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
                echo json_encode(['success' => true,
                 'message' => 'Thêm danh mục thành công.']);
    }

    public function updateCategory() {
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

    public function create()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $user_id = $_SESSION['user_id'];

            if (!empty($title) && !empty($description) && !empty($category_id)) {
                $this->taskModel->create($title, $description, $user_id, $category_id);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill in all information!']);
            }
        }
    }

    public function update()
    {



        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];

            if ($title !== '' && $description !== '' && $category_id !== '') {
                $this->taskModel->update($task_id, $title, $description, $category_id);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill in all information!']);
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

    public function delete()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $this->taskModel->delete($task_id);
            echo json_encode(['success' => true]);
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
}
