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

<style>
    /* Pure CSS for Kept Books Page */
    :root {
        --primary-color: #6c63ff;
        --primary-dark: #5652db;
        --secondary-color: #ff9d72;
        --accent-color: #ff6584;
        --success-color: #4caf50;
        --warning-color: #ff9800;
        --danger-color: #f44336;
        --info-color: #2196f3;
        --light-color: #f8f9ff;
        --dark-color: #2c2c54;
        --gray-light: #f0f2f9;
        --gray-medium: #e0e0e0;
        --gray-dark: #a0a0a0;
        --text-primary: #333333;
        --text-secondary: #666666;
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --radius-sm: 6px;
        --transition: all 0.3s ease;
    }
    
    .books-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .page-title {
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        font-size: 1.75rem;
        font-weight: 600;
        position: relative;
        padding-left: 15px;
    }
    
    .page-title::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 5px;
        background-color: var(--primary-color);
        border-radius: 3px;
    }
    
    /* Empty Message */
    .info-message {
        background-color: rgba(33, 150, 243, 0.1);
        border: 1px solid rgba(33, 150, 243, 0.3);
        color: var(--info-color);
        padding: 15px 20px;
        border-radius: var(--radius-sm);
        margin-bottom: 20px;
    }
    
    /* Table Styles */
    .table-container {
        overflow-x: auto;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        margin-bottom: 25px;
    }
    
    .books-table {
        width: 100%;
        border-collapse: collapse;
        white-space: nowrap;
        font-size: 0.95rem;
        background-color: white;
    }
    
    .books-table thead {
        background-color: var(--gray-light);
    }
    
    .books-table th {
        text-align: left;
        padding: 14px 16px;
        font-weight: 600;
        color: var(--dark-color);
        border-bottom: 2px solid var(--gray-medium);
    }
    
    .books-table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--gray-light);
        color: var(--text-secondary);
    }
    
    .books-table tbody tr:nth-child(even) {
        background-color: var(--gray-light);
    }
    
    .books-table tbody tr:hover {
        background-color: rgba(108, 99, 255, 0.05);
    }
    
    /* Status Badges */
    .status-indicator {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: capitalize;
        color: white;
    }
    
    .status-available {
        background-color: var(--success-color);
    }
    
    .status-sold {
        background-color: var(--gray-dark);
    }
    
    /* Back Button */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--gray-dark) 0%, var(--gray-medium) 100%);
        color: var(--text-primary);
        padding: 10px 20px;
        border-radius: 30px;
        text-decoration: none;
        font-weight: 500;
        transition: var(--transition);
        margin-top: 20px;
    }
    
    .back-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        background: linear-gradient(135deg, var(--gray-medium) 0%, var(--gray-dark) 100%);
    }
    
    /* Responsive Table */
    @media screen and (max-width: 768px) {
        .books-table {
            display: block;
        }
        
        .books-table thead {
            display: none;
        }
        
        .books-table tbody {
            display: block;
        }
        
        .books-table tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: var(--radius-sm);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        
        .books-table td {
            display: flex;
            justify-content: space-between;
            text-align: right;
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-light);
        }
        
        .books-table td::before {
            content: attr(data-label);
            font-weight: 600;
            float: left;
            text-align: left;
            color: var(--dark-color);
        }
        
        .books-table tbody tr:hover {
            transform: translateY(-2px);
        }
    }
    
    @media screen and (max-width: 576px) {
        .books-container {
            padding: 0 15px;
        }
        
        .page-title {
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
        }
    }
</style>

<div class="books-container">
    <h2 class="page-title">My Kept Books</h2>
    
    <?php if (empty($keptBooks)): ?>
        <div class="info-message">You haven't kept any books yet.</div>
    <?php else: ?>
        <div class="table-container">
            <table class="books-table">
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
                            <td data-label="Title"><?php echo $book['title']; ?></td>
                            <td data-label="Author"><?php echo $book['author']; ?></td>
                            <td data-label="Price">Rs. <?php echo number_format($book['price'], 2); ?></td>
                            <td data-label="Status">
                                <span class="status-indicator status-<?php echo $book['status']; ?>">
                                    <?php echo ucfirst($book['status']); ?>
                                </span>
                            </td>
                            <td data-label="Added Date"><?php echo date('M d, Y', strtotime($book['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    
    <div>
        <a href="dashboard.php" class="back-link">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php require_once $relativePath . 'includes/footer.php'; ?>