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

/**
 * Function to get book by ID
 * 
 * @param int $bookId The ID of the book.
 * @return array|false Book data or false if not found.
 */
function getBookById($bookId) {
    global $conn;
    
    // Include join with users table to get user info for admin books
    $query = "SELECT books.*, users.name as added_by_name, users.is_admin as added_by_is_admin 
              FROM books 
              LEFT JOIN users ON books.added_by = users.id 
              WHERE books.id = '$bookId'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Function to get top rated books (using priority queue concept).
 */
function getTopRatedBooks($limit = 5) {
    global $conn;

    // Query to fetch books with their average rating and rating count
    // Modified to include admin books regardless of quantity
    $query = "SELECT books.*, users.is_admin, AVG(ratings.rating) as avg_rating, 
              COUNT(ratings.id) as rating_count 
              FROM books 
              JOIN ratings ON books.id = ratings.book_id 
              LEFT JOIN users ON books.added_by = users.id
              WHERE (books.quantity > 0) OR (users.is_admin = 1 OR books.added_by IS NULL)
              GROUP BY books.id
              HAVING avg_rating >= 4"; 

    $result = mysqli_query($conn, $query);

    // Use SplPriorityQueue to store books based on avg_rating and rating_count
    $priorityQueue = new SplPriorityQueue();

    while ($row = mysqli_fetch_assoc($result)) {
        // Use avg_rating as the primary priority
        $priority = $row['avg_rating']; 
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
 * Get available books - includes all admin books and user books with quantity > 0.
 */
function getAvailableBooks() {
    global $conn;
    // Join with users table to get added_by info and is_admin status
    // Modified query to include admin books regardless of quantity
    $query = "SELECT books.*, users.name as added_by_name, users.is_admin as added_by_is_admin FROM books 
              LEFT JOIN users ON books.added_by = users.id 
              WHERE (books.quantity > 0) OR (users.is_admin = 1 OR books.added_by IS NULL)";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

/**
 * Update book quantity after confirmed purchase.
 * 
 * @param int $bookId The ID of the book.
 * @param int $orderQuantity The quantity being ordered.
 * @return bool Success status.
 */
function updateBookQuantity($bookId, $orderQuantity = 1) {
    global $conn;
    
    // Log the function call for debugging
    error_log("Updating book quantity: Book ID=$bookId, Quantity=$orderQuantity");
    
    // Get current book information
    $query = "SELECT quantity, status FROM books WHERE id = $bookId";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query error in updateBookQuantity: " . mysqli_error($conn));
        return false;
    }
    
    if (mysqli_num_rows($result) === 0) {
        error_log("Book ID $bookId not found");
        return false;
    }
    
    $book = mysqli_fetch_assoc($result);
    $currentQuantity = (int)$book['quantity'];
    
    // Calculate new quantity
    $newQuantity = $currentQuantity - $orderQuantity;
    
    // Prevent negative quantity
    if ($newQuantity < 0) {
        $newQuantity = 0;
    }
    
    // Determine new status
    $newStatus = $newQuantity > 0 ? 'available' : 'sold';
    
    // Update book quantity and status
    $updateQuery = "UPDATE books SET quantity = $newQuantity, status = '$newStatus' WHERE id = $bookId";
    $updateResult = mysqli_query($conn, $updateQuery);
    
    if (!$updateResult) {
        error_log("Update error in updateBookQuantity: " . mysqli_error($conn));
    }
    
    return $updateResult;
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
 * Function to get book rating
 */
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

/**
 * Check if a user has purchased a book with completed status.
 * 
 * @param int $userId The ID of the user.
 * @param int $bookId The ID of the book.
 * @return bool True if the user has purchased the book with completed status, false otherwise.
 */
function hasCompletedPurchase($userId, $bookId) {
    global $conn;
    
    $query = "SELECT o.id FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = $userId 
              AND oi.book_id = $bookId 
              AND o.payment_status = 'completed' 
              AND o.status = 'completed'";
    
    $result = mysqli_query($conn, $query);
    
    return mysqli_num_rows($result) > 0;
}
?>