</div>

    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>BookNest</h5>
                    <p>Your one-stop shop for buying and selling new and used books.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="index.php?page=books" class="text-white">Browse Books</a></li>
                        <li><a href="index.php?page=add_used_book" class="text-white">Sell Your Books</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <i class="fas fa-map-marker-alt mr-2"></i> Tribhuvan University<br>
                        <i class="fas fa-envelope mr-2"></i> info@booknest.com<br>
                        <i class="fas fa-phone mr-2"></i> +977 98XXXXXXXX
                    </address>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>&copy; <?php echo date('Y'); ?> BookNest. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
    <?php if (isset($is_admin) && $is_admin): ?>
    <script src="assets/js/admin.js"></script>
    <?php endif; ?>
</body>
</html>