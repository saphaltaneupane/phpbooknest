<?php
require_once 'includes/header.php';

// Get available books
$books = getAvailableBooks();

// Get top rated books for recommendation
$recommendedBooks = getTopRatedBooks(4);
?>

<div class="jumbotron bg-light p-4 rounded mb-4" style="background: url('assets/images/banner.jpg') no-repeat center center; background-size: cover; color: white; position: relative;">
    <div class="container text-center" style="background-color: rgba(0, 0, 0, 0.6); padding: 20px; border-radius: 10px;">
        <h1 class="display-4">Welcome to Online Book Trading System</h1>
        <p class="lead">Discover, buy, and sell books with ease.</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn btn-primary btn-lg">Sign Up Now</a>
        <?php endif; ?>
    </div>
</div>

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
                        <h5 class="card-title"><?php echo $book['title']; ?></h5>
                        <p class="card-text book-author">by <?php echo $book['author']; ?></p>
                        <p class="card-text rating">
                            <?php 
                            $avgRating = $book['avg_rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avgRating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else if ($i <= $avgRating + 0.5) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            echo " (" . round($avgRating, 1) . ")";
                            ?>
                        </p>
                        <p class="card-text book-price">Rs. <?php echo $book['price']; ?></p>
                        <a href="book_details.php?id=<?php echo $book['id']; ?>" class="btn btn-primary">View Details</a>
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
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>