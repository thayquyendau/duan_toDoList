<?php
session_start();
require_once __DIR__ . '/../controllers/TaskController.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if ($action === 'login' || $action === 'register' || $action === 'logout' || $action === 'forgotPassword' || $action === 'resetPassword' || $action === 'changePassword') {
    $controller = new AuthController();
    if ($action === 'login') {
        $controller->login();
    } elseif ($action === 'register') {
        $controller->register();
    } elseif ($action === 'forgotPassword') {
        $controller->forgotPassword();
    } elseif ($action === 'resetPassword') {
        $controller->resetPassword();
    } elseif ($action === 'changePassword') {
        $controller->changePassword();
    } else {
        $controller->logout();
    }
} else {
    $controller = new TaskController();
    switch ($action) {
        case 'restoreTask':
            $controller->restoreTask();
            break;
        case 'deleteAllAction':
            $controller->deleteAllAction();
            break;
        case 'historyAction':
            $controller->historyAction();
            break;
        case 'createTask':
            $controller->createTask();
            break;
        case 'updateTask':
            $controller->updateTask();
            break;
        case 'deleteTask':
            $controller->deleteTask();
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