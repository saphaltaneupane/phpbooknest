<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete the book
$query = "DELETE FROM books WHERE id = $bookId";
if (mysqli_query($conn, $query)) {
    $_SESSION['success_message'] = 'Book deleted successfully!';
} else {
    $_SESSION['error_message'] = 'Error deleting book: ' . mysqli_error($conn);
}

// Redirect back to books page
redirect('books.php');
?>