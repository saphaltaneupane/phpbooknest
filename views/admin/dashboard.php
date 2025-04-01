<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-indigo-600 text-white rounded-lg p-6 shadow">
            <h3 class="text-xl font-semibold mb-2">Total Users</h3>
            <p class="text-3xl font-bold"><?php echo count($users); ?></p>
            <a href="index.php?controller=admin&action=users" class="inline-block mt-4 text-indigo-200 hover:text-white">
                Manage Users <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-green-600 text-white rounded-lg p-6 shadow">
            <h3 class="text-xl font-semibold mb-2">Total Books</h3>
            <p class="text-3xl font-bold"><?php echo count($books); ?></p>
            <a href="index.php?controller=admin&action=books" class="inline-block mt-4 text-green-200 hover:text-white">
                Manage Books <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="bg-amber-600 text-white rounded-lg p-6 shadow">
            <h3 class="text-xl font-semibold mb-2">Total Orders</h3>
            <p class="text-3xl font-bold"><?php echo count($orders); ?></p>
            <a href="index.php?controller=admin&action=orders" class="inline-block mt-4 text-amber-200 hover:text-white">
                Manage Orders <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h3 class="text-xl font-semibold mb-4">Recent Books</h3>
            
            <?php if (empty($books)): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                    <p>No books available.</p>
                </div>
            <?php else: ?>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                <th class="py-2 px-4 text-left">Title</th>
                                <th class="py-2 px-4 text-left">Author</th>
                                <th class="py-2 px-4 text-left">Price</th>
                                <th class="py-2 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentBooks = array_slice($books, 0, 5);
                            foreach ($recentBooks as $book): 
                            ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700"><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700"><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">Rs. <?php echo number_format($book['price'], 2); ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                        <span class="px-2 py-1 rounded text-xs text-white 
                                            <?php echo $book['status'] === 'available' ? 'bg-green-600' : 'bg-red-600'; ?>">
                                            <?php echo ucfirst($book['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <h3 class="text-xl font-semibold mb-4">Recent Orders</h3>
            
            <?php if (empty($orders)): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                    <p>No orders available.</p>
                </div>
            <?php else: ?>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                <th class="py-2 px-4 text-left">User</th>
                                <th class="py-2 px-4 text-left">Book</th>
                                <th class="py-2 px-4 text-left">Method</th>
                                <th class="py-2 px-4 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentOrders = array_slice($orders, 0, 5);
                            foreach ($recentOrders as $order): 
                            ?>
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700"><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700"><?php echo htmlspecialchars($order['title']); ?></td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                        <?php echo $order['payment_method'] === 'khalti' ? 'Khalti' : 'Cash'; ?>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                        <span class="px-2 py-1 rounded text-xs text-white 
                                            <?php echo $order['order_status'] === 'completed' ? 'bg-green-600' : 'bg-yellow-600'; ?>">
                                            <?php echo ucfirst($order['order_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>