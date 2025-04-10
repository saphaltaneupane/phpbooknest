<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get book ID and action from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Get book details
$book = getBookById($bookId);

// If book not found, redirect to books page
if (!$book) {
    redirect('books.php');
}

// Process the action
if ($action === 'approve') {
    // Update book status to available
    $query = "UPDATE books SET status = 'available' WHERE id = $bookId";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = 'Book approved and now available for purchase!';
    } else {
        $_SESSION['error_message'] = 'Error approving book: ' . mysqli_error($conn);
    }
} elseif ($action === 'reject') {
    // Delete the book
    $query = "DELETE FROM books WHERE id = $bookId";
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = 'Book rejected and removed from the system.';
    } else {
        $_SESSION['error_message'] = 'Error rejecting book: ' . mysqli_error($conn);
    }
} else {
    $_SESSION['error_message'] = 'Invalid action.';
}

// Redirect back to books page
redirect('books.php');
?>