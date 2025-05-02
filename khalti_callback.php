<?php
// Session configuration must come before any output or session initialization
$sessionStarted = (session_status() == PHP_SESSION_ACTIVE);
if (!$sessionStarted) {
    // Only set these if session hasn't started yet
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.gc_maxlifetime', 3600);
    session_start();
}

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'khalti_callback.log');

// Force content type
header('Content-Type: text/html; charset=utf-8');

// Get database connection and functions
require_once 'config/db.php';
require_once 'includes/functions.php';

// Log request data for debugging
error_log("Khalti callback received: " . json_encode($_GET));
error_log("Session state: " . (isset($_SESSION['khalti_payment']) ? json_encode($_SESSION['khalti_payment']) : "Not set"));

// Check if pidx parameter exists
if (!isset($_GET['pidx'])) {
    // Handle missing pidx parameter
    $orderId = isset($_SESSION['khalti_payment']['order_id']) ? $_SESSION['khalti_payment']['order_id'] : 0;
    
    if ($orderId > 0) {
        // Clear session data
        unset($_SESSION['khalti_payment']);
        
        // Redirect to payment failure page
        redirect("payment_success.php?order_id=$orderId&status=failed&message=" . urlencode("Missing payment verification data"));
    } else {
        // Clear session and redirect to home
        session_unset();
        redirect("index.php");
    }
    exit;
}

// Extract the pidx
$pidx = $_GET['pidx'];
error_log("Processing payment with pidx: $pidx");

// Check if we have payment data in session
if (!isset($_SESSION['khalti_payment']) || !isset($_SESSION['khalti_payment']['order_id'])) {
    error_log("No payment data in session. Attempting to find order by pidx.");
    
    // Try to find order by pidx
    $pidxEscaped = mysqli_real_escape_string($conn, $pidx);
    $query = "SELECT id FROM orders WHERE purchase_order_id LIKE '%$pidxEscaped%' OR purchase_order_id = '$pidxEscaped' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        // Found order by pidx
        $row = mysqli_fetch_assoc($result);
        $orderId = $row['id'];
        error_log("Found order $orderId from database using pidx");
    } else {
        // No order found - redirect to home
        session_unset();
        redirect("index.php");
        exit;
    }
} else {
    // Get order ID from session
    $orderId = $_SESSION['khalti_payment']['order_id'];
    error_log("Found order_id in session: $orderId");
}

// Verify payment with Khalti
$secretKey = KHALTI_SECRET_KEY;
$verifyUrl = "https://a.khalti.com/api/v2/epayment/lookup/";

// Set up cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $verifyUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['pidx' => $pidx]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . $secretKey,
    'Content-Type: application/json'
]);

// Execute request
$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    error_log("cURL Error: $error");
    curl_close($ch);
    
    // Clear session data
    unset($_SESSION['khalti_payment']);
    
    // Redirect to payment failure page
    redirect("payment_success.php?order_id=$orderId&status=failed&message=" . urlencode("Connection error: $error"));
    exit;
}

// Close cURL
curl_close($ch);

// Log the response
error_log("Khalti Verification Response (HTTP $statusCode): $response");
$responseData = json_decode($response, true);

// Check payment status
if ($statusCode === 200 && isset($responseData['status']) && $responseData['status'] === 'Completed') {
    // Payment successful
    $transactionId = isset($responseData['transaction_id']) ? $responseData['transaction_id'] : $pidx;
    error_log("Payment successful. Transaction ID: $transactionId");
    
    // Update order status
    $transactionId = mysqli_real_escape_string($conn, $transactionId);
    $query = "UPDATE orders SET payment_status = 'completed', transaction_id = '$transactionId' WHERE id = $orderId";
    
    if (mysqli_query($conn, $query)) {
        error_log("Order status updated successfully");
        
        // NOW Update book inventory (since we only do this for completed Khalti payments)
        $itemsQuery = "SELECT book_id, quantity FROM order_items WHERE order_id = $orderId";
        $itemsResult = mysqli_query($conn, $itemsQuery);
        
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $bookId = $item['book_id'];
            $quantity = $item['quantity'];
            
            // Update book inventory using our function
            updateBookQuantity($bookId, $quantity);
            error_log("Updated book #$bookId inventory after payment verification");
        }
        
        // Clear session data
        unset($_SESSION['khalti_payment']);
        
        // Redirect to success page
        redirect("payment_success.php?order_id=$orderId&status=success");
    } else {
        // Database error
        $dbError = mysqli_error($conn);
        error_log("Database error: $dbError");
        
        // Clear session data
        unset($_SESSION['khalti_payment']);
        
        // Redirect to failure page
        redirect("payment_success.php?order_id=$orderId&status=failed&message=" . urlencode("Database error: $dbError"));
    }
} else {
    // Payment failed
    $status = isset($responseData['status']) ? $responseData['status'] : 'Unknown';
    error_log("Payment verification failed. Status: $status");
    
    // Clear session data
    unset($_SESSION['khalti_payment']);
    
    // Redirect to failure page
    redirect("payment_success.php?order_id=$orderId&status=failed&message=" . urlencode("Payment status: $status"));
}
?>