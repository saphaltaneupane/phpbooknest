<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Add Book</h2>
    
    <form action="index.php?controller=book&action=add" method="POST">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 dark:text-gray-300 mb-2">Book Title</label>
            <input type="text" id="title" name="title" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-4">
            <label for="author" class="block text-gray-700 dark:text-gray-300 mb-2">Author</label>
            <input type="text" id="author" name="author" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea id="description" name="description" rows="4" class="w-full border rounded-md px-3 py-2 text-base" required></textarea>
        </div>
        
        <div class="mb-4">
            <label for="price" class="block text-gray-700 dark:text-gray-300 mb-2">Price (Rs.)</label>
            <input type="number" id="price" name="price" min="1" step="0.01" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 mb-2">Condition</label>
            <div class="flex space-x-4">
                <label class="flex items-center">
                    <input type="radio" name="condition" value="old" checked class="mr-2">
                    <span>Old</span>
                </label>
                <label class="flex items-center">
                    <input type="radio" name="condition" value="new" class="mr-2">
                    <span>New</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-between">
            <a href="index.php" class="bg-gray-500 text-white rounded-md px-4 py-2 hover:bg-gray-600 transition">
                Cancel
            </a>
            <button type="submit" class="bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700 transition">
                Add Book
            </button>
        </div>
    </form>
</div>