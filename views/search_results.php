<h1>Search Results</h1>
<?php foreach ($searchResults as $book): ?>
    <div>
        <h3><?php echo $book['title']; ?></h3>
        <p>Author: <?php echo $book['author']; ?></p>
        <p>Description: <?php echo $book['description']; ?></p>
        <p>Price: <?php echo $book['price']; ?></p>
    </div>
<?php endforeach; ?>
