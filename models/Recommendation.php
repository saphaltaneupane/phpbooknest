<?php
class Recommendation {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getRecommendationsForUser($user_id, $limit = 5) {
        // Get books the user has purchased
        $query = "SELECT b.genre FROM orders o
                  JOIN books b ON o.book_id = b.id
                  WHERE o.buyer_id = $user_id";
                  
        $result = mysqli_query($this->conn, $query);
        
        if (mysqli_num_rows($result) == 0) {
            // If user hasn't purchased anything, return some popular books
            return $this->getPopularBooks($limit);
        }
        
        // Extract all genres from user's purchased books
        $userGenres = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $genres = explode(',', $row['genre']);
            foreach ($genres as $genre) {
                $genre = trim($genre);
                if (!in_array($genre, $userGenres) && !empty($genre)) {
                    $userGenres[] = $genre;
                }
            }
        }
        
        // Find books with similar genres that user hasn't purchased yet
        $recommendedBooks = [];
        
        // Get all available books
        $booksQuery = "SELECT b.*, u.username as seller_name,
                      (SELECT COUNT(*) FROM orders WHERE book_id = b.id) as order_count
                      FROM books b
                      LEFT JOIN users u ON b.seller_id = u.id
                      WHERE b.status = 'Available'
                      AND b.id NOT IN (
                          SELECT book_id FROM orders WHERE buyer_id = $user_id
                      )
                      ORDER BY b.created_at DESC";
        
        $booksResult = mysqli_query($this->conn, $booksQuery);
        
        while ($book = mysqli_fetch_assoc($booksResult)) {
            $bookGenres = explode(',', $book['genre']);
            $score = 0;
            
            // Count matching genres (frequency count)
            foreach ($bookGenres as $genre) {
                $genre = trim($genre);
                if (in_array($genre, $userGenres)) {
                    $score++;
                }
            }
            
            if ($score > 0) {
                $book['score'] = $score;
                $recommendedBooks[] = $book;
            }
        }
        
        // Sort by score descending
        usort($recommendedBooks, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Return limited number of recommendations
        return array_slice($recommendedBooks, 0, $limit);
    }
    
    private function getPopularBooks($limit = 5) {
        $query = "SELECT b.*, u.username as seller_name,
                  (SELECT COUNT(*) FROM orders WHERE book_id = b.id) as order_count
                  FROM books b
                  LEFT JOIN users u ON b.seller_id = u.id
                  WHERE b.status = 'Available'
                  ORDER BY order_count DESC, b.created_at DESC
                  LIMIT $limit";
                  
        $result = mysqli_query($this->conn, $query);
        $books = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    public function getRecommendationsBasedOnBook($book_id, $limit = 5) {
        // Get the genres of the selected book
        $query = "SELECT genre FROM books WHERE id = $book_id";
        $result = mysqli_query($this->conn, $query);
        
        if (mysqli_num_rows($result) == 0) {
            return [];
        }
        
        $row = mysqli_fetch_assoc($result);
        $selectedBookGenres = explode(',', $row['genre']);
        
        // Find books with similar genres
        $recommendedBooks = [];
        
        // Get all available books except the current one
        $booksQuery = "SELECT b.*, u.username as seller_name
                      FROM books b
                      LEFT JOIN users u ON b.seller_id = u.id
                      WHERE b.status = 'Available'
                      AND b.id != $book_id
                      ORDER BY b.created_at DESC";
        
        $booksResult = mysqli_query($this->conn, $booksQuery);
        
        while ($book = mysqli_fetch_assoc($booksResult)) {
            $bookGenres = explode(',', $book['genre']);
            $score = 0;
            
            // Count matching genres (frequency count)
            foreach ($bookGenres as $genre) {
                $genre = trim($genre);
                if (in_array($genre, $selectedBookGenres)) {
                    $score++;
                }
            }
            
            if ($score > 0) {
                $book['score'] = $score;
                $recommendedBooks[] = $book;
            }
        }
        
        // Sort by score descending
        usort($recommendedBooks, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        // Return limited number of recommendations
        return array_slice($recommendedBooks, 0, $limit);
    }
}
?>