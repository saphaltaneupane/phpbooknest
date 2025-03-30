<?php
require_once 'models/User.php';
require_once 'models/Book.php';

class AdminController {
    private $userModel;
    private $bookModel;
    
    public function __construct($conn) {
        $this->userModel = new User($conn);
        $this->bookModel = new Book($conn);
    }
    
    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $totalUsers = count($this->userModel->getAllUsers());
        $totalBooks = count($this->bookModel->getAllAdminBooks());
        
        require_once 'views/admin/dashboard.php';
    }
    
    public function manageUsers() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $users = $this->userModel->getAllUsers();
        
        require_once 'views/admin/manage_users.php';
    }
    
    public function deleteUser($id) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($id != $_SESSION['user_id']) {  // Prevent admin from deleting themselves
            $this->userModel->deleteUser($id);
        }
        
        header('Location: index.php?page=admin_manage_users');
        exit;
    }
    
    public function manageBooks() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $books = $this->bookModel->getAllAdminBooks();
        
        require_once 'views/admin/manage_books.php';
    }
    
    public function addBook() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = mysqli_real_escape_string($GLOBALS['conn'], $_POST['title']);
            $author = mysqli_real_escape_string($GLOBALS['conn'], $_POST['author']);
            $description = mysqli_real_escape_string($GLOBALS['conn'], $_POST['description']);
            $price = (float) $_POST['price'];
            $genre = mysqli_real_escape_string($GLOBALS['conn'], $_POST['genre']);
            $condition = mysqli_real_escape_string($GLOBALS['conn'], $_POST['condition']);
            
            // Handle image upload
            $image = 'default_book.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $upload_dir = 'assets/images/books/';
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . $_FILES['image']['name'];
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = 'books/' . $file_name;
                }
            }
            
            $book_id = $this->bookModel->addBook($title, $author, $description, $price, $genre, $condition, $_SESSION['user_id'], false, $image);
            
            if ($book_id) {
                header('Location: index.php?page=admin_manage_books');
                exit;
            } else {
                $error = "Failed to add book";
            }
        }
        
        $genres = $this->bookModel->getGenres();
        require_once 'views/admin/add_book.php';
    }
    
    public function editBook($id) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $book = $this->bookModel->getBookById($id);
        
        if (!$book) {
            header('Location: index.php?page=admin_manage_books');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['title']),
                'author' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['author']),
                'description' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['description']),
                'price' => (float) $_POST['price'],
                'genre' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['genre']),
                'condition' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['condition']),
                'status' => mysqli_real_escape_string($GLOBALS['conn'], $_POST['status'])
            ];
            
            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $upload_dir = 'assets/images/books/';
                
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = time() . '_' . $_FILES['image']['name'];
                $upload_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $data['image'] = 'books/' . $file_name;
                }
            }
            
            if ($this->bookModel->updateBook($id, $data)) {
                header('Location: index.php?page=admin_manage_books');
                exit;
            } else {
                $error = "Failed to update book";
            }
        }
        
        $genres = $this->bookModel->getGenres();
        require_once 'views/admin/edit_book.php';
    }
    
    public function deleteBook($id) {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        
        $this->bookModel->deleteBook($id);
        
        header('Location: index.php?page=admin_manage_books');
        exit;
    }
}
?>