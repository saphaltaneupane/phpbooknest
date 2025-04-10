// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle password confirmation validation
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
            if (passwordField.value !== confirmPasswordField.value) {
                confirmPasswordField.setCustomValidity("Passwords don't match");
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    }
    
    // Handle dark mode
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.classList.add('dark');
    }
    
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
        if (event.matches) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    });
    
    // Khalti Payment integration (initialized when needed)
    window.initializeKhaltiPayment = function(amount, purchaseOrderId, purchaseOrderName, returnUrl) {
        const config = {
            // replace this key with yours
            "publicKey": "dcae89c4d38243cd86fcca483bb97b1e",
            "productIdentity": purchaseOrderId,
            "productName": purchaseOrderName,
            "productUrl": window.location.href,
            "paymentPreference": [
                "KHALTI",
                "EBANKING",
                "MOBILE_BANKING",
                "CONNECT_IPS",
                "SCT",
            ],
            "eventHandler": {
                onSuccess(payload) {
                    // hit merchant api for initiating verification
                    console.log(payload);
                    
                    // Show loading spinner
                    document.getElementById('payment-loading').style.display = 'block';
                    
                    // Send verification request to server
                    fetch('process_khalti_payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            token: payload.token,
                            amount: payload.amount,
                            purchase_order_id: purchaseOrderId
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = returnUrl + '?status=success&txn_id=' + data.transaction_id;
                        } else {
                            window.location.href = returnUrl + '?status=failed&message=' + data.message;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.location.href = returnUrl + '?status=failed&message=An error occurred during payment verification';
                    });
                },
                onError(error) {
                    console.log(error);
                    alert('Error occurred during payment. Please try again.');
                },
                onClose() {
                    console.log('Widget is closing');
                }
            }
        };

        const checkout = new KhaltiCheckout(config);
        return checkout;
    };
});