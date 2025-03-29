<?php
class Book {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function addBook($title, $author, $description, $price, $genre, $condition, $seller_id, $is_used = false, $image = 'default_book.jpg') {
        $query = "INSERT INTO books (title, author, description, price, genre, condition, seller_id, is_used, image) 
                  VALUES ('$title', '$author', '$description', $price, '$genre', '$condition', $seller_id, $is_used, '$image')";
        
        if (mysqli_query($this->conn, $query)) {
            return mysqli_insert_id($this->conn);
        } else {
            return false;
        }
    }
    
    public function getBookById($id) {
        $query = "SELECT b.*, u.username as seller_name 
                  FROM books b 
                  LEFT JOIN users u ON b.seller_id = u.id 
                  WHERE b.id = $id";
        $result = mysqli_query($this->conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }
    
    public function getAllBooks($limit = 0, $offset = 0) {
        $limitQuery = ($limit > 0) ? "LIMIT $offset, $limit" : "";
        $query = "SELECT b.*, u.username as seller_name 
                  FROM books b 
                  LEFT JOIN users u ON b.seller_id = u.id 
                  WHERE b.status = 'Available' 
                  ORDER BY b.created_at DESC 
                  $limitQuery";
                  
        $result = mysqli_query($this->conn, $query);
        $books = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    public function getAllAdminBooks() {
        $query = "SELECT b.*, u.username as seller_name 
                  FROM books b 
                  LEFT JOIN users u ON b.seller_id = u.id 
                  ORDER BY b.created_at DESC";
                  
        $result = mysqli_query($this->conn, $query);
        $books = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    public function getBooksByUserId($user_id) {
        $query = "SELECT * FROM books WHERE seller_id = $user_id ORDER BY created_at DESC";
        $result = mysqli_query($this->conn, $query);
        $books = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    public function searchBooks($keyword, $genre = '') {
        $genreFilter = ($genre) ? "AND genre LIKE '%$genre%'" : "";
        $query = "SELECT b.*, u.username as seller_name 
                  FROM books b 
                  LEFT JOIN users u ON b.seller_id = u.id 
                  WHERE (b.title LIKE '%$keyword%' OR b.author LIKE '%$keyword%' OR b.description LIKE '%$keyword%') 
                  $genreFilter 
                  AND b.status = 'Available' 
                  ORDER BY b.created_at DESC";
                  
        $result = mysqli_query($this->conn, $query);
        $books = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    public function updateBook($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = '$value', ";
        }
        $fields = rtrim($fields, ', ');
        
        $query = "UPDATE books SET $fields WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }
    
    public function deleteBook($id) {
        $query = "DELETE FROM books WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }
    
    public function getGenres() {
        $query = "SELECT DISTINCT genre FROM books";
        $result = mysqli_query($this->conn, $query);
        $genres = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $genreList = explode(',', $row['genre']);
            foreach ($genreList as $g) {
                $g = trim($g);
                if (!in_array($g, $genres) && !empty($g)) {
                    $genres[] = $g;
                }
            }
        }
        
        sort($genres);
        return $genres;
    }
    
    public function buyBook($book_id, $buyer_id) {
        // Get book details to find seller
        $book = $this->getBookById($book_id);
        if (!$book) return false;
        
        $seller_id = $book['seller_id'];
        
        // Begin transaction
        mysqli_begin_transaction($this->conn);
        
        try {
            // Update book status
            $updateBook = "UPDATE books SET status = 'Sold' WHERE id = $book_id";
            mysqli_query($this->conn, $updateBook);
            
            // Create order record
            $createOrder = "INSERT INTO orders (book_id, buyer_id, seller_id, status) 
                           VALUES ($book_id, $buyer_id, $seller_id, 'Completed')";
            mysqli_query($this->conn, $createOrder);
            
            // Commit transaction
            mysqli_commit($this->conn);
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($this->conn);
            return false;
        }
    }
    
    public function getPurchaseHistory($user_id) {
        $query = "SELECT o.*, b.title, b.price, b.image, u.username as seller_name 
                  FROM orders o 
                  JOIN books b ON o.book_id = b.id 
                  JOIN users u ON o.seller_id = u.id 
                  WHERE o.buyer_id = $user_id 
                  ORDER BY o.order_date DESC";
                  
        $result = mysqli_query($this->conn, $query);
        $purchases = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $purchases[] = $row;
        }
        
        return $purchases;
    }
}
?>