<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden dark:border dark:border-gray-700">
    <div class="p-6">
        <h2 class="text-2xl font-bold mb-6">Checkout</h2>
        
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Book Details</h3>
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded">
                <h4 class="font-medium"><?php echo htmlspecialchars($book['title']); ?></h4>
                <p class="text-gray-600 dark:text-gray-400">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                <p class="text-gray-600 dark:text-gray-400">Condition: <?php echo ucfirst(htmlspecialchars($book['condition'])); ?></p>
                <p class="text-indigo-600 dark:text-indigo-400 font-bold mt-2">Price: Rs. <?php echo number_format($book['price'], 2); ?></p>
            </div>
        </div>
        
        <form action="index.php?controller=order&action=process" method="POST">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Payment Method</h3>
                <div class="space-y-2">
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600">
                        <input type="radio" name="payment_method" value="cash" checked class="mr-2">
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="flex items-center p-3 border rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 dark:border-gray-600">
                        <input type="radio" name="payment_method" value="khalti" class="mr-2">
                        <span>Pay with Khalti</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-between">
                <a href="index.php" class="bg-gray-500 text-white rounded-md px-4 py-2 hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700 transition">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</div>