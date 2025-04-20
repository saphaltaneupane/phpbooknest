<?php
// Ensure no output is sent before this point
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if logged in user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Function to redirect
function redirect($url) {
    // Ensure no output is sent before calling header()
    if (headers_sent()) {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
    header("Location: $url");
    exit();
}

// Function to display error message
function displayError($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

// Function to display success message
function displaySuccess($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// Function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number (10 digits)
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to get book by ID
function getBookById($bookId) {
    global $conn;
    $query = "SELECT * FROM books WHERE id = '$bookId'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Function to get top rated books (using priority queue concept).
 */
function getTopRatedBooks($limit = 5) {
    global $conn;

    // Query to fetch books with their average rating and rating count
    // Ensure only books with avg_rating >= 4 are included
    $query = "SELECT books.*, AVG(ratings.rating) as avg_rating, COUNT(ratings.id) as rating_count 
              FROM books 
              JOIN ratings ON books.id = ratings.book_id 
              WHERE books.quantity > 0 
              GROUP BY books.id
              HAVING avg_rating >= 4"; // Include books with avg_rating equal to or greater than 4

    $result = mysqli_query($conn, $query);

    // Use SplPriorityQueue to store books based on avg_rating and rating_count
    $priorityQueue = new SplPriorityQueue();

    while ($row = mysqli_fetch_assoc($result)) {
        // Use avg_rating as the primary priority
        // SplPriorityQueue processes items with higher priority values first
        $priority = $row['avg_rating']; // Use avg_rating directly for prioritization
        $priorityQueue->insert($row, $priority);
    }

    // Extract top-rated books
    $priorityQueue->setExtractFlags(SplPriorityQueue::EXTR_DATA);
    $books = [];
    $count = 0;

    while (!$priorityQueue->isEmpty() && $count < $limit) {
        $books[] = $priorityQueue->extract();
        $count++;
    }

    return $books;
}

// Function to get user by ID
function getUserById($userId) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = '$userId'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get all users (for admin)
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM users WHERE is_admin = 0";
    $result = mysqli_query($conn, $query);
    $users = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

// Function to get all books
function getAllBooks() {
    global $conn;
    $query = "SELECT books.*, users.name as added_by_name FROM books LEFT JOIN users ON books.added_by = users.id";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

/**
 * Get available books with quantity > 0.
 */
function getAvailableBooks() {
    global $conn;
    // Join with users table to get added_by info and is_admin status
    // Note: We only check for quantity > 0, not the status
    $query = "SELECT books.*, users.name as added_by_name, users.is_admin as added_by_is_admin FROM books 
              LEFT JOIN users ON books.added_by = users.id 
              WHERE books.quantity > 0";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

/**
 * Decrement the quantity of books in an order.
 * If quantity reaches 0 or less, update book status to 'sold'.
 * 
 * @param int $orderId The ID of the order.
 */
function decrementBookQuantity($orderId) {
    global $conn;
    $query = "SELECT book_id, quantity FROM order_items WHERE order_id = $orderId";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $bookId = $row['book_id'];
        $orderQuantity = $row['quantity'] ?? 1;
        
        // Get current quantity
        $qtyQuery = "SELECT quantity FROM books WHERE id = $bookId";
        $qtyResult = mysqli_query($conn, $qtyQuery);
        $book = mysqli_fetch_assoc($qtyResult);
        
        if ($book) {
            $currentQuantity = (int)$book['quantity'];
            
            // Calculate new quantity
            $newQuantity = $currentQuantity - $orderQuantity;
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }
            
            // Update quantity and status if needed
            if ($newQuantity == 0) {
                $updateQuery = "UPDATE books SET quantity = 0, status = 'sold' WHERE id = $bookId";
            } else {
                $updateQuery = "UPDATE books SET quantity = $newQuantity WHERE id = $bookId";
            }
            
            mysqli_query($conn, $updateQuery);
        }
    }
}

/**
 * Update book quantity immediately when an order is placed.
 * If quantity reaches 0, update book status to 'sold'.
 * 
 * @param int $bookId The ID of the book.
 * @param int $orderQuantity The quantity being ordered.
 * @return bool Success status.
 */
function updateBookQuantity($bookId, $orderQuantity = 1) {
    global $conn;
    
    // Get current book information including added_by and status
    $query = "SELECT quantity, added_by, status FROM books WHERE id = $bookId";
    $result = mysqli_query($conn, $query);
    
    if ($result && $book = mysqli_fetch_assoc($result)) {
        $currentQuantity = (int)$book['quantity'];
        $addedBy = $book['added_by'];
        $currentStatus = $book['status'];
        
        // Calculate new quantity
        $newQuantity = $currentQuantity - $orderQuantity;
        
        // Prevent negative quantity
        if ($newQuantity < 0) {
            $newQuantity = 0;
        }
        
        // Determine new status
        $newStatus = $currentStatus;
        
        if ($newQuantity == 0) {
            // If quantity is 0, mark as sold
            $newStatus = 'sold';
        } else {
            // If quantity > 0, always set status to available
            $newStatus = 'available';
        }
        
        // Update book quantity and status
        $updateQuery = "UPDATE books SET quantity = $newQuantity, status = '$newStatus' WHERE id = $bookId";
        
        return mysqli_query($conn, $updateQuery);
    }
    
    return false;
}

// Function to get book rating
function getBookRating($bookId) {
    global $conn;
    $query = "SELECT AVG(rating) as avg_rating FROM ratings WHERE book_id = '$bookId'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return round($row['avg_rating'], 1);
}

// Function to search books
function searchBooks($keyword) {
    global $conn;
    $keyword = sanitize($keyword);
    $query = "SELECT * FROM books WHERE status = 'available' AND (title LIKE '%$keyword%' OR author LIKE '%$keyword%')";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

// Function to get user orders
function getUserOrders($userId) {
    global $conn;
    $query = "SELECT * FROM orders WHERE user_id = '$userId' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $orders = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    return $orders;
}

// Function to get order items
function getOrderItems($orderId) {
    global $conn;
    $query = "SELECT oi.*, b.title, b.author FROM order_items oi 
              JOIN books b ON oi.book_id = b.id 
              WHERE oi.order_id = '$orderId'";
    $result = mysqli_query($conn, $query);
    $items = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

/**
 * Update book status to 'sold' when the order is completed.
 * 
 * @param int $orderId The ID of the order.
 */
function updateBookStatusToSold($orderId) {
    global $conn;
    $query = "SELECT book_id FROM order_items WHERE order_id = $orderId";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $bookId = $row['book_id'];
        // Check quantity before updating status
        $qtyQuery = "SELECT quantity FROM books WHERE id = $bookId";
        $qtyResult = mysqli_query($conn, $qtyQuery);
        $book = mysqli_fetch_assoc($qtyResult);
        $quantity = $book ? (int)$book['quantity'] : 0;
        if ($quantity == 0) {
            $updateQuery = "UPDATE books SET status = 'sold' WHERE id = $bookId";
            mysqli_query($conn, $updateQuery);
        }
    }
}
?>