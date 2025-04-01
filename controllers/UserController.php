<?php
class UserController {
    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = User::login($username, $password);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: index.php?controller=admin&action=dashboard');
                } else {
                    header('Location: index.php?controller=book&action=index');
                }
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/users/login.php';
        include 'views/layout/footer.php';
    }
    
    public function register() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $email = $_POST['email'] ?? '';
            
            // Validate input
            if (empty($username) || empty($password) || empty($email)) {
                $error = 'All fields are required';
            } elseif ($password !== $confirm_password) {
                $error = 'Passwords do not match';
            } elseif (User::getUserByUsername($username)) {
                $error = 'Username already exists';
            } else {
                // Register user
                if (User::register($username, $password, $email)) {
                    // Redirect to login page
                    header('Location: index.php?controller=user&action=login');
                    exit;
                } else {
                    $error = 'Registration failed';
                }
            }
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/users/register.php';
        include 'views/layout/footer.php';
    }
    
    public function logout() {
        // Destroy session
        session_destroy();
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    }
    
    public function profile() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        $user = User::getUserById($_SESSION['user_id']);
        $books = Book::getBooksByUser($_SESSION['user_id']);
        $orders = Order::getOrdersByUser($_SESSION['user_id']);
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/users/profile.php';
        include 'views/layout/footer.php';
    }
}
?>