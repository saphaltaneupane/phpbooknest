<div class="max-w-md mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
    
    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="index.php?controller=user&action=register" method="POST">
        <div class="mb-4">
            <label for="username" class="block text-gray-700 dark:text-gray-300 mb-2">Username</label>
            <input type="text" id="username" name="username" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 dark:text-gray-300 mb-2">Email</label>
            <input type="email" id="email" name="email" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700 dark:text-gray-300 mb-2">Password</label>
            <input type="password" id="password" name="password" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <div class="mb-6">
            <label for="confirm_password" class="block text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="w-full border rounded-md px-3 py-2 text-base" required>
        </div>
        
        <button type="submit" class="w-full bg-indigo-600 text-white rounded-md px-4 py-2 hover:bg-indigo-700 transition">
            Register
        </button>
    </form>
    
    <div class="mt-4 text-center">
        <p class="text-gray-600 dark:text-gray-400">
            Already have an account? <a href="index.php?controller=user&action=login" class="text-indigo-600 dark:text-indigo-400 hover:underline">Login</a>
        </p>
    </div>
</div>