<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Manage Books</h2>
        <a href="index.php?controller=book&action=add" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
            <i class="fas fa-plus mr-1"></i> Add New Book
        </a>
    </div>
    
    <?php if (empty($books)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>No books available.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Title</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Author</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Price</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Condition</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Status</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Seller</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo $book['id']; ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($book['title']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($book['author']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">Rs. <?php echo number_format($book['price'], 2); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php echo ucfirst(htmlspecialchars($book['condition'])); ?>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <form action="index.php?controller=admin&action=books" method="POST" class="inline">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <select name="status" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="available" <?php echo $book['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="sold" <?php echo $book['status'] === 'sold' ? 'selected' : ''; ?>>Sold</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php echo htmlspecialchars($book['seller'] ?? 'Admin'); ?>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <a href="index.php?controller=admin&action=books&delete_book=<?php echo $book['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this book?');"
                                   class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>