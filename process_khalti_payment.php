<?php
// Include only the files that exist
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define Khalti lookup URL if not already defined
if (!defined('KHALTI_LOOKUP_URL')) {
    define('KHALTI_LOOKUP_URL', 'https://a.khalti.com/api/v2/epayment/lookup/');
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a log file for easier debugging
ini_set('log_errors', 1);
ini_set('error_log', 'payment_errors.log');

// Log request data for debugging
error_log("Khalti callback received: " . json_encode($_GET));
error_log("Session data: " . json_encode(isset($_SESSION['khalti_payment']) ? $_SESSION['khalti_payment'] : 'Not set'));

// Function to handle failures and prevent redirect loops
function handleFailure($orderId, $message) {
    // Clear session payment data
    if (isset($_SESSION['khalti_payment'])) {
        unset($_SESSION['khalti_payment']);
    }
    
    // Display error and provide links
    echo "<html><body>";
    echo "<h1>Payment Processing Error</h1>";
    echo "<p>" . htmlspecialchars($message) . "</p>";
    echo "<p>Please try one of the following options:</p>";
    echo "<ul>";
    echo "<li><a href='payment_success.php?order_id={$orderId}&status=failed&message=" . urlencode($message) . "'>View Order Details</a></li>";
    echo "<li><a href='khalti_payment.php?retry={$orderId}'>Try Payment Again</a></li>";
    echo "<li><a href='index.php'>Return to Homepage</a></li>";
    echo "</ul>";
    echo "</body></html>";
    exit;
}

// Check if there's a pidx parameter (from Khalti callback)
if (!isset($_GET['pidx'])) {
    error_log("No pidx parameter found in callback");
    
    // Check if we have order data in session
    if (isset($_SESSION['khalti_payment']) && isset($_SESSION['khalti_payment']['order_id'])) {
        $orderId = $_SESSION['khalti_payment']['order_id'];
        handleFailure($orderId, "Missing payment verification data");
    } else {
        // No session data - redirect to home
        header("Location: index.php");
        exit;
    }
}

// Get the pidx parameter
$pidx = $_GET['pidx'];
error_log("Processing payment with pidx: $pidx");

// Check if we have payment data in session
if (!isset($_SESSION['khalti_payment']) || !isset($_SESSION['khalti_payment']['order_id'])) {
    error_log("No payment data in session");
    
    // Try to find the order by pidx in the database
    $pidxLike = '%' . mysqli_real_escape_string($conn, $pidx) . '%';
    $query = "SELECT id FROM orders WHERE purchase_order_id LIKE '$pidxLike' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $orderId = $row['id'];
        error_log("Found order ID: $orderId based on pidx");
        handleFailure($orderId, "Session data lost - please try again");
    } else {
        // Can't find the order - redirect to homepage
        header("Location: index.php");
        exit;
    }
}

// Get the order ID from session
$orderId = $_SESSION['khalti_payment']['order_id'];
error_log("Processing payment for order: $orderId");

// Prepare verification request
$data = ['pidx' => $pidx];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, KHALTI_LOOKUP_URL);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . KHALTI_SECRET_KEY,
    'Content-Type: application/json'
]);

// Execute cURL request
$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    error_log("cURL Error: $error");
    curl_close($ch);
    handleFailure($orderId, "Connection error: $error");
}

// Close cURL
curl_close($ch);

// Log the response
error_log("Khalti Verification Response (HTTP $statusCode): $response");

// Parse the response
$responseData = json_decode($response, true);

// Store the response for debugging
$_SESSION['khalti_response'] = $responseData;

// Check if payment was successful
if ($statusCode === 200 && isset($responseData['status']) && $responseData['status'] === 'Completed') {
    // Payment successful
    $transactionId = isset($responseData['transaction_id']) ? $responseData['transaction_id'] : $pidx;
    error_log("Payment successful. Transaction ID: $transactionId");
    
    // Update order status
    $query = "UPDATE orders SET payment_status = 'completed', transaction_id = '$transactionId' WHERE id = $orderId";
    
    if (mysqli_query($conn, $query)) {
        error_log("Order status updated successfully");
        
        // Update book inventory
        $itemsQuery = "SELECT book_id, quantity FROM order_items WHERE order_id = $orderId";
        $itemsResult = mysqli_query($conn, $itemsQuery);
        
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $bookId = $item['book_id'];
            $quantity = $item['quantity'];
            
            // Get current book data
            $bookQuery = "SELECT quantity FROM books WHERE id = $bookId";
            $bookResult = mysqli_query($conn, $bookQuery);
            
            if ($bookResult && $book = mysqli_fetch_assoc($bookResult)) {
                $currentQty = (int)$book['quantity'];
                $newQty = max(0, $currentQty - $quantity);
                $newStatus = ($newQty > 0) ? 'available' : 'sold';
                
                // Update book quantity
                $updateQuery = "UPDATE books SET quantity = $newQty, status = '$newStatus' WHERE id = $bookId";
                mysqli_query($conn, $updateQuery);
                error_log("Updated book #$bookId inventory. New quantity: $newQty");
            }
        }
        
        // Clear payment session data
        unset($_SESSION['khalti_payment']);
        
        // Redirect to homepage
        header("Location: index.php?payment_status=success");
        exit;
    } else {
        // Database error
        $dbError = mysqli_error($conn);
        error_log("Database error: $dbError");
        handleFailure($orderId, "Database error: $dbError");
    }
} else {
    // Payment was not successful
    $status = isset($responseData['status']) ? $responseData['status'] : 'Unknown';
    error_log("Payment verification failed. Status: $status");
    handleFailure($orderId, "Payment status: $status");
}
?>