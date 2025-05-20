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

// Check if the user has completed a purchase for this book
$userHasCompletedPurchase = false;
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    
    // Check if user has purchased this book with completed status
    $query = "SELECT o.id FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = $userId 
              AND oi.book_id = $bookId 
              AND o.payment_status = 'completed' 
              AND o.status = 'completed'";
    
    $result = mysqli_query($conn, $query);
    $userHasCompletedPurchase = mysqli_num_rows($result) > 0;
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
        
        // Refresh the page to update
        redirect("book_details.php?id=$bookId");
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

// Fetch category name for the book
$categoryName = '';
if (!empty($book['category_id'])) {
    $catId = (int)$book['category_id'];
    $catQuery = "SELECT name FROM categories WHERE id = $catId LIMIT 1";
    $catResult = mysqli_query($conn, $catQuery);
    if ($catRow = mysqli_fetch_assoc($catResult)) {
        $categoryName = $catRow['name'];
    }
}
?>

<style>
    /* Custom Book Details Page Styling */
    .book-details-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        margin-bottom: 50px;
    }

    .book-image-container {
        flex: 0 0 300px;
    }

    .book-image {
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .book-image:hover {
        transform: scale(1.02);
    }

    .book-info-container {
        flex: 1;
        min-width: 300px;
    }

    .book-title {
        font-size: 26px;
        color: #333;
        margin-bottom: 5px;
    }

    .book-author {
        font-size: 16px;
        color: #666;
        margin-bottom: 15px;
        font-style: italic;
    }

    .book-rating {
        margin-bottom: 20px;
    }

    .star-rating {
        color: #FFD700;
        font-size: 18px;
        margin-right: 5px;
    }

    .rating-text {
        font-size: 14px;
        color: #666;
    }

    .book-price {
        font-size: 22px;
        font-weight: bold;
        color: #5D5CDE;
        margin-bottom: 15px;
    }

    .book-availability {
        margin-bottom: 15px;
    }

    .availability-text {
        font-weight: bold;
    }

    .in-stock {
        color: #28a745;
    }

    .out-of-stock {
        color: #dc3545;
    }

    .hidden-quantity {
        color: #6c757d;
    }

    .used-badge {
        display: inline-block;
        background-color: #6c757d;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        margin-bottom: 15px;
    }

    .book-description {
        margin-bottom: 25px;
        line-height: 1.6;
        color: #444;
    }

    /* Quantity selector styling */
    .quantity-container {
        margin-bottom: 20px;
    }

    .quantity-label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .quantity-controls {
        display: flex;
        max-width: 150px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
    }

    .quantity-btn {
        width: 40px;
        background: #f5f5f5;
        border: none;
        color: #333;
        font-size: 18px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .quantity-btn:hover {
        background: #e0e0e0;
    }

    .quantity-input {
        width: 70px;
        border: none;
        text-align: center;
        font-size: 16px;
        padding: 8px 0;
        -moz-appearance: textfield;
    }

    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quantity-max {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .buy-btn {
        display: inline-block;
        background-color: #5D5CDE;
        color: white;
        padding: 10px 25px;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.2s, transform 0.2s;
        border: none;
        cursor: pointer;
    }

    .buy-btn:hover {
        background-color: #4a49b7;
        transform: translateY(-2px);
    }

    .buy-btn:disabled {
        background-color: #6c757d;
        cursor: not-allowed;
        transform: none;
    }

    /* Reviews section styling */
    .reviews-section {
        margin-top: 50px;
    }

    .reviews-title {
        font-size: 22px;
        margin-bottom: 20px;
        border-bottom: 2px solid #5D5CDE;
        padding-bottom: 10px;
        color: #333;
    }

    .review-form-container {
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .review-form-header {
        background-color: #5D5CDE;
        color: white;
        padding: 12px 20px;
        font-weight: bold;
    }

    .review-form-body {
        padding: 20px;
    }

    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    .info-message {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 10px 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .rating-options {
        margin-bottom: 15px;
    }

    .rating-label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
    }

    .radio-group {
        display: flex;
        gap: 15px;
    }

    .radio-option {
        display: flex;
        align-items: center;
    }

    .radio-input {
        margin-right: 5px;
    }

    .review-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        margin-bottom: 15px;
        min-height: 100px;
        font-family: inherit;
    }

    .review-textarea:focus {
        border-color: #5D5CDE;
        outline: none;
    }

    .review-submit-btn {
        background-color: #5D5CDE;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.2s;
    }

    .review-submit-btn:hover {
        background-color: #4a49b7;
    }

    .helper-text {
        font-size: 12px;
        color: #666;
        margin-top: 10px;
    }

    /* Updated review card styling - avoiding black */
    .review-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
        background-color: #fcfcfc;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(93, 92, 222, 0.08);
    }

    .review-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(93, 92, 222, 0.15);
    }

    .review-card.user-review {
        border-color: #5D5CDE;
        border-width: 2px;
        background-color: #f8f8ff;
    }

    .review-card-body {
        padding: 20px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        border-bottom: 1px solid #eaeaea;
        padding-bottom: 10px;
    }

    .reviewer-name {
        font-weight: bold;
        font-size: 16px;
        margin: 0;
        color: #5D5CDE;
    }

    .user-badge {
        background-color: #5D5CDE;
        color: white;
        font-size: 12px;
        padding: 3px 8px;
        border-radius: 20px;
        margin-left: 10px;
    }

    .review-content {
        line-height: 1.6;
        margin-bottom: 10px;
        color: #555;
    }

    .review-date {
        font-size: 12px;
        color: #888;
        border-top: 1px dotted #eaeaea;
        padding-top: 8px;
    }
    
    /* For dark mode compatibility */
    @media (prefers-color-scheme: dark) {
        .book-title {
            color:black;
        }
        
        .book-author {
            color:black;
        }
        
        .book-description {
            color:black;
        }
        
        .review-form-container {
            background-color: #343a40;
            border-color: #495057;
        }
        
        /* Updated dark mode card styling */
        .review-card {
            background-color: #2d3035;
            border-color: #495057;
            box-shadow: 0 2px 8px rgba(93, 92, 222, 0.15);
        }
        
        .review-card.user-review {
            background-color: #36355a;
            border-color: #5D5CDE;
        }
        
        .review-header {
            border-bottom-color: #495057;
        }
        
        .reviewer-name {
            color: #9191ee;
        }
        
        .review-content {
            color: #ced4da;
        }
        
        .review-date {
            color: #adb5bd;
            border-top-color: #495057;
        }
        
        .quantity-controls {
            border-color: #495057;
        }
        
        .quantity-btn {
            background: #495057;
            color: #f8f9fa;
        }
        
        .quantity-btn:hover {
            background: #6c757d;
        }
        
        .quantity-input {
            background: #343a40;
            color: #f8f9fa;
        }
        
        .review-textarea {
            background-color: #343a40;
            border-color: #495057;
            color: #f8f9fa;
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .book-details-container {
            flex-direction: column;
        }
        
        .book-image-container {
            flex: 0 0 100%;
            max-width: 300px;
            margin: 0 auto;
        }
    }
</style>

<div class="book-details-container">
    <div class="book-image-container">
        <img src="assets/images/<?php echo $book['image']; ?>" class="book-image" alt="<?php echo $book['title']; ?>">
    </div>
    <div class="book-info-container">
        <h2 class="book-title"><?php echo $book['title']; ?></h2>
        <p class="book-author">by <?php echo $book['author']; ?></p>
        
        <div class="book-rating">
            <?php 
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $avgRating) {
                    echo '<i class="fas fa-star star-rating"></i>';
                } else if ($i <= $avgRating + 0.5) {
                    echo '<i class="fas fa-star-half-alt star-rating"></i>';
                } else {
                    echo '<i class="far fa-star star-rating"></i>';
                }
            }
            echo " <span class='rating-text'>(" . $avgRating . " out of 5)</span>";
            ?>
        </div>
        
        <p class="book-price">Rs. <?php echo $book['price']; ?></p>
        
        <div class="book-availability">
            <span class="availability-text">Availability:</span> 
            <?php 
            if (isLoggedIn() && $_SESSION['user_id'] == $book['added_by']) {
                echo '<span class="hidden-quantity">Quantity hidden for your added book</span>';
            } else {
                if ($book['quantity'] > 0) {
                    echo '<span class="in-stock">' . $book['quantity'] . ' copies available</span>';
                } else {
                    echo '<span class="out-of-stock">Out of stock</span>';
                }
            }
            ?>
        </div>
        
        <?php if (isset($book['is_old']) && $book['is_old'] == 1): ?>
            <div class="used-badge">Used Book</div>
        <?php endif; ?>
        
        <div class="book-description"><?php echo $book['description']; ?></div>
        
        <?php if ($book['quantity'] > 0): ?>
            <div class="quantity-container">
                <label for="quantity" class="quantity-label">Quantity:</label>
                <div class="quantity-controls">
                    <button type="button" id="decrease-quantity" class="quantity-btn">-</button>
                    <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="<?php echo $book['quantity']; ?>">
                    <button type="button" id="increase-quantity" class="quantity-btn">+</button>
                </div>
                <div class="quantity-max">Maximum available: <?php echo $book['quantity']; ?></div>
            </div>
            <button id="add-to-cart-btn" class="cart-btn">Add to Cart</button>
            <div id="cart-message" class="cart-message cart-success"></div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const quantityInput = document.getElementById('quantity');
                    const decreaseBtn = document.getElementById('decrease-quantity');
                    const increaseBtn = document.getElementById('increase-quantity');
                    const addToCartBtn = document.getElementById('add-to-cart-btn');
                    const cartMessage = document.getElementById('cart-message');
                    const maxQuantity = <?php echo $book['quantity']; ?>;
                    
                    // Decrease quantity button
                    decreaseBtn.addEventListener('click', function() {
                        let quantity = parseInt(quantityInput.value);
                        if (quantity > 1) {
                            quantityInput.value = quantity - 1;
                        }
                    });
                    
                    // Increase quantity button
                    increaseBtn.addEventListener('click', function() {
                        let quantity = parseInt(quantityInput.value);
                        if (quantity < maxQuantity) {
                            quantityInput.value = quantity + 1;
                        }
                    });
                    
                    // Manual input change
                    quantityInput.addEventListener('change', function() {
                        let quantity = parseInt(this.value);
                        
                        // Validate quantity
                        if (isNaN(quantity) || quantity < 1) {
                            this.value = 1;
                        } else if (quantity > maxQuantity) {
                            this.value = maxQuantity;
                        }
                    });
                    
                    // Add to cart button
                    addToCartBtn.addEventListener('click', function() {
                        const quantity = parseInt(quantityInput.value);
                        const bookId = <?php echo $book['id']; ?>;
                        
                        // Send AJAX request to add item to cart
                        fetch('add_to_cart.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `book_id=${bookId}&quantity=${quantity}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                cartMessage.textContent = data.message;
                                cartMessage.style.display = 'block';
                                
                                // Update cart count in header if it exists
                                const cartCountElement = document.getElementById('cart-count');
                                if (cartCountElement) {
                                    cartCountElement.textContent = data.cartCount;
                                    cartCountElement.style.display = 'inline-block';
                                }
                                
                                // Hide message after 3 seconds
                                setTimeout(() => {
                                    cartMessage.style.display = 'none';
                                }, 3000);
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while adding to cart.');
                        });
                    });
                });
            </script>
        <?php else: ?>
            <button class="cart-btn" disabled>Out of Stock</button>
        <?php endif; ?>
    </div>
</div>

<div class="book-details">
    <p><strong>Author:</strong> <?php echo $book['author']; ?></p>
    <p><strong>Category:</strong>
        <?php if (!empty($categoryName)): ?>
            <a href="category.php?id=<?php echo $book['category_id']; ?>"><?php echo htmlspecialchars($categoryName); ?></a>
        <?php else: ?>
            <span>Unknown</span>
        <?php endif; ?>
    </p>
    <p><strong>Price:</strong> Rs. <?php echo $book['price']; ?></p>
    <p><strong>Status:</strong> <?php echo ucfirst($book['status']); ?></p>
    <?php if (!empty($book['is_old'])): ?>
        <p><strong>Condition:</strong> <span class="badge bg-secondary">Used</span></p>
    <?php endif; ?>
</div>

<!-- Reviews Section -->
<div class="reviews-section">
    <h3 class="reviews-title">Reviews and Ratings</h3>
    
    <?php if (isLoggedIn()): ?>
        <?php if ($userHasCompletedPurchase): ?>
            <div class="review-form-container">
                <div class="review-form-header">
                    <?php if (!$userHasReviewed): ?>
                        <strong>Write a Review</strong>
                        <span>(Thank you for your purchase!)</span>
                    <?php else: ?>
                        <strong>Update Your Review</strong>
                    <?php endif; ?>
                </div>
                <div class="review-form-body">
                    <?php if ($ratingSubmitted): ?>
                        <div class="success-message">Your review has been submitted successfully!</div>
                    <?php elseif (isset($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form action="book_details.php?id=<?php echo $bookId; ?>" method="POST">
                        <div class="rating-options">
                            <label class="rating-label">Rating</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" name="rating" id="rating1" value="1" class="radio-input" required>
                                    <label for="rating1">1</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="rating" id="rating2" value="2" class="radio-input">
                                    <label for="rating2">2</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="rating" id="rating3" value="3" class="radio-input">
                                    <label for="rating3">3</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="rating" id="rating4" value="4" class="radio-input">
                                    <label for="rating4">4</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" name="rating" id="rating5" value="5" class="radio-input">
                                    <label for="rating5">5</label>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="review" class="rating-label">Review</label>
                            <textarea class="review-textarea" id="review" name="review" required placeholder="Share your thoughts about this book..."></textarea>
                        </div>
                        <button type="submit" class="review-submit-btn">
                            <?php echo $userHasReviewed ? 'Update Review' : 'Submit Review'; ?>
                        </button>
                        
                        <p class="helper-text">
                            <i class="fas fa-info-circle"></i> Your review helps other readers make informed decisions.
                        </p>
                    </form>
                </div>
            </div>
        <?php elseif (!$userHasCompletedPurchase): ?>
            <div class="info-message">
                <i class="fas fa-info-circle"></i> You can review this book after completing a purchase.
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php if (empty($ratings)): ?>
        <div class="info-message">
            <i class="fas fa-info-circle"></i> No reviews yet. Be the first to leave a review after purchasing!
        </div>
    <?php else: ?>
        <?php foreach ($ratings as $rating): ?>
            <div class="review-card <?php echo (isLoggedIn() && $rating['user_id'] == $_SESSION['user_id']) ? 'user-review' : ''; ?>">
                <div class="review-card-body">
                    <div class="review-header">
                        <h5 class="reviewer-name">
                            <?php echo $rating['user_name']; ?>
                            <?php if (isLoggedIn() && $rating['user_id'] == $_SESSION['user_id']): ?>
                                <span class="user-badge">Your Review</span>
                            <?php endif; ?>
                        </h5>
                        <div>
                            <?php 
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating['rating']) {
                                    echo '<i class="fas fa-star star-rating"></i>';
                                } else {
                                    echo '<i class="far fa-star star-rating"></i>';
                                }
                            ?>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <p class="review-content"><?php echo $rating['review']; ?></p>
                    <p class="review-date">Posted on <?php echo date('M d, Y', strtotime($rating['created_at'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>