<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 dark:border dark:border-gray-700">
    <h2 class="text-2xl font-bold mb-6">Manage Users</h2>
    
    <?php if (empty($users)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
            <p>No users available.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">ID</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Username</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Email</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Role</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Created</th>
                        <th class="py-2 px-4 border-b border-gray-200 dark:border-gray-600 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo $user['id']; ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <form action="index.php?controller=admin&action=users" method="POST" class="inline">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm" <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-600">
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <a href="index.php?controller=admin&action=users&delete_user=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this user?');"
                                       class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>