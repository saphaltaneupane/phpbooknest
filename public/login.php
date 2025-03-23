<?php
session_start();
include 'config/database.php';
include 'controllers/UserController.php';

$userController = new UserController($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $userController->login($username, $password);
}
?>
