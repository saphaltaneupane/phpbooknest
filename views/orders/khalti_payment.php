<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Pay with Khalti</h2>
    
    <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Book Details</h3>
        <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded">
            <h4 class="font-medium"><?php echo htmlspecialchars($book['title']); ?></h4>
            <p class="text-gray-600 dark:text-gray-400">Author: <?php echo htmlspecialchars($book['author']); ?></p>
            <p class="text-gray-600 dark:text-gray-400">Condition: <?php echo ucfirst(htmlspecialchars($book['condition'])); ?></p>
            <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-2">Price: Rs. <?php echo number_format($book['price'], 2); ?></p>
        </div>
    </div>
    
    <div id="payment-status" class="hidden mb-4 p-4 rounded"></div>
    
    <button id="payment-button" class="w-full bg-purple-600 text-white rounded-md px-4 py-3 flex items-center justify-center hover:bg-purple-700 transition">
        <img src="https://khalti.com/static/khalti-logo-3adbe.svg" alt="Khalti" class="h-6 mr-2"> Pay with Khalti
    </button>
    
    <div class="mt-4 text-center">
        <a href="index.php?controller=order&action=checkout&book_id=<?php echo $book['id']; ?>" class="text-gray-600 dark:text-gray-400 hover:underline">
            <i class="fas fa-arrow-left mr-1"></i> Back to Checkout
        </a>
    </div>
</div>

<!-- Khalti Payment Integration -->
<script src="https://khalti.com/static/khalti-checkout.js"></script>
<script>
    // Convert price to paisa (1 Rs = 100 paisa)
    const amount = <?php echo $book['price'] * 100; ?>;
    const productName = "<?php echo htmlspecialchars($book['title']); ?>";
    const productId = "<?php echo $book['id']; ?>";
    
    // Khalti configuration
    var config = {
        // replace this key with yours
        "publicKey": "9c47618c854f474197759dbacae10f7c",
        "productIdentity": productId,
        "productName": productName,
        "productUrl": window.location.href,
        "amount": amount,
        "eventHandler": {
            onSuccess (payload) {
                // Show loading status
                document.getElementById('payment-status').classList.remove('hidden', 'bg-red-100', 'text-red-700', 'border-red-500');
                document.getElementById('payment-status').classList.add('bg-yellow-100', 'text-yellow-700', 'border-yellow-500');
                document.getElementById('payment-status').innerHTML = '<p>Processing payment, please wait...</p>';
                
                // Send payment data to server for verification
                $.ajax({
                    url: "index.php?controller=order&action=khalti_verify",
                    type: "POST",
                    data: {
                        token: payload.token,
                        amount: payload.amount
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        
                        if (data.status === 'success') {
                            document.getElementById('payment-status').classList.remove('bg-yellow-100', 'text-yellow-700', 'border-yellow-500');
                            document.getElementById('payment-status').classList.add('bg-green-100', 'text-green-700', 'border-green-500');
                            document.getElementById('payment-status').innerHTML = '<p>Payment successful! Redirecting to order history...</p>';
                            
                            // Redirect to order history page
                            setTimeout(function() {
                                window.location.href = "index.php?controller=order&action=history";
                            }, 2000);
                        } else {
                            document.getElementById('payment-status').classList.remove('bg-yellow-100', 'text-yellow-700', 'border-yellow-500');
                            document.getElementById('payment-status').classList.add('bg-red-100', 'text-red-700', 'border-red-500');
                            document.getElementById('payment-status').innerHTML = '<p>Error: ' + data.message + '</p>';
                        }
                    },
                    error: function() {
                        document.getElementById('payment-status').classList.remove('bg-yellow-100', 'text-yellow-700', 'border-yellow-500');
                        document.getElementById('payment-status').classList.add('bg-red-100', 'text-red-700', 'border-red-500');
                        document.getElementById('payment-status').innerHTML = '<p>Error: Could not verify payment. Please contact support.</p>';
                    }
                });
            },
            onError (error) {
                document.getElementById('payment-status').classList.remove('hidden', 'bg-yellow-100', 'text-yellow-700', 'border-yellow-500');
                document.getElementById('payment-status').classList.add('bg-red-100', 'text-red-700', 'border-red-500');
                document.getElementById('payment-status').innerHTML = '<p>Error: ' + error + '</p>';
            },
            onClose () {
                console.log('Payment widget closed');
            }
        }
    };

    // Initialize Khalti payment widget
    var checkout = new KhaltiCheckout(config);
    
    // Attach to button
    document.getElementById('payment-button').addEventListener('click', function() {
        checkout.show();
    });
</script>