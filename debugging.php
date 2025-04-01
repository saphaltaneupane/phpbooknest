<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<h1>User Login Debugging</h1>";

// Get a user from the database
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

echo "<h2>All users in database:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Username</th><th>Password (Hashed)</th><th>Email</th><th>Role</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['username'] . "</td>";
    echo "<td>" . htmlspecialchars($row['password']) . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}

echo "</table>";

// Test form for login
echo "<h2>Test Login</h2>";
echo "<form method='POST'>";
echo "Username: <input type='text' name='username'><br>";
echo "Password: <input type='password' name='password'><br>";
echo "<input type='submit' value='Test Login'>";
echo "</form>";

// Process login test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "<h3>Login attempt:</h3>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    
    // Get user
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) {
        echo "User found in database<br>";
        echo "Stored password hash: " . htmlspecialchars($user['password']) . "<br>";
        
        // Test password_verify
        $verify_result = password_verify($password, $user['password']);
        echo "password_verify result: " . ($verify_result ? "TRUE (Password is correct)" : "FALSE (Password is incorrect)") . "<br>";
        
        // Create a new hash of the entered password for comparison
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        echo "New hash of entered password: " . htmlspecialchars($new_hash) . "<br>";
        
        // Test direct string comparison (not secure, just for debugging)
        echo "Direct string comparison: " . (($password === $user['password']) ? "TRUE" : "FALSE") . "<br>";
        
        // Create known test password hash
        $test_hash = password_hash("test123", PASSWORD_DEFAULT);
        echo "Test hash for 'test123': " . htmlspecialchars($test_hash) . "<br>";
        echo "Password_verify for 'test123': " . (password_verify("test123", $test_hash) ? "TRUE" : "FALSE") . "<br>";
    } else {
        echo "User not found in database<br>";
    }
}

// Close the connection
mysqli_close($conn);
?>