<?php
// Ensure no output is sent before this point
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if logged in user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Function to redirect
function redirect($url) {
    // Ensure no output is sent before calling header()
    if (headers_sent()) {
        echo "<script>window.location.href='$url';</script>";
        exit();
    }
    header("Location: $url");
    exit();
}

// Function to display error message
function displayError($message) {
    return "<div class='alert alert-danger'>$message</div>";
}

// Function to display success message
function displaySuccess($message) {
    return "<div class='alert alert-success'>$message</div>";
}

// Function to sanitize input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number (10 digits)
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

/**
 * Get all available books (status must be 'available')
 */
function getAvailableBooks() {
    global $conn;
    
    $query = "SELECT b.*, u.name as added_by_name 
              FROM books b 
              LEFT JOIN users u ON b.added_by = u.id 
              WHERE b.status = 'available'
              ORDER BY b.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $books = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
    }
    
    return $books;
}

/**
 * Get all books regardless of status (for admin)
 */
function getAllBooks() {
    global $conn;
    
    $query = "SELECT b.*, u.name as added_by_name 
              FROM books b 
              LEFT JOIN users u ON b.added_by = u.id 
              ORDER BY b.created_at DESC";
    
    $result = mysqli_query($conn, $query);
    $books = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }
    }
    
    return $books;
}

/**
 * Get book by ID
 */
function getBookById($id) {
    global $conn;
    
    $id = (int)$id; // Ensure it's an integer
    
    $query = "SELECT b.*, u.name as added_by_name 
              FROM books b 
              LEFT JOIN users u ON b.added_by = u.id 
              WHERE b.id = $id";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Function to get top rated books (4+ stars) as fallback recommendation.
 * @param int $limit The maximum number of recommendations to return.
 * @return array The recommended books.
 */
function getTopRatedBooks($limit = 5) {
    global $conn;

    // Query to fetch books with average rating of 4 or higher
    $query = "SELECT books.*, 
              COALESCE(AVG(ratings.rating), 0) as avg_rating, 
              COUNT(ratings.id) as rating_count 
              FROM books 
              LEFT JOIN ratings ON books.id = ratings.book_id 
              WHERE books.quantity > 0 AND books.status = 'available'
              GROUP BY books.id
              HAVING avg_rating >= 4
              ORDER BY avg_rating DESC, rating_count DESC
              LIMIT $limit";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        error_log("No high-rated books found or error in getTopRatedBooks: " . mysqli_error($conn));
        
        // If no books with rating ≥ 4, use any available books
        $fallbackQuery = "SELECT books.*, COALESCE(AVG(ratings.rating), 0) as avg_rating,
                         COUNT(ratings.id) as rating_count  
                         FROM books 
                         LEFT JOIN ratings ON books.id = ratings.book_id
                         WHERE books.quantity > 0 AND books.status = 'available' 
                         GROUP BY books.id
                         ORDER BY avg_rating DESC, books.id DESC
                         LIMIT $limit";
        $fallbackResult = mysqli_query($conn, $fallbackQuery);
        
        if (!$fallbackResult) {
            return [];
        }
        
        $books = [];
        while ($row = mysqli_fetch_assoc($fallbackResult)) {
            $books[] = $row;
        }
        
        return $books;
    }
    
    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

/**
 * Calculate similarity score between two books based on their attributes.
 * Enhanced version with stronger author, category, and price matching.
 * 
 * @param array $book1 First book data
 * @param array $book2 Second book data
 * @return float Similarity score
 */
function calculateBookSimilarity($book1, $book2) {
    $score = 0;
    
    // Author similarity (highest weight - 5 points, increased from 4)
    if (strtolower(trim($book1['author'])) === strtolower(trim($book2['author']))) {
        $score += 5; // Increased weight for author match
    }
    
    // Category/Genre similarity (3 points for exact category match)
    if (isset($book1['category_id']) && isset($book2['category_id']) && 
        $book1['category_id'] == $book2['category_id']) {
        $score += 3;
    }
    
    // Title keyword similarity
    $title1Keywords = extractKeywords($book1['title']);
    $title2Keywords = extractKeywords($book2['title']);
    $commonTitleWords = array_intersect($title1Keywords, $title2Keywords);
    $score += count($commonTitleWords) * 0.8; // 0.8 points per common word
    
    // Description keyword similarity
    if (!empty($book1['description']) && !empty($book2['description'])) {
        $desc1Keywords = extractKeywords($book1['description']);
        $desc2Keywords = extractKeywords($book2['description']);
        $commonDescWords = array_intersect($desc1Keywords, $desc2Keywords);
        $score += count($commonDescWords) * 0.3; // 0.3 points per common word
    }
    
    // Book type similarity (new/used)
    if (isset($book1['is_old']) && isset($book2['is_old']) && $book1['is_old'] == $book2['is_old']) {
        $score += 1;
    }
    
    // Price similarity (improved to consider price ranges better)
    if (isset($book1['price']) && isset($book2['price']) && 
        $book1['price'] > 0 && $book2['price'] > 0) {
        
        // Basic price ratio (as before)
        $priceRatio = min($book1['price'], $book2['price']) / max($book1['price'], $book2['price']);
        
        // Additional points if books are in similar price ranges
        $priceDiff = abs($book1['price'] - $book2['price']);
        $avgPrice = ($book1['price'] + $book2['price']) / 2;
        $percentDiff = $priceDiff / $avgPrice;
        
        if ($percentDiff < 0.1) {  // Less than 10% difference
            $score += 1.5;  // Very similar prices
        } else if ($percentDiff < 0.25) {  // Less than 25% difference
            $score += 1;  // Similar prices
        }
        
        $score += $priceRatio; // Add the base ratio score too
    }
    
    return $score;
}

/**
 * Extract meaningful keywords from a string.
 * @param string $string The input string
 * @return array Array of keywords
 */
function extractKeywords($string) {
    if (empty($string)) return [];
    
    // Convert to lowercase
    $string = strtolower($string);
    
    // Remove punctuation
    $string = preg_replace('/[^\w\s]/', ' ', $string);
    
    // Split into words
    $words = preg_split('/\s+/', $string, -1, PREG_SPLIT_NO_EMPTY);
    
    // Remove common stop words
    $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'with', 'by', 'of', 
                 'is', 'are', 'was', 'were', 'this', 'that', 'these', 'those', 'it', 'they', 'them', 
                 'their', 'his', 'her', 'he', 'she', 'we', 'you', 'i', 'am', 'been', 'being', 'have', 'has',
                 'from', 'will', 'would', 'could', 'should', 'can', 'may', 'might', 'must', 'shall'];
    $words = array_diff($words, $stopWords);
    
    // Filter out very short words (less than 3 characters)
    $words = array_filter($words, function($word) {
        return strlen($word) >= 3;
    });
    
    // Return unique keywords
    return array_values(array_unique($words));
}

