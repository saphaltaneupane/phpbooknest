<?php
require_once 'models/Book.php';
require_once 'models/Recommendation.php';

class BookController {
    private $bookModel;
    private $recommendationModel;
    
    public function __construct($conn) {
        $this->bookModel = new Book($conn);
        $this->recommendationModel = new Recommendation($conn);
    }
    
    public function listBooks() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $books = $this->bookModel->getAllBooks();
        $genres = $this->bookModel->getGenres();
        
        require_once 'views/user/books.php';
    }
    
    public function searchBooks() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($GLOBALS['conn'], $_GET['keyword']) : '';
        $genre = isset($_GET['genre']) ? mysqli_real_escape_string($GLOBALS['conn'], $_GET['genre']) : '';
        
        $books = $this->bookModel->searchBooks($keyword, $genre);
        $genres = $this->bookModel->getGenres();
        
        require_once 'views/user/search_books.php';
    }
    
    public function bookDetails($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $book = $this->bookModel->getBookById($id);
        
        if (!$book) {
            header('Location: index.php?page=books');
            exit;
        }
        
        $similarBooks = $this->recommendationModel->getRecommendationsBasedOnBook($id, 4);
        
        require_once 'views/user/book_details.php';
    }
    
    public function addUsedBook() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
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
            
            $book_id = $this->bookModel->addBook($title, $author, $description, $price, $genre, $condition, $_SESSION['user_id'], true, $image);
            
            if ($book_id) {
                header('Location: index.php?page=my_books');
                exit;
            } else {
                $error = "Failed to add book";
            }
        }
        
        $genres = $this->bookModel->getGenres();
        require_once 'views/user/add_used_book.php';
    }
    
    public function myBooks() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $books = $this->bookModel->getBooksByUserId($_SESSION['user_id']);
        
        require_once 'views/user/my_books.php';
    }
    
    public function deleteMyBook($id) {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $book = $this->bookModel->getBookById($id);
        
        if ($book && $book['seller_id'] == $_SESSION['user_id']) {
            $this->bookModel->deleteBook($id);
        }