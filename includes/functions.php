<?php
// Start session if not already started
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

// Function to get top rated books (using priority queue concept)
function getTopRatedBooks($limit = 5) {
    global $conn;
    
    // Get books with average rating >= 4
    $query = "SELECT books.*, AVG(ratings.rating) as avg_rating, COUNT(ratings.id) as rating_count 
              FROM books 
              JOIN ratings ON books.id = ratings.book_id 
              WHERE books.status = 'available' 
              GROUP BY books.id 
              HAVING avg_rating >= 4 
              ORDER BY avg_rating DESC, rating_count DESC 
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
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

// Function to get available books
function getAvailableBooks() {
    global $conn;
    $query = "SELECT * FROM books WHERE status = 'available'";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
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
?>