/**
 * Get books that a user has purchased or rated highly,
 * organized by author and category for better pattern recognition.
 * 
 * @param int $userId The user ID
 * @return array Array of preferred books with metadata
 */
function getUserPreferredBooks($userId) {
    global $conn;
    $userBooks = [];
    $preferredAuthors = [];
    $preferredCategories = [];
    $priceRanges = [];
    
    // Get books user has purchased with COMPLETED orders and payment status
    $query = "SELECT DISTINCT b.* FROM books b
              JOIN order_items oi ON b.id = oi.book_id
              JOIN orders o ON oi.order_id = o.id
              WHERE o.user_id = $userId
              AND o.status IN ('completed', 'processing', 'shipped')
              AND o.payment_status = 'completed'";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $userBooks[$row['id']] = $row;
            
            // Track preferred authors
            $author = strtolower(trim($row['author']));
            if (!isset($preferredAuthors[$author])) {
                $preferredAuthors[$author] = 0;
            }
            $preferredAuthors[$author]++;
            
            // Track preferred categories
            $categoryId = $row['category_id'] ?? 0;
            if ($categoryId > 0) {
                if (!isset($preferredCategories[$categoryId])) {
                    $preferredCategories[$categoryId] = 0;
                }
                $preferredCategories[$categoryId]++;
            }
            
            // Track price ranges
            $price = (float)$row['price'];
            $priceRanges[] = $price;
        }
    } else {
        error_log("Error getting purchased books: " . mysqli_error($conn));
    }
    
    // Get books user has rated 4 or higher (genuinely preferred)
    $query = "SELECT DISTINCT b.* FROM books b
              JOIN ratings r ON b.id = r.book_id
              WHERE r.user_id = $userId
              AND r.rating >= 4";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $userBooks[$row['id']] = $row;
            
            // Also track authors and categories from highly rated books
            $author = strtolower(trim($row['author']));
            if (!isset($preferredAuthors[$author])) {
                $preferredAuthors[$author] = 0;
            }
            $preferredAuthors[$author]++;
            
            $categoryId = $row['category_id'] ?? 0;
            if ($categoryId > 0) {
                if (!isset($preferredCategories[$categoryId])) {
                    $preferredCategories[$categoryId] = 0;
                }
                $preferredCategories[$categoryId]++;
            }
            
            // Track price ranges
            $price = (float)$row['price'];
            $priceRanges[] = $price;
        }
    } else {
        error_log("Error getting rated books: " . mysqli_error($conn));
    }
    
    // Calculate preferred price range if we have data
    $minPrice = $maxPrice = $avgPrice = 0;
    if (!empty($priceRanges)) {
        $minPrice = min($priceRanges);
        $maxPrice = max($priceRanges);
        $avgPrice = array_sum($priceRanges) / count($priceRanges);
    }
    
    // Log for debugging
    error_log("Found " . count($userBooks) . " preferred books for user $userId");
    
    // Return books with metadata about user preferences
    return [
        'books' => array_values($userBooks),
        'authors' => $preferredAuthors,
        'categories' => $preferredCategories,
        'priceRange' => [
            'min' => $minPrice,
            'max' => $maxPrice,
            'avg' => $avgPrice
        ]
    ];
}

