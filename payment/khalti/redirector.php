<?php
// This file handles redirects from malformed khalti callback URLs
session_start();

// Log all incoming request details for debugging
error_log("Redirector accessed with: " . json_encode($_SERVER['REQUEST_URI']));
error_log("Query string: " . json_encode($_GET));

// Get the pidx from query parameters if available
$pidx = isset($_GET['pidx']) ? $_GET['pidx'] : '';

// Redirect to the proper callback URL
if (!empty($pidx)) {
    // Include the pidx in the redirect
    header("Location: http://localhost/booktrading/payment/khalti/callback.php?pidx=" . urlencode($pidx));
} else {
    // Default redirect if no pidx is present
    header("Location: http://localhost/booktrading/payment/khalti/callback.php");
}
exit;
?>