<?php
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Application routes
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'book';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Include database configuration
require_once 'config/database.php';

// Include models
require_once 'models/User.php';
require_once 'models/Book.php';
require_once 'models/Order.php';

// Include the Binary Search Tree library
require_once 'libs/BST.php';

// Include controllers
require_once 'controllers/BookController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/AdminController.php';

// Route to the appropriate controller based on URL parameters
switch ($controller) {
    case 'book':
        $bookController = new BookController();
        
        switch ($action) {
            case 'index':
                $bookController->index();
                break;
            case 'view':
                $bookController->view($_GET['id'] ?? null);
                break;
            case 'add':
                $bookController->add();
                break;
            case 'search':
                $bookController->search($_GET['query'] ?? '');
                break;
            case 'filter':
                $bookController->filter($_GET['min'] ?? 0, $_GET['max'] ?? PHP_INT_MAX, $_GET['sort'] ?? 'asc');
                break;
            default:
                $bookController->index();
                break;
        }
        break;
        
    case 'user':
        $userController = new UserController();
        
        switch ($action) {
            case 'login':
                $userController->login();
                break;
            case 'register':
                $userController->register();
                break;
            case 'logout':
                $userController->logout();
                break;
            case 'profile':
                $userController->profile();
                break;
            default:
                $userController->login();
                break;
        }
        break;
        
    case 'order':
        $orderController = new OrderController();
        
        switch ($action) {
            case 'checkout':
                $orderController->checkout($_GET['book_id'] ?? null);
                break;
            case 'process':
                $orderController->processOrder();
                break;
            case 'history':
                $orderController->history();
                break;
            case 'khalti_verify':
                $orderController->verifyKhaltiPayment();
                break;
            default:
                $orderController->history();
                break;
        }
        break;
        
    case 'admin':
        $adminController = new AdminController();
        
        switch ($action) {
            case 'dashboard':
                $adminController->dashboard();
                break;
            case 'books':
                $adminController->manageBooks();
                break;
            case 'users':
                $adminController->manageUsers();
                break;
            case 'orders':
                $adminController->manageOrders();
                break;
            default:
                $adminController->dashboard();
                break;
        }
        break;
        
    default:
        $bookController = new BookController();
        $bookController->index();
        break;
}
?>