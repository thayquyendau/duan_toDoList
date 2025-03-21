<?php
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Category.php';

class TaskController {
    private $taskModel;
    private $categoryModel;

    public function __construct() {
        $this->taskModel = new Task();
        $this->categoryModel = new Category();
    }

    public function index() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /todolist/public/?action=login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $sort = isset($_GET['sort']) && $_GET['sort'] === 'true';

        $tasks = $this->taskModel->read($user_id, $filter, $search, $sort);
        $stats = $this->taskModel->getStats($user_id);
        $categories = $this->categoryModel->getAll();

        require_once __DIR__ . '/../views/layouts/main.php';
    }

    public function create() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];
            $user_id = $_SESSION['user_id'];

            if (!empty($title)) {
                $this->taskModel->create($title, $description, $user_id, $category_id);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Title cannot be empty']);
            }
        }
    }

    public function update() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $category_id = $_POST['category_id'];

            $this->taskModel->update($task_id, $title, $description, $category_id);
            echo json_encode(['success' => true]);
        }
    }

    public function toggle() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $status = $_POST['status'];
            $this->taskModel->toggle($task_id, $status);
            echo json_encode(['success' => true]);
        }
    }

    public function delete() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];
            $this->taskModel->delete($task_id);
            echo json_encode(['success' => true]);
        }
    }

    public function clearCompleted() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $_SESSION['user_id'];
            $this->taskModel->clearCompleted($user_id);
            echo json_encode(['success' => true]);
        }
    }

    public function export() {
        session_start();
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
?>