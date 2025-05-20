<?php
require_once 'includes/header.php';

// Get category ID from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no valid category ID, redirect to homepage
if ($categoryId <= 0) {
    redirect('index.php');
}

// Get category details
$categoryQuery = "SELECT * FROM categories WHERE id = $categoryId";
$categoryResult = mysqli_query($conn, $categoryQuery);

// If category doesn't exist, redirect to homepage
if (!$categoryResult || mysqli_num_rows($categoryResult) === 0) {
    redirect('index.php');
}

$category = mysqli_fetch_assoc($categoryResult);

// Set up pagination
$booksPerPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $booksPerPage;

// Get total number of books in this category
$countQuery = "SELECT COUNT(*) as total FROM books WHERE category_id = $categoryId AND status = 'available'";
$countResult = mysqli_query($conn, $countQuery);
$totalBooks = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalBooks / $booksPerPage);

// Get books for the current page
$booksQuery = "SELECT * FROM books WHERE category_id = $categoryId AND status = 'available' ORDER BY created_at DESC LIMIT $offset, $booksPerPage";
$booksResult = mysqli_query($conn, $booksQuery);

$books = [];
if ($booksResult && mysqli_num_rows($booksResult) > 0) {
    while ($row = mysqli_fetch_assoc($booksResult)) {
        $books[] = $row;
    }
}

// Get all categories for navigation
$allCategoriesQuery = "SELECT * FROM categories ORDER BY name";
$allCategoriesResult = mysqli_query($conn, $allCategoriesQuery);
$allCategories = [];
if ($allCategoriesResult && mysqli_num_rows($allCategoriesResult) > 0) {
    while ($row = mysqli_fetch_assoc($allCategoriesResult)) {
        $allCategories[] = $row;
    }
}
?>

<style>
    /* Internal CSS */
    /* Category page specific styles */
    .category-header {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .category-title {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }
    
    .category-description {
        margin-top: 10px;
        opacity: 0.9;
    }
    
    .category-nav {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .category-badge {
        background-color: #007bff;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        margin-right: 10px;
        text-decoration: none;
        display: inline-block;
        transition: background-color 0.3s, transform 0.2s;
        font-weight: 500;
    }
    
    .category-badge:hover {
        background-color: #0056b3;
        text-decoration: none;
        color: white;
        transform: translateY(-2px);
    }
    
    .category-badge.active {
        background-color: #0056b3;
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        font-weight: 600;
    }
    
    .pagination-container {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    
    .pagination {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .page-item {
        margin: 0 2px;
    }
    
    .page-link {
        display: block;
        padding: 8px 12px;
        text-decoration: none;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        transition: all 0.2s;
    }
    
    .page-link:hover {
        color: #0056b3;
        background-color: #e9ecef;
        border-color: #dee2e6;
        text-decoration: none;
    }
    
    .page-item.active .page-link {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    .empty-results {
        text-align: center;
        padding: 40px 20px;
        background-color: #f8f9fa;
        border-radius: 8px;
        color: #6c757d;
        font-size: 1.1rem;
    }
    
    .empty-results i {
        font-size: 3rem;
        display: block;
        margin-bottom: 15px;
        color: #dee2e6;
    }
    
    /* Different colors for different category badges */
    .category-badge.fiction {
        background-color: #6a11cb;
        background-image: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
    }
    
    .category-badge.non-fiction {
        background-color: #0ba360;
        background-image: linear-gradient(to right, #0ba360 0%, #3cba92 100%);
    }
    
    .category-badge.children {
        background-color: #ff9a9e;
        background-image: linear-gradient(to right, #ff9a9e 0%, #fad0c4 100%);
        color: #333;
    }
    
    .category-badge.educational {
        background-color: #4facfe;
        background-image: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
    }
    
    .category-badge.comics {
        background-color: #f857a6;
        background-image: linear-gradient(to right, #f857a6 0%, #ff5858 100%);
    }
    
    .category-badge.horror {
        background-color: #232526;
        background-image: linear-gradient(to right, #232526 0%, #414345 100%);
    }
    
    /* Book card styles */
    .card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.25rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    
    .card-body {
        flex: 1 1 auto;
        padding: 1.25rem;
    }
    
    .card-title {
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .card-text {
        margin-bottom: 0.5rem;
    }
    
    .book-card {
        height: 100%;
    }
    
    .book-image {
        height: 200px;
        object-fit: cover;
    }
    
    .book-author {
        font-style: italic;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .book-price {
        font-weight: bold;
        color: #28a745;
        font-size: 1.1rem;
    }
    
    .rating {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        gap: 5px;
    }
    
    .stars {
        display: inline-flex;
        color: #ffc107;
        margin-right: 5px;
    }
    
    .stars i {
        margin-right: 2px;
    }
    
    .rating-count {
        color: #6c757d;
        font-size: 0.9rem;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 10px;
        display: inline-block;
    }
    
    /* Custom style for Used badge with black text */
    .used-badge {
        color: black !important;
        font-weight: bold;
    }
    
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    
    .bg-secondary {
        background-color: #6c757d !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        max-width: 1140px;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    @media (max-width: 768px) {
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .category-header {
            padding: 15px;
        }
        
        .category-title {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="container">
    <!-- Category Header -->
    <div class="category-header">
        <h1 class="category-title"><?php echo $category['name']; ?></h1>
        <?php if (isset($category['description']) && !empty($category['description'])): ?>
            <p class="category-description"><?php echo $category['description']; ?></p>
        <?php endif; ?>
    </div>
    
    <!-- Categories Navigation -->
    <div class="category-nav">
        <a href="index.php" class="category-badge">All Books</a>
        <?php foreach ($allCategories as $cat): 
            // Generate CSS class based on category name
            $categoryClass = strtolower(str_replace(' ', '-', str_replace('\'s', '', str_replace('&', 'and', $cat['name']))));
            $isActive = ($cat['id'] == $categoryId) ? 'active' : '';
        ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>" class="category-badge <?php echo $categoryClass; ?> <?php echo $isActive; ?>">
                <?php echo $cat['name']; ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Books Grid -->
    <?php if (empty($books)): ?>
        <div class="empty-results">
            <i class="bi bi-book"></i>
            <p>No books found in this category.</p>
            <a href="index.php" class="btn btn-primary">Back to Homepage</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($books as $book): ?>
                <div class="col-md-3 mb-4">
                    <div class="card book-card h-100">
                        <img src="assets/images/<?php echo $book['image']; ?>" class="card-img-top book-image" alt="<?php echo $book['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title" title="<?php echo $book['title']; ?>"><?php echo $book['title']; ?></h5>
                            <p class="card-text book-author" title="by <?php echo $book['author']; ?>">by <?php echo $book['author']; ?></p>
                            <div class="card-text rating">
                                <div class="stars">
                                <?php 
                                $rating = getBookRating($book['id']);
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="bi bi-star-fill"></i>';
                                    } else if ($i <= $rating + 0.5) {
                                        echo '<i class="bi bi-star-half"></i>';
                                    } else {
                                        echo '<i class="bi bi-star"></i>';
                                    }
                                }
                                ?>
                                </div>
                                <span class="rating-count"><?php echo $rating; ?></span>
                            </div>
                            <p class="card-text book-price">Rs. <?php echo $book['price']; ?></p>
                            <?php if (isset($book['is_old']) && $book['is_old'] == 1): ?>
                                <span class="badge bg-secondary used-badge">Used</span>
                            <?php endif; ?>
                            <p class="card-text">
                                <small class="<?php echo $book['quantity'] > 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php if ($book['quantity'] > 0): ?>
                                        <?php echo $book['quantity']; ?> copies available
                                    <?php else: ?>
                                        Out of stock
                                    <?php endif; ?>
                                </small>
                            </p>
                            <a href="book_details.php?id=<?php echo $book['id']; ?>" 
                               class="btn <?php echo $book['quantity'] > 0 ? 'btn-primary' : 'btn-secondary'; ?>">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <ul class="pagination">
                    <!-- Previous Page Link -->
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($page <= 1) ? '#' : "category.php?id=$categoryId&page=" . ($page - 1); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page Number Links -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link" href="category.php?id=<?php echo $categoryId; ?>&page=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <!-- Next Page Link -->
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo ($page >= $totalPages) ? '#' : "category.php?id=$categoryId&page=" . ($page + 1); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any interactive elements if needed
    });
</script>

<?php require_once 'includes/footer.php'; ?>