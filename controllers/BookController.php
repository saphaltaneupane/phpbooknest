<?php
class BookController {
    public function index() {
        $books = Book::getAllBooks();
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/books/index.php';
        include 'views/layout/footer.php';
    }
    
    public function view($id) {
        if (!$id) {
            header('Location: index.php?controller=book&action=index');
            exit;
        }
        
        $book = Book::getBookById($id);
        
        if (!$book) {
            // Book not found
            header('Location: index.php?controller=book&action=index');
            exit;
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/books/view.php';
        include 'views/layout/footer.php';
    }
    
    public function add() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $author = $_POST['author'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $condition = $_POST['condition'] ?? 'new';
            $user_id = $_SESSION['user_id'];
            
            if (Book::addBook($title, $author, $description, $price, $condition, $user_id)) {
                // Book added successfully
                header('Location: index.php?controller=book&action=index');
                exit;
            }
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/books/add.php';
        include 'views/layout/footer.php';
    }
    
    public function search($query) {
        $books = Book::searchBooks($query);
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/books/index.php';
        include 'views/layout/footer.php';
    }
    
    public function filter($min, $max, $sort = 'asc') {
        // Get all books
        $allBooks = Book::getBooksForBST();
        
        // Create BST
        $bst = new BookBST();
        
        // Insert all books into BST
        foreach ($allBooks as $book) {
            $bst->insert($book['price'], $book);
        }
        
        // Filter books by price range
        if ($min != 0 || $max != PHP_INT_MAX) {
            $books = $bst->searchByPriceRange($min, $max);
        } else {
            // Get all books with specified sorting
            $books = $bst->inOrderTraversal($sort);
        }
        
        // Load the view
        include 'views/layout/header.php';
        include 'views/books/index.php';
        include 'views/layout/footer.php';
    }
}
?>