<?php
class UserController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    public function login($username, $password) {
        $user = $this->user->getUserByUsername($username);
        if ($user && $user['password'] == $password) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
        } else {
            echo "Invalid login";
        }
    }

    public function register($username, $password, $email) {
        return $this->user->registerUser($username, $password, $email);
    }

    public function searchBooks($searchTerm) {
        $book = new Book($this->pdo);
        return $book->searchBooks($searchTerm);
    }
}
?>
