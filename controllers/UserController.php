<?php
require_once 'models/User.php';

class UserController {
    private $userModel;
    
    public function __construct($conn) {
        $this->userModel = new User($conn);
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = mysqli_real_escape_string($GLOBALS['conn'], $_POST['username']);
            $password = mysqli_real_escape_string($GLOBALS['conn'], $_POST['password']);
            
            $user = $this->userModel->login($username, $password);
            
            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                
                if ($user['user_type'] === 'admin') {
                    header('Location: index.php?page=admin_dashboard');
                } else {
                    header('Location: index.php?page=dashboard');
                }
                exit;
            } else {
                return ['error' => 'Invalid username or password'];
            }
        }
        
        require_once 'views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = mysqli_real_escape_string($GLOBALS['conn'], $_POST['username']);
            $email = mysqli_real_escape_string($GLOBALS['conn'], $_POST['email']);
            $password = mysqli_real_escape_string($GLOBALS['conn'], $_POST['password']);
            $full_name = mysqli_real_escape_string($GLOBALS['conn'], $_POST['full_name']);
            $address = isset($_POST['address']) ? mysqli_real_escape_string($GLOBALS['conn'], $_POST['address']) : null;
            $phone = isset($_POST['phone']) ? mysqli_real_escape_string($GLOBALS['conn'], $_POST['phone']) : null;
            
            $user_id = $this->userModel->register($username, $email, $password, $full_name, $address, $phone);
            
            if ($user_id) {
                session_start();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                $_SESSION['user_type'] = 'user';
                
                header('Location: index.php?page=dashboard');
                exit;
            } else {
                return ['error' => 'Registration failed. Username or email may already exist.'];
            }
        }
        
        require_once 'views/auth/register.php';
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        require_once 'models/Book.php';
        require_once 'models/Recommendation.php';
        
        $bookModel = new Book($GLOBALS['conn']);
        $recommendationModel = new Recommendation($GLOBALS['conn']);
        
        $books = $bookModel->getAllBooks(8);
        $recommendations = $recommendationModel->getRecommendationsForUser($_SESSION['user_id']);
        
        require_once 'views/user/dashboard.php';
    }
    
    public function profile() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'full_name' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['full_name']),
                'email' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['email']),
                'address' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['address']),
                'phone' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['phone'])
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = mysqli_real_escape_string($GLOBALS['conn'], $_POST['password']);
            }
            
            if ($this->userModel->updateUser($_SESSION['user_id'], $data)) {
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                $success = "Profile updated successfully";
            } else {
                $error = "Failed to update profile";
            }
        }
        
        require_once 'views/user/profile.php';
    }
}
?>