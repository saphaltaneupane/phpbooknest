<?php
$active_page = 'login';
$page_title = 'Login';
require_once 'views/layout/header.php';
?>


<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-sign-in-alt mr-2"></i>Login to Your Account</h4>
            </div>
            <div class="card-body">
                <form action="index.php?page=login" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Don't have an account? <a href="index.php?page=register">Register Now</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>