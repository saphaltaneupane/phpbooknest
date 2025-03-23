<?php
class Purchase {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addPurchase($user_id, $book_id) {
        $stmt = $this->pdo->prepare("INSERT INTO purchases (user_id, book_id) VALUES (?, ?)");
        return $stmt->execute([$user_id, $book_id]);
    }
}
?>
