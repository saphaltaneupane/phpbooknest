<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all users
$users = getAllUsers();
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Admin Panel
            </div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="users.php" class="list-group-item list-group-item-action active">Manage Users</a>
                <a href="books.php" class="list-group-item list-group-item-action">Manage Books</a>
                <a href="add_book.php" class="list-group-item list-group-item-action">Add New Book</a>
                <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <h2>Manage Users</h2>
        
        <?php if (empty($users)): ?>
            <div class="alert alert-info">No users found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Joined On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><?php echo $user['address'] ? $user['address'] : '-'; ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>