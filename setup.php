<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select the database
mysqli_select_db($conn, DB_NAME);

// Drop tables if they exist (for clean setup)
$sql = "DROP TABLE IF EXISTS orders, books, users";
if (mysqli_query($conn, $sql)) {
    echo "Old tables dropped successfully<br>";
} else {
    echo "Error dropping tables: " . mysqli_error($conn) . "<br>";
}

// Create users table
$sql = "CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Create books table
$sql = "CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    `condition` ENUM('new', 'old') DEFAULT 'new',
    user_id INT,
    status ENUM('available', 'sold') DEFAULT 'available',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)";

if (mysqli_query($conn, $sql)) {
    echo "Books table created successfully<br>";
} else {
    echo "Error creating books table: " . mysqli_error($conn) . "<br>";
}

// Create orders table
$sql = "CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    payment_method ENUM('cash', 'khalti') DEFAULT 'cash',
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "Orders table created successfully<br>";
} else {
    echo "Error creating orders table: " . mysqli_error($conn) . "<br>";
}

// Create admin user with password: admin123
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, password, email, role) VALUES 
        (?, ?, 'admin@bookstore.com', 'admin')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $admin_username, $admin_password);
$admin_username = "admin";
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Admin user created successfully<br>";
    echo "Admin username: admin<br>";
    echo "Admin password: admin123<br>";
    echo "Admin password hash: " . htmlspecialchars($admin_password) . "<br>";
} else {
    echo "Error creating admin user: " . mysqli_error($conn) . "<br>";
}

// Create regular user with password: user123
$user_password = password_hash('user123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, password, email, role) VALUES 
        (?, ?, 'user@example.com', 'user')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $user_username, $user_password);
$user_username = "user";
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Regular user created successfully<br>";
    echo "User username: user<br>";
    echo "User password: user123<br>";
    echo "User password hash: " . htmlspecialchars($user_password) . "<br>";
} else {
    echo "Error creating regular user: " . mysqli_error($conn) . "<br>";
}

// Get user ID for the regular user
$sql = "SELECT id FROM users WHERE username = 'user'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$user_id = $user['id'];

// Insert sample books
$sql = "INSERT INTO books (title, author, description, price, `condition`, user_id, status) VALUES
        ('The Great Gatsby', 'F. Scott Fitzgerald', 'A classic novel about the American Dream.', 1200.00, 'new', NULL, 'available'),
        ('To Kill a Mockingbird', 'Harper Lee', 'A powerful story about racial injustice.', 980.00, 'new', NULL, 'available'),
        ('1984', 'George Orwell', 'A dystopian novel about surveillance and control.', 850.00, 'new', NULL, 'available'),
        ('Pride and Prejudice', 'Jane Austen', 'A romantic novel about manners and marriage.', 750.00, 'old', ?, 'available'),
        ('The Hobbit', 'J.R.R. Tolkien', 'A fantasy novel about a hobbit who goes on an adventure.', 1100.00, 'old', ?, 'available')";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    echo "Sample books added successfully<br>";
} else {
    echo "Error adding sample books: " . mysqli_error($conn) . "<br>";
}

echo "<br><hr>";
echo "<h2>Login Instructions:</h2>";
echo "<strong>Admin Login:</strong><br>";
echo "Username: admin<br>";
echo "Password: admin123<br><br>";
echo "<strong>User Login:</strong><br>";
echo "Username: user<br>";
echo "Password: user123<br><br>";
echo "<hr>";
echo "<a href='index.php'>Go to the application</a> | <a href='debugging.php'>Debug login issues</a>";

// Close the connection
mysqli_close($conn);
?>