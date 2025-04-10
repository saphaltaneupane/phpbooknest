<?php
require_once 'includes/header.php';

$keyword = isset($_GET['keyword']) ? sanitize($_GET['keyword']) : '';
$books = [];

if (!empty($keyword)) {
    $books = searchBooks($keyword);
}
?>

<div class="row">
    <div class="col-12">
        <h2>Search Results for "<?php echo $keyword; ?>"</h2>
        
        <?php if (empty($books)): ?>
            <div class="alert alert-info">No books found matching your search.</div>
        <?php else: ?>
            <div class="row mt-4">
                <?php foreach ($books as $book): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card book-card h-100">
                            <img src="assets/images/<?php echo $book['image']; ?>" class="card-img-top book-image" alt="<?php echo $book['title']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                <p class="card-text book-author">by <?php echo $book['author']; ?></p>
                                <p class="card-text rating">
                                    <?php 
                                    $rating = getBookRating($book['id']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else if ($i <= $rating + 0.5) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    echo " (" . $rating . ")";
                                    ?>
                                </p>
                                <p class="card-text book-price">Rs. <?php echo $book['price']; ?></p>
                                <a href="book_details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>