/**
 * Function to get content-based book recommendations for a user.
 * Enhanced to better utilize user preferences from purchase history.
 * 
 * @param int $userId The ID of the user.
 * @param int $limit The maximum number of recommendations to return.
 * @return array The recommended books.
 */
function getContentBasedRecommendations($userId, $limit = 5) {
    global $conn;
    
    // Get books this user has purchased or rated highly, with preference data
    $userPreferences = getUserPreferredBooks($userId);
    $userPreferredBooks = $userPreferences['books'];
    $preferredAuthors = $userPreferences['authors'];
    $preferredCategories = $userPreferences['categories'];
    $priceRange = $userPreferences['priceRange'];
    
    // If no preferred books, return top rated books as fallback
    if (empty($userPreferredBooks)) {
        error_log("User $userId has no preferred books, returning top rated books");
        return getTopRatedBooks($limit);
    }
    
    // Get all available books
    $query = "SELECT * FROM books WHERE quantity > 0 AND status = 'available'";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Error fetching books for recommendations: " . mysqli_error($conn));
        return getTopRatedBooks($limit);
    }
    
    $availableBooks = [];
    $userBookIds = array_column($userPreferredBooks, 'id');
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Skip books the user already has interacted with
        if (!in_array($row['id'], $userBookIds)) {
            $availableBooks[] = $row;
        }
    }
    
    // If no available books, return top rated
    if (empty($availableBooks)) {
        error_log("No available books found for recommendations");
        return getTopRatedBooks($limit);
    }
    
    // Calculate similarity scores for each available book
    $scoredBooks = [];
    foreach ($availableBooks as $book) {
        $totalScore = 0;
        $bestScore = 0;
        
        // Add bonus points for preferred authors (from purchase history)
        $bookAuthor = strtolower(trim($book['author']));
        if (isset($preferredAuthors[$bookAuthor]) && $preferredAuthors[$bookAuthor] > 0) {
            // More purchases of this author = higher bonus
            $authorBonus = min(3, $preferredAuthors[$bookAuthor]) * 2;
            $totalScore += $authorBonus;
        }
        
        // Add bonus points for preferred categories
        $bookCategory = $book['category_id'] ?? 0;
        if ($bookCategory > 0 && isset($preferredCategories[$bookCategory]) && $preferredCategories[$bookCategory] > 0) {
            // More purchases in this category = higher bonus
            $categoryBonus = min(3, $preferredCategories[$bookCategory]) * 1.5;
            $totalScore += $categoryBonus;
        }
        
        // Add bonus for books in user's preferred price range
        if ($priceRange['avg'] > 0) {
            $bookPrice = (float)$book['price'];
            $percentDiff = abs($bookPrice - $priceRange['avg']) / $priceRange['avg'];
            
            if ($percentDiff < 0.15) {  // Within 15% of average spending
                $totalScore += 2;
            } else if ($percentDiff < 0.30) {  // Within 30% of average spending
                $totalScore += 1;
            }
        }
        
        // Find best matching score against any user preferred book
        foreach ($userPreferredBooks as $userBook) {
            $similarityScore = calculateBookSimilarity($book, $userBook);
            $totalScore += $similarityScore;
            
            if ($similarityScore > $bestScore) {
                $bestScore = $similarityScore;
            }
        }
        
        // Calculate final score with weighted components
        $avgScore = count($userPreferredBooks) > 0 ? 
                   $totalScore / count($userPreferredBooks) : 0;
        
        // Weighted formula: 40% average similarity + 60% best match similarity
        $finalScore = ($avgScore * 0.4) + ($bestScore * 0.6);
        
        // Store the book with its similarity score
        $book['similarity_score'] = $finalScore;
        $scoredBooks[] = $book;
    }
    
    // Sort books by similarity score (descending)
    usort($scoredBooks, function($a, $b) {
        return $b['similarity_score'] <=> $a['similarity_score'];
    });
    
    // Get top N recommendations
    $recommendations = array_slice($scoredBooks, 0, $limit);
    
    // If we couldn't get enough recommendations, fill with top rated books
    if (count($recommendations) < $limit) {
        $existingIds = array_column($recommendations, 'id');
        $additionalBooks = getTopRatedBooks($limit);
        
        foreach ($additionalBooks as $book) {
            if (!in_array($book['id'], $existingIds)) {
                $recommendations[] = $book;
                $existingIds[] = $book['id'];
                if (count($recommendations) >= $limit) {
                    break;
                }
            }
        }
    }
    
    // Log recommendations for debugging
    $recIds = array_column($recommendations, 'id');
    error_log("Recommending books: " . implode(', ', $recIds) . " for user $userId");
    
    return $recommendations;
}

// Function to get user by ID
function getUserById($userId) {
    global $conn;
    $query = "SELECT * FROM users WHERE id = '$userId'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

// Function to get all users (for admin)
function getAllUsers() {
    global $conn;
    $query = "SELECT * FROM users WHERE is_admin = 0";
    $result = mysqli_query($conn, $query);
    $users = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * Update book quantity after confirmed purchase.
 * 
 * @param int $bookId The ID of the book.
 * @param int $orderQuantity The quantity being ordered.
 * @return bool Success status.
 */
function updateBookQuantity($bookId, $orderQuantity = 1) {
    global $conn;
    
    // Log the function call for debugging
    error_log("Updating book quantity: Book ID=$bookId, Quantity=$orderQuantity");
    
    // Get current book information
    $query = "SELECT quantity, status FROM books WHERE id = $bookId";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        error_log("Query error in updateBookQuantity: " . mysqli_error($conn));
        return false;
    }
    
    if (mysqli_num_rows($result) === 0) {
        error_log("Book ID $bookId not found");
        return false;
    }
    
    $book = mysqli_fetch_assoc($result);
    $currentQuantity = (int)$book['quantity'];
    
    // Calculate new quantity
    $newQuantity = $currentQuantity - $orderQuantity;
    
    // Prevent negative quantity
    if ($newQuantity < 0) {
        $newQuantity = 0;
    }
    
    // Determine new status
    $newStatus = $newQuantity > 0 ? 'available' : 'sold';
    
    // Update book quantity and status
    $updateQuery = "UPDATE books SET quantity = $newQuantity, status = '$newStatus' WHERE id = $bookId";
    $updateResult = mysqli_query($conn, $updateQuery);
    
    if (!$updateResult) {
        error_log("Update error in updateBookQuantity: " . mysqli_error($conn));
    }
    
    return $updateResult;
}

/**
 * Decrement the quantity of books in an order.
 * If quantity reaches 0 or less, update book status to 'sold'.
 * 
 * @param int $orderId The ID of the order.
 */
function decrementBookQuantity($orderId) {
    global $conn;
    $query = "SELECT book_id, quantity FROM order_items WHERE order_id = $orderId";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $bookId = $row['book_id'];
        $orderQuantity = $row['quantity'] ?? 1;
        
        // Get current quantity
        $qtyQuery = "SELECT quantity FROM books WHERE id = $bookId";
        $qtyResult = mysqli_query($conn, $qtyQuery);
        $book = mysqli_fetch_assoc($qtyResult);
        
        if ($book) {
            $currentQuantity = (int)$book['quantity'];
            
            // Calculate new quantity
            $newQuantity = $currentQuantity - $orderQuantity;
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }
            
            // Update quantity and status if needed
            if ($newQuantity == 0) {
                $updateQuery = "UPDATE books SET quantity = 0, status = 'sold' WHERE id = $bookId";
            } else {
                $updateQuery = "UPDATE books SET quantity = $newQuantity WHERE id = $bookId";
            }
            
            mysqli_query($conn, $updateQuery);
        }
    }
}

/**
 * Function to get book rating
 */
function getBookRating($bookId) {
    global $conn;
    $query = "SELECT AVG(rating) as avg_rating FROM ratings WHERE book_id = '$bookId'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return round($row['avg_rating'], 1);
}

// Function to search books
function searchBooks($keyword) {
    global $conn;
    $keyword = sanitize($keyword);
    $query = "SELECT * FROM books WHERE status = 'available' AND (title LIKE '%$keyword%' OR author LIKE '%$keyword%')";
    $result = mysqli_query($conn, $query);
    $books = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $books[] = $row;
    }
    
    return $books;
}

// Function to get user orders
function getUserOrders($userId) {
    global $conn;
    $query = "SELECT * FROM orders WHERE user_id = '$userId' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $orders = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    return $orders;
}

// Function to get order items
function getOrderItems($orderId) {
    global $conn;
    $query = "SELECT oi.*, b.title, b.author FROM order_items oi 
              JOIN books b ON oi.book_id = b.id 
              WHERE oi.order_id = '$orderId'";
    $result = mysqli_query($conn, $query);
    $items = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    
    return $items;
}

/**
 * Update book status to 'sold' when the order is completed.
 * 
 * @param int $orderId The ID of the order.
 */
function updateBookStatusToSold($orderId) {
    global $conn;
    $query = "SELECT book_id FROM order_items WHERE order_id = $orderId";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        $bookId = $row['book_id'];
        // Check quantity before updating status
        $qtyQuery = "SELECT quantity FROM books WHERE id = $bookId";
        $qtyResult = mysqli_query($conn, $qtyQuery);
        $book = mysqli_fetch_assoc($qtyResult);
        $quantity = $book ? (int)$book['quantity'] : 0;
        if ($quantity == 0) {
            $updateQuery = "UPDATE books SET status = 'sold' WHERE id = $bookId";
            mysqli_query($conn, $updateQuery);
        }
    }
}

/**
 * Check if a user has purchased a book with completed status.
 * 
 * @param int $userId The ID of the user.
 * @param int $bookId The ID of the book.
 * @return bool True if the user has purchased the book with completed status, false otherwise.
 */
function hasCompletedPurchase($userId, $bookId) {
    global $conn;
    
    $query = "SELECT o.id FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = $userId 
              AND oi.book_id = $bookId 
              AND o.payment_status = 'completed' 
              AND o.status = 'completed'";
    
    $result = mysqli_query($conn, $query);
    
    return mysqli_num_rows($result) > 0;
}

/**
 * Delete a user by ID
 * 
 * @param int $userId The ID of the user to delete
 * @return bool True if successful, false otherwise
 */
function deleteUser($userId) {
    global $conn;
    
    // Convert to integer for security
    $userId = (int)$userId;
    
    // Begin transaction to ensure data integrity
    mysqli_begin_transaction($conn);
    
    try {
        // First, delete any related records
        // Delete user's orders and order items
        $orderQuery = "SELECT id FROM orders WHERE user_id = $userId";
        $orderResult = mysqli_query($conn, $orderQuery);
        
        if ($orderResult) {
            while ($order = mysqli_fetch_assoc($orderResult)) {
                $orderId = $order['id'];
                // Delete order items
                mysqli_query($conn, "DELETE FROM order_items WHERE order_id = $orderId");
            }
        }
        
        // Delete user's orders
        mysqli_query($conn, "DELETE FROM orders WHERE user_id = $userId");
        
        // Delete user's ratings/reviews
        mysqli_query($conn, "DELETE FROM ratings WHERE user_id = $userId");
        
        // Delete the user
        $deleteQuery = "DELETE FROM users WHERE id = $userId";
        $result = mysqli_query($conn, $deleteQuery);
        
        if ($result && mysqli_affected_rows($conn) > 0) {
            // Commit transaction
            mysqli_commit($conn);
            return true;
        } else {
            // Rollback if user deletion failed
            mysqli_rollback($conn);
            return false;
        }
    } catch (Exception $e) {
        // Rollback on any error
        mysqli_rollback($conn);
        return false;
    }
}
?>