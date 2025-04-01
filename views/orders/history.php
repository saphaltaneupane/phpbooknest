<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Your Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>You haven't placed any orders yet.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Book</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Price</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Payment Method</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Payment Status</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Order Status</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($order['title']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">Rs. <?php echo number_format($order['price'], 2); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php echo $order['payment_method'] === 'khalti' ? 'Khalti' : 'Cash on Delivery'; ?>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <span class="px-2 py-1 rounded text-xs text-white 
                                    <?php echo $order['payment_status'] === 'completed' ? 'bg-green-600' : 'bg-yellow-600'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <span class="px-2 py-1 rounded text-xs text-white 
                                    <?php 
                                    if ($order['order_status'] === 'completed') {
                                        echo 'bg-green-600';
                                    } elseif ($order['order_status'] === 'cancelled') {
                                        echo 'bg-red-600';
                                    } else {
                                        echo 'bg-yellow-600';
                                    }
                                    ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>