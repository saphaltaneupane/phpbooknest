<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Manage Orders</h2>
    
    <?php if (empty($orders)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>No orders available.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">User</th>
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
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo $order['id']; ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($order['username']); ?></td>
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
                                <form action="index.php?controller=admin&action=orders" method="POST" class="inline">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="pending" <?php echo $order['order_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="processing" <?php echo $order['order_status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="shipping" <?php echo $order['order_status'] === 'shipping' ? 'selected' : ''; ?>>Shipping</option>
                                        <option value="completed" <?php echo $order['order_status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $order['order_status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
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