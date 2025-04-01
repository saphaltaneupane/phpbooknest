<?php
class Book {
    // Get all books
    public static function getAllBooks() {
        $sql = "SELECT books.*, users.username AS seller 
                FROM books 
                LEFT JOIN users ON books.user_id = users.id 
                ORDER BY books.created_at DESC";
        return fetchAll($sql);
    }
    
    // Get book by ID
    public static function getBookById($id) {
        $id = escapeString($id);
        $sql = "SELECT books.*, users.username AS seller 
                FROM books 
                LEFT JOIN users ON books.user_id = users.id 
                WHERE books.id = '$id'";
        return fetchOne($sql);
    }
    
    // Search books by query
    public static function searchBooks($query) {
        $query = escapeString($query);
        $sql = "SELECT books.*, users.username AS seller 
                FROM books 
                LEFT JOIN users ON books.user_id = users.id 
                WHERE books.title LIKE '%$query%' 
                OR books.author LIKE '%$query%' 
                OR books.description LIKE '%$query%'";
        return fetchAll($sql);
    }
    
    // Get all books for BST
    public static function getBooksForBST() {
        $sql = "SELECT * FROM books";
        return fetchAll($sql);
    }
    
    // Add new book
    public static function addBook($title, $author, $description, $price, $condition, $user_id) {
        $title = escapeString($title);
        $author = escapeString($author);
        $description = escapeString($description);
        $price = escapeString($price);
        $condition = escapeString($condition);
        $user_id = escapeString($user_id);
        
        $sql = "INSERT INTO books (title, author, description, price, `condition`, user_id, status, created_at) 
                VALUES ('$title', '$author', '$description', '$price', '$condition', '$user_id', 'available', NOW())";
        
        return executeQuery($sql);
    }
    
    // Update book status
    public static function updateBookStatus($id, $status) {
        $id = escapeString($id);
        $status = escapeString($status);
        
        $sql = "UPDATE books SET status = '$status' WHERE id = '$id'";
        return executeQuery($sql);
    }
    
    // Delete book
    public static function deleteBook($id) {
        $id = escapeString($id);
        $sql = "DELETE FROM books WHERE id = '$id'";
        return executeQuery($sql);
    }
    
    // Get books by user
    public static function getBooksByUser($user_id) {
        $user_id = escapeString($user_id);
        $sql = "SELECT * FROM books WHERE user_id = '$user_id'";
        return fetchAll($sql);
    }
}
?>