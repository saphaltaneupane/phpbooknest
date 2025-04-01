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

// Function to execute queries
function executeQuery($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Function to fetch results as associative array
function fetchAll($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

// Function to fetch a single row
function fetchOne($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Function to escape strings for SQL safety
function escapeString($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// Function to get the last inserted id
function getLastInsertId() {
    global $conn;
    return mysqli_insert_id($conn);
}
?>