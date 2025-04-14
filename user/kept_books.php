<?php
$relativePath = '../';
require_once $relativePath . 'includes/header.php';

// Redirect if not logged in or is admin
if (!isLoggedIn() || isAdmin()) {
    redirect($relativePath . 'login.php');
}

// Get user ID
$userId = $_SESSION['user_id'];

// Fetch books kept by the user (both available and sold)
$query = "SELECT * FROM books WHERE added_by = $userId AND status IN ('available', 'sold')";
$result = mysqli_query($conn, $query);
$keptBooks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $keptBooks[] = $row;
}
?>

<div class="row">
    <div class="col-12">
        <h2>My Kept Books</h2>
        <?php if (empty($keptBooks)): ?>
            <div class="alert alert-info">You haven't kept any books yet.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Added Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keptBooks as $book): ?>
                            <tr>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['author']; ?></td>
                                <td>Rs. <?php echo number_format($book['price'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $book['status'] === 'available' ? 'success' : 'secondary'; ?>">
                                        <?php echo ucfirst($book['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>
