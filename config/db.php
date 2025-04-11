
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'booktrading';
// Khalti API Keys
define('KHALTI_SECRET_KEY', 'b42d1cff70d84d759d823a75f0ac17d5'); // Your live secret key
define('KHALTI_PUBLIC_KEY', 'c6e784a644ca4f3bbe85d89b25213fd1'); // Your live public key
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}