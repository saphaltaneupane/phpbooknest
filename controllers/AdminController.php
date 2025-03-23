<?php
class AdminController {
    private $book;

    public function __construct($pdo) {
        $this->book = new Book($pdo);
    }

    public function addBook($title, $author, $description, $price) {
        return $this->book->addBook($title, $author, $description, $price);
    }

    public function updateBookStatus($book_id, $status) {
        $stmt = $this->pdo->prepare("UPDATE books SET status = ? WHERE book_id = ?");
        return $stmt->execute([$status, $book_id]);
    }

    public function getRecommendations($book_id) {
        return $this->book->getRecommendations($book_id);
    }
}
?>
