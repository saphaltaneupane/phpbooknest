<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check if form data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
    $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $purchaseOrderId = isset($_POST['purchase_order_id']) ? sanitize($_POST['purchase_order_id']) : '';
    $purchaseOrderName = isset($_POST['purchase_order_name']) ? sanitize($_POST['purchase_order_name']) : '';
    
    // Get customer info
    $customerName = isset($_POST['customer_name']) ? sanitize($_POST['customer_name']) : '';
    $customerEmail = isset($_POST['customer_email']) ? sanitize($_POST['customer_email']) : '';
    $customerPhone = isset($_POST['customer_phone']) ? sanitize($_POST['customer_phone']) : '';
    
    // Validate data
    if ($orderId <= 0 || $bookId <= 0 || $amount <= 0 || empty($purchaseOrderId) || empty($purchaseOrderName)) {
        die("Invalid payment data");
    }
    
    // Convert amount to paisa (Khalti uses paisa)
    $amountInPaisa = (int)($amount * 100);
    
    // Base URL for the site (adjust according to your setup)
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    $baseUrl = rtrim(dirname($baseUrl . $_SERVER['PHP_SELF']), '/');
    
    // Return URL after payment
    $returnUrl = "$baseUrl/process_khalti_payment.php";
    
    // Website URL
    $websiteUrl = "$baseUrl/index.php";
    
    // Khalti API URL for initiating payment
    $url = "https://dev.khalti.com/api/v2/epayment/initiate/";
    
    // Prepare payload for Khalti
    $payload = [
        "return_url" => $returnUrl,
        "website_url" => $websiteUrl,
        "amount" => $amountInPaisa,
        "purchase_order_id" => $purchaseOrderId,
        "purchase_order_name" => $purchaseOrderName,
        "customer_info" => [
            "name" => $customerName,
            "email" => $customerEmail,
            "phone" => $customerPhone
        ]
    ];
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ]);
    
    // Execute cURL
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }
    
    // Close cURL
    curl_close($ch);
    
    // Check response status
    if ($statusCode === 200) {
        $responseData = json_decode($response, true);
        
        // Store payment details in session for verification
        $_SESSION['khalti_payment'] = [
            'pidx' => $responseData['pidx'],
            'order_id' => $orderId,
            'purchase_order_id' => $purchaseOrderId
        ];
        
        // Redirect to Khalti payment page
        header("Location: " . $responseData['payment_url']);
        exit;
    } else {
        // Payment initiation failed
        $errorData = json_decode($response, true);
        $errorMessage = isset($errorData['error_key']) ? $errorData['error_key'] : 'Unknown error';
        
        // Redirect to payment failure page
        header("Location: payment_success.php?order_id=$orderId&status=failed&message=" . urlencode("Payment initiation failed: $errorMessage"));
        exit;
    }
} else {
    // Not a POST request
    header('Location: index.php');
    exit;
}
?>