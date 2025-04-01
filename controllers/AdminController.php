<?php
class AdminController {
    // Check if user is admin
    private function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
    }
    
    public function dashboard() {
        $this->checkAdmin();
        
        $users = User::getAllUsers();
        $books = Book::getAllBooks();
        $orders = Order::getAllOrders();
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/admin/dashboard.php';
        include 'views/layout/footer.php';
    }
    
    public function manageBooks() {
        $this->checkAdmin();
        
        $books = Book::getAllBooks();
        
        // Handle book status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id']) && isset($_POST['status'])) {
            $book_id = $_POST['book_id'];
            $status = $_POST['status'];
            
            Book::updateBookStatus($book_id, $status);
            
            // Redirect to refresh page
            header('Location: index.php?controller=admin&action=books');
            exit;
        }
        
        // Handle book deletion
        if (isset($_GET['delete_book'])) {
            $book_id = $_GET['delete_book'];
            
            Book::deleteBook($book_id);
            
            // Redirect to refresh page
            header('Location: index.php?controller=admin&action=books');
            exit;
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/admin/books.php';
        include 'views/layout/footer.php';
    }
    
    public function manageUsers() {
        $this->checkAdmin();
        
        $users = User::getAllUsers();
        
        // Handle user role update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
            $user_id = $_POST['user_id'];
            $role = $_POST['role'];
            
            User::updateUserRole($user_id, $role);
            
            // Redirect to refresh page
            header('Location: index.php?controller=admin&action=users');
            exit;
        }
        
        // Handle user deletion
        if (isset($_GET['delete_user'])) {
            $user_id = $_GET['delete_user'];
            
            User::deleteUser($user_id);
            
            // Redirect to refresh page
            header('Location: index.php?controller=admin&action=users');
            exit;
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/admin/users.php';
        include 'views/layout/footer.php';
    }
    
    public function manageOrders() {
        $this->checkAdmin();
        
        $orders = Order::getAllOrders();
        
        // Handle order status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
            $order_id = $_POST['order_id'];
            $status = $_POST['status'];
            
            Order::updateOrderStatus($order_id, $status);
            
            // Redirect to refresh page
            header('Location: index.php?controller=admin&action=orders');
            exit;
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/admin/orders.php';
        include 'views/layout/footer.php';
    }
}
?>