<?php
$active_page = 'dashboard';
$page_title = 'Dashboard';
require_once 'views/layout/header.php';
?>

<div class="jumbotron">
    <h1 class="display-4">Welcome to BookNest, <?php echo $_SESSION['username']; ?>!</h1>
    <p class="lead">Buy and sell books easily. Find great deals on new and used books or sell your old books.</p>
    <hr class="my-4">
    <p>Get started by browsing our collection or list your books for sale.</p>
    <div class="btn-group">
        <a class="btn btn-primary btn-lg" href="index.php?page=books" role="button">Browse Books</a>
        <a class="btn btn-success btn-lg" href="index.php?page=add_used_book" role="button">Sell Books</a>
    </div>
</div>

<!-- Featured Books Section -->
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">Featured Books</h2>
    </div>
    
    <?php foreach ($books as $book): ?>
    <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm">
            <img src="assets/images/<?php