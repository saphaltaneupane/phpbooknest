<?php
session_start();

// Include database connection
require_once 'config/db_connect.php';

// Default controller and action
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'book';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Routes that don't require authentication
$publicRoutes = [
    'user-login', 'user-register', 'book-index', 'book-search', 'book-details'
];

// Check if the current route requires authentication
$currentRoute = $controller . '-' . $action;
if (!$isLoggedIn && !in_array($currentRoute, $publicRoutes) && $controller != 'user') {
    // Redirect to login page
    header('Location: index.php?controller=user&action=login');
    exit();
}

// Admin routes check
if ($controller == 'admin' && (!$isLoggedIn || $_SESSION['user_type'] != 'admin')) {
    // Redirect non-admin users
    header('Location: index.php?controller=user&action=login');
    exit();
}

// Include controllers
require_once 'controllers/UserController.php';
require_once 'controllers/BookController.php';
require_once 'controllers/AdminController.php';
require_once 'controllers/RecommendationController.php';

// Route to appropriate controller
switch ($controller) {
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
            case 'dashboard':
                $userController->dashboard();
                break;
            case 'addUsedBook':
                $userController->addUsedBook();
                break;
            case 'myBooks':
                $userController->myBooks();
                break;
            case 'purchaseHistory':
                $userController->purchaseHistory();
                break;
            default:
                $userController->login();
                break;
        }
        break;
    
    case 'book':
        $bookController = new BookController();
        switch ($action) {
            case 'index':
                $bookController->index();
                break;
            case 'search':
                $bookController->search();
                break;
            case 'details':
                $bookController->details();
                break;
            case 'buy':
                $bookController->buy();
                break;
            default:
                $bookController->index();
                break;
        }
        break;
        
    case 'admin':
        $adminController = new AdminController();
        switch ($action) {
            case 'dashboard':
                $adminController->dashboard();
                break;
            case 'manageBooks':
                $adminController->manageBooks();
                break;
            case 'manageUsers':
                $adminController->manageUsers();
                break;
            case 'addBook':
                $adminController->addBook();
                break;
            case 'editBook':
                $adminController->editBook();
                break;
            case 'deleteBook':
                $adminController->deleteBook();
                break;
            case 'editUser':
                $adminController->editUser();
                break;
            case 'deleteUser':
                $adminController->deleteUser();
                break;
            default:
                $adminController->dashboard();
                break;
        }
        break;
        
    case 'recommendation':
        $recommendationController = new RecommendationController();
        switch ($action) {
            case 'getRecommendations':
                $recommendationController->getRecommendations();
                break;
            default:
                $recommendationController->getRecommendations();
                break;
        }
        break;
        
    default:
        // Default to book controller
        $bookController = new BookController();
        $bookController->index();
        break;
}
?>