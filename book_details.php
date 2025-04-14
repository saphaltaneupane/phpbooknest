<?php
require_once 'includes/header.php';

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get book details
$book = getBookById($bookId);

// If book not found, redirect to homepage
if (!$book) {
    redirect('index.php');
}

// Handle rating submission
$ratingSubmitted = false;
if (isLoggedIn() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    $userId = $_SESSION['user_id'];
    $rating = (int)$_POST['rating'];
    $review = sanitize($_POST['review']);

    // Ensure both rating and review are provided
    if (empty($rating) || empty($review)) {
        $error = "Both rating and review are required.";
    } else {
        // Check if user already rated this book
        $checkQuery = "SELECT * FROM ratings WHERE user_id = $userId AND book_id = $bookId";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            // Update existing rating
            $updateQuery = "UPDATE ratings SET rating = $rating, review = '$review' WHERE user_id = $userId AND book_id = $bookId";
            mysqli_query($conn, $updateQuery);
        } else {
            // Insert new rating
            $insertQuery = "INSERT INTO ratings (user_id, book_id, rating, review) VALUES ($userId, $bookId, $rating, '$review')";
            mysqli_query($conn, $insertQuery);
        }

        $ratingSubmitted = true;
    }
}

// Check if the user has reviewed and rated the book
$userHasReviewed = false;
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $reviewCheckQuery = "SELECT * FROM ratings WHERE user_id = $userId AND book_id = $bookId";
    $reviewCheckResult = mysqli_query($conn, $reviewCheckQuery);
    $userHasReviewed = mysqli_num_rows($reviewCheckResult) > 0;
}

// Get book ratings and reviews
$ratingsQuery = "SELECT r.*, u.name as user_name FROM ratings r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.book_id = $bookId
                ORDER BY r.created_at DESC";
$ratingsResult = mysqli_query($conn, $ratingsQuery);
$ratings = [];
while ($row = mysqli_fetch_assoc($ratingsResult)) {
    $ratings[] = $row;
}

// Calculate average rating
$avgRating = getBookRating($bookId);
?>

<div class="row">
    <div class="col-md-4">
        <img src="assets/images/<?php echo $book['image']; ?>" class="img-fluid rounded" alt="<?php echo $book['title']; ?>">
    </div>
    <div class="col-md-8">
        <h2><?php echo $book['title']; ?></h2>
        <p class="text-muted">by <?php echo $book['author']; ?></p>
        
        <div class="mb-3">
            <?php 
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $avgRating) {
                    echo '<i class="fas fa-star text-warning"></i>';
                } else if ($i <= $avgRating + 0.5) {
                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                } else {
                    echo '<i class="far fa-star text-warning"></i>';
                }
            }
            echo " <span>(" . $avgRating . " out of 5)</span>";
            ?>
        </div>
        
        <p><strong>Price:</strong> Rs. <?php echo $book['price']; ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($book['status']); ?></p>
        
        <?php if ($book['is_old']): ?>
            <p><span class="badge bg-secondary">Used Book</span></p>
        <?php endif; ?>
        
        <p><?php echo $book['description']; ?></p>
        
        <?php if ($book['status'] === 'available'): ?>
            <?php if (isLoggedIn()): ?>
                <?php if ($userHasReviewed): ?>
                    <a href="checkout.php?id=<?php echo $book['id']; ?>" class="btn btn-primary mb-3">Buy Now</a>
                <?php else: ?>
                    <div class="alert alert-warning">You must review and rate this book before purchasing.</div>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary mb-3">Login to Buy</a>
            <?php endif; ?>
        <?php else: ?>
            <button class="btn btn-secondary mb-3" disabled>Not Available</button>
        <?php endif; ?>
    </div>
</div>

<!-- Reviews Section -->
<div class="row mt-5">
    <div class="col-12">
        <h3>Reviews and Ratings</h3>
        
        <?php if (isLoggedIn()): ?>
            <div class="card mb-4">
                <div class="card-header">Write a Review</div>
                <div class="card-body">
                    <?php if ($ratingSubmitted): ?>
                        <div class="alert alert-success">Your review has been submitted!</div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form action="book_details.php?id=<?php echo $bookId; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating1" value="1" required>
                                    <label class="form-check-label" for="rating1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                    <label class="form-check-label" for="rating2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                                    <label class="form-check-label" for="rating3">3</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                    <label class="form-check-label" for="rating4">4</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                    <label class="form-check-label" for="rating5">5</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Review</label>
                            <textarea class="form-control" id="review" name="review" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (empty($ratings)): ?>
            <p>No reviews yet. Be the first to leave a review!</p>
        <?php else: ?>
            <?php foreach ($ratings as $rating): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="card-title"><?php echo $rating['user_name']; ?></h5>
                            <div>
                                <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating['rating']) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-warning"></i>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <p class="card-text"><?php echo $rating['review']; ?></p>
                        <p class="card-text"><small class="text-muted">Posted on <?php echo date('M d, Y', strtotime($rating['created_at'])); ?></small></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>