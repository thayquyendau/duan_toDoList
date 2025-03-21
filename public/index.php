<?php
require_once __DIR__ . '/../controllers/TaskController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if ($action === 'login' || $action === 'logout') {
    $controller = new AuthController();
    if ($action === 'login') {
        $controller->login();
    } else {
        $controller->logout();
    }
} else {
    $controller = new TaskController();
    switch ($action) {
        case 'create':
            $controller->create();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'toggle':
            $controller->toggle();
            break;
        case 'clear_completed':
            $controller->clearCompleted();
            break;
        case 'export':
            $controller->export();
            break;
        default:
            $controller->index();
            break;
    }
}
?>