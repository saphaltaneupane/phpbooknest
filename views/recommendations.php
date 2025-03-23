<h1>Recommended Books</h1>
<?php foreach ($recommendations as $book): ?>
    <div>
        <h3><?php echo $book['title']; ?></h3>
        <p>Author: <?php echo $book['author']; ?></p>
        <p>Description: <?php echo $book['description']; ?></p>
    </div>
<?php endforeach; ?>
