<?php
// Adjust the path for includes based on the current directory
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get all books
$books = getAllBooks();
?>

<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Admin Panel
            </div>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
                <a href="books.php" class="list-group-item list-group-item-action active">Manage Books</a>
                <a href="add_book.php" class="list-group-item list-group-item-action">Add New Book</a>
                <a href="orders.php" class="list-group-item list-group-item-action">Manage Orders</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <h2>Manage Books</h2>
        
        <div class="mb-3">
            <a href="add_book.php" class="btn btn-primary">Add New Book</a>
        </div>
        
        <?php if (empty($books)): ?>
            <div class="alert alert-info">No books found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Added By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['author']; ?></td>
                                <td>Rs. <?php echo $book['price']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $book['status'] === 'available' ? 'success' : ($book['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                        <?php echo ucfirst($book['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $book['is_old'] ? 'Used' : 'New'; ?></td>
                                <td><?php echo $book['added_by_name'] ? $book['added_by_name'] : 'Admin'; ?></td>
                                <td>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    
                                    <?php if ($book['status'] === 'pending' && $book['is_old']): ?>
                                        <a href="approve_book.php?id=<?php echo $book['id']; ?>&action=approve" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this book?')">Approve</a>
                                    <?php endif; ?>
                                    
                                    <a href="delete_book.php?id=<?php echo $book['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>