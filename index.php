<?php
require_once 'includes/header.php';

if (isLoggedIn() && isAdmin()) {
    redirect('admin/dashboard.php'); // Redirect admin to the admin dashboard
}

// Get available books
$books = getAvailableBooks();

// Get content-based recommendations if user is logged in
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $userPreferred = getUserPreferredBooks($userId);
    
    // Log for debugging
    error_log("User $userId has " . count($userPreferred) . " preferred books");
    
    if (count($userPreferred) > 0) {
        // User has purchase/rating history, use content-based recommendations
        $recommendedBooks = getContentBasedRecommendations($userId, 4);
    } else {
        // User is logged in but has no history, show top rated books
        $recommendedBooks = getTopRatedBooks(4);
    }
} else {
    // Not logged in, show top rated books with 4+ stars
    $recommendedBooks = getTopRatedBooks(4);
}
?>

<style>
    /* Internal CSS */
    .jumbotron {
        background: url('assets/images/banner.jpg') no-repeat center center;
        background-size: cover;
        color: white;
        position: relative;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
    }
    
    .jumbotron-content {
        background-color: rgba(0, 0, 0, 0.6);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
    }
    
    .display-4 {
        font-size: 2.5rem;
        font-weight: 300;
    }
    
    .lead {
        font-size: 1.25rem;
        font-weight: 300;
    }
    
    .btn {
        display: inline-block;
        font-weight: 400;
        text-align: center;
        vertical-align: middle;
        cursor: pointer;
        border: 1px solid transparent;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
        border-radius: 0.25rem;
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    
    .btn-primary {
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1.25rem;
        line-height: 1.5;
        border-radius: 0.3rem;
    }
    
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        max-width: 1140px;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .mb-5 {
        margin-bottom: 3rem !important;
    }
    
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-right: -15px;
        margin-left: -15px;
    }
    
    .col-12 {
        flex: 0 0 100%;
        max-width: 100%;
        padding-right: 15px;
        padding-left: 15px;
    }
    
    .col-md-3 {
        flex: 0 0 25%;
        max-width: 25%;
        padding-right: 15px;
        padding-left: 15px;
        box-sizing: border-box;
    }
    
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
    
    .card-img-top {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-top-left-radius: calc(0.25rem - 1px);
        border-top-right-radius: calc(0.25rem - 1px);
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
        color: #ffc107; /* Bootstrap's warning color, good for stars */
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
    
    h2 {
        margin-top: 0;
        margin-bottom: 1rem;
        font-weight: 500;
        line-height: 1.2;
        color: #333;
        position: relative;
        padding-bottom: 10px;
    }
    
    h2:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background-color: #007bff;
    }
    
    section {
        margin-bottom: 2rem;
    }
    
    /* Responsive CSS */
    @media (max-width: 768px) {
        .col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 576px) {
        .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
    
    .h-100 {
        height: 100% !important;
    }

    /* Custom style for Used badge with black text */
    .used-badge {
        color: black !important;
        font-weight: bold;
    }
    
    /* Text colors */
    .text-success {
        color: #28a745 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
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
    
    .btn-secondary {
        color: #fff;
        background-color: #6c757d;
        border-color: #6c757d;
    }
</style>

<div class="jumbotron">
    <div class="jumbotron-content">
        <h1 class="display-4">Welcome to Online Book Trading System</h1>
        <p class="lead">Discover, buy, and sell books with ease.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary btn-lg">Sign Up Now</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <!-- Recommended Books Section -->
    <?php if (!empty($recommendedBooks)): ?>
    <section class="mb-5">
        <h2 class="mb-4">Recommended Books</h2>
        <div class="row">
            <?php foreach ($recommendedBooks as $book): ?>
                <div class="col-md-3 mb-4">
                    <div class="card book-card h-100">
                        <img src="assets/images/<?php echo $book['image']; ?>" class="card-img-top book-image" alt="<?php echo $book['title']; ?>">
                        <div class="card-body">
                            <h5 class="card-title" title="<?php echo $book['title']; ?>"><?php echo $book['title']; ?></h5>
                            <p class="card-text book-author" title="by <?php echo $book['author']; ?>">by <?php echo $book['author']; ?></p>
                            <div class="card-text rating">
                                <div class="stars">
                                <?php 
                                $avgRating = isset($book['avg_rating']) ? $book['avg_rating'] : 0;
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $avgRating) {
                                        echo '<i class="bi bi-star-fill"></i>';
                                    } else if ($i <= $avgRating + 0.5) {
                                        echo '<i class="bi bi-star-half"></i>';
                                    } else {
                                        echo '<i class="bi bi-star"></i>';
                                    }
                                }
                                ?>
                                </div>
                                <span class="rating-count"><?php echo round($avgRating, 1); ?></span>
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
    </section>
    <?php endif; ?>

    <!-- All Available Books -->
    <section>
        <h2 class="mb-4">Available Books</h2>
        <div class="row">
            <?php if (empty($books)): ?>
                <div class="col-12">
                    <p>No books available at the moment.</p>
                </div>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
    // JavaScript for enhanced card functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips for truncated text if needed
        const bookTitles = document.querySelectorAll('.card-title');
        const bookAuthors = document.querySelectorAll('.book-author');
        
        // Add any additional card interactivity here if needed
    });
</script>

<?php require_once 'includes/footer.php'; ?>