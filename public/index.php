<?php
session_start();
require_once __DIR__ . '/../controllers/TaskController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if ($action === 'login'|| $action === 'register' || $action === 'logout') {
    $controller = new AuthController();
    if ($action === 'login') {
        $controller->login();
    }elseif($action === 'register'){
        $controller->register();
    }
     else {
        $controller->logout();
    }
} else {
    $controller = new TaskController();
    switch ($action) {
        case 'createTask':
            $controller->create();
            break;
        case 'updateTask':
            $controller->update();
            break;
        case 'deleteTask':
            $controller->delete();
            break;
        case 'createCategory':
            $controller->createCategory();
            break;
        case 'updateCategory':
            $controller->updateCategory();
            break;
        case 'deleteCategory':
            $controller->deleteCategory();
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