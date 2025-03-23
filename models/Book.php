<?php
class Book {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all books
    public function getAllBooks() {
        $stmt = $this->pdo->prepare("SELECT * FROM books");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new book
    public function addBook($title, $author, $description, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO books (title, author, description, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $author, $description, $price]);
    }

    // Get recommendations based on similar author or description
    public function getRecommendations($book_id) {
        // Fetch the book details
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch books by similar author or description
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE (author = ? OR description LIKE ?) AND book_id != ?");
        $stmt->execute([$book['author'], "%" . $book['description'] . "%", $book_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search books by title or author
    public function searchBooks($searchTerm) {
        $stmt = $this->pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
        $stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
