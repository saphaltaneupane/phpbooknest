<div class="flex flex-col md:flex-row">
    <!-- Sidebar with filters -->
    <div class="w-full md:w-1/4 mb-6 md:mb-0 md:pr-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4">Filters</h3>
            
            <form action="index.php" method="GET">
                <input type="hidden" name="controller" value="book">
                <input type="hidden" name="action" value="filter">
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Price Range</label>
                    <div class="flex space-x-2">
                        <input type="number" name="min" placeholder="Min" min="0" class="w-1/2 border rounded-md px-3 py-2 text-base">
                        <input type="number" name="max" placeholder="Max" min="0" class="w-1/2 border rounded-md px-3 py-2 text-base">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 mb-2">Sort by Price</label>
                    <select name="sort" class="w-full border rounded-md px-3 py-2 text-base">
                        <option value="asc">Low to High</option>
                        <option value="desc">High to Low</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700 transition">
                    Apply Filters
                </button>
            </form>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="mt-6 pt-6 border-t dark:border-gray-700">
                    <a href="index.php?controller=book&action=add" class="block w-full bg-green-600 text-white rounded-md px-4 py-2 text-center hover:bg-green-700 transition">
                        <i class="fas fa-plus mr-2"></i> Add Old Book
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main content -->
    <div class="w-full md:w-3/4">
        <h2 class="text-2xl font-bold mb-6">Available Books</h2>
        
        <?php if (empty($books)): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
                <p>No books found.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($books as $book): ?>
                    <div class="book-card bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden transition duration-300 ease-in-out dark:border dark:border-gray-700">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <span class="px-2 py-1 bg-<?php echo $book['condition'] === 'new' ? 'green' : 'amber'; ?>-600 text-white text-xs rounded">
                                    <?php echo ucfirst(htmlspecialchars($book['condition'])); ?>
                                </span>
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-300 mb-2">Author: <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                <?php 
                                $desc = htmlspecialchars($book['description']);
                                echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc; 
                                ?>
                            </p>
                            
                            <div class="flex justify-between items-center">
                                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                    Rs. <?php echo number_format($book['price'], 2); ?>
                                </p>
                                
                                <?php if ($book['status'] === 'available'): ?>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="index.php?controller=order&action=checkout&book_id=<?php echo $book['id']; ?>" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                                            Buy Now
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?controller=user&action=login" class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition">
                                            Login to Buy
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-red-500 text-white rounded">Sold Out</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-sm text-gray-600 dark:text-gray-300">
                            <p>Seller: <?php echo htmlspecialchars($book['seller'] ?? 'Admin'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>