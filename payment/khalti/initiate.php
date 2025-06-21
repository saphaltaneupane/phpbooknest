<?php
// Session settings must come before session initialization
$sessionStarted = (session_status() == PHP_SESSION_ACTIVE);
if (!$sessionStarted) {
    // Only set these if session hasn't started yet
    ini_set('session.cookie_lifetime', 3600); // 1 hour
    ini_set('session.gc_maxlifetime', 3600);
    session_start();
}

require_once '../../config/db.php';
require_once '../../includes/functions.php';

// Setup error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/khalti_initiate.log');

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('../../login.php');
    exit;
}

// Get order data from POST
$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

// Validate input
if ($orderId <= 0 || $amount <= 0) {
    echo "<p>Invalid order information. <a href='../../index.php'>Return to homepage</a>.</p>";
    exit;
}

// Get user data
$userId = $_SESSION['user_id'];
$userQuery = "SELECT * FROM users WHERE id = $userId";
$userResult = mysqli_query($conn, $userQuery);

if (!$userResult || mysqli_num_rows($userResult) === 0) {
    echo "<p>Could not find user information. <a href='../../index.php'>Return to homepage</a>.</p>";
    exit;
}

$user = mysqli_fetch_assoc($userResult);

// Create unique purchase order ID that includes the pidx prefix for better tracing
$purchaseOrderId = 'ORDER-' . time() . '-' . $orderId;

// Update order with this purchase order ID
$updateQuery = "UPDATE orders SET purchase_order_id = '$purchaseOrderId' WHERE id = $orderId";
if (!mysqli_query($conn, $updateQuery)) {
    echo "<p>Database error: " . mysqli_error($conn) . " <a href='../../index.php'>Return to homepage</a>.</p>";
    exit;
}

// Get order information for the payment name
$itemsQuery = "SELECT b.title FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = $orderId LIMIT 1";
$itemsResult = mysqli_query($conn, $itemsQuery);

$orderName = "Order #$orderId";
if ($itemsResult && mysqli_num_rows($itemsResult) > 0) {
    $item = mysqli_fetch_assoc($itemsResult);
    $orderName = $item['title'];
    
    // Get total items count
    $countQuery = "SELECT COUNT(*) as total FROM order_items WHERE order_id = $orderId";
    $countResult = mysqli_query($conn, $countQuery);
    if ($countResult && $row = mysqli_fetch_assoc($countResult)) {
        if ($row['total'] > 1) {
            $orderName .= " and " . ($row['total'] - 1) . " more items";
        }
    }
}

// Convert amount to paisa (Khalti requires amount in paisa)
$amountInPaisa = (int)($amount * 100);

// Use absolute URL with correct domain for return_url
$returnUrl = "http://localhost/booktrading/payment/khalti/callback.php?order_id=$orderId";
$websiteUrl = "http://localhost/booktrading";

// Log the return URL for debugging
error_log("Return URL: $returnUrl");

// Prepare payload for Khalti API
$payload = [
    "return_url" => $returnUrl,
    "website_url" => $websiteUrl,
    "amount" => $amountInPaisa,
    "purchase_order_id" => $purchaseOrderId,
    "purchase_order_name" => $orderName,
    "customer_info" => [
        "name" => $user['name'],
        "email" => $user['email'],
        "phone" => $user['phone']
    ]
];

// Log the request
error_log("Khalti Initiate Request: " . json_encode($payload));

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://a.khalti.com/api/v2/epayment/initiate/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Key ' . KHALTI_SECRET_KEY,
    'Content-Type: application/json'
]);

// Execute the request
$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    error_log("cURL Error: $error");
    echo "<p>Connection error: $error <a href='../../index.php'>Return to homepage</a>.</p>";
    exit;
}

// Close cURL
curl_close($ch);

// Log the response
error_log("Khalti Initiate Response (HTTP $statusCode): $response");

// Process the response
if ($statusCode === 200) {
    $responseData = json_decode($response, true);
    
    if (!isset($responseData['payment_url']) || !isset($responseData['pidx'])) {
        echo "<p>Invalid response from payment gateway. <a href='../../index.php'>Return to homepage</a>.</p>";
        exit;
    }
    
    // Store payment details in session
    $_SESSION['khalti_payment'] = [
        'pidx' => $responseData['pidx'],
        'order_id' => $orderId,
        'purchase_order_id' => $purchaseOrderId,
        'initiated_at' => time()
    ];
    
    // Redirect to Khalti payment page
    header("Location: " . $responseData['payment_url']);
    exit;
} else {
    // Payment initiation failed
    $responseData = json_decode($response, true);
    $errorMessage = isset($responseData['detail']) ? $responseData['detail'] : 'Unknown error';
    
    echo "<p>Payment initiation failed: $errorMessage <a href='../../index.php'>Return to homepage</a>.</p>";
    exit;
}
?>