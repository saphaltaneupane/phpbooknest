<?php
class User {
    // Get user by ID
    public static function getUserById($id) {
        $id = escapeString($id);
        $sql = "SELECT * FROM users WHERE id = '$id'";
        return fetchOne($sql);
    }
    
    // Get user by username
    public static function getUserByUsername($username) {
        $username = escapeString($username);
        $sql = "SELECT * FROM users WHERE username = '$username'";
        return fetchOne($sql);
    }
    
    // Register new user
    public static function register($username, $password, $email) {
        $username = escapeString($username);
        $email = escapeString($email);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, email, role, created_at) 
                VALUES ('$username', '$hashed_password', '$email', 'user', NOW())";
        
        return executeQuery($sql);
    }
    
    // Login validation with detailed debugging
    public static function login($username, $password) {
        $user = self::getUserByUsername($username);
        
        if ($user) {
            // Add detailed debugging for password verification
            $password_match = password_verify($password, $user['password']);
            
            // For debugging - remove in production
            error_log("Login attempt: " . $username);
            error_log("Password hash in DB: " . $user['password']);
            error_log("Password match result: " . ($password_match ? "true" : "false"));
            
            if ($password_match) {
                return $user;
            }
        }
        
        return false;
    }
    
    // Get all users (for admin)
    public static function getAllUsers() {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        return fetchAll($sql);
    }
    
    // Update user role
    public static function updateUserRole($id, $role) {
        $id = escapeString($id);
        $role = escapeString($role);
        
        $sql = "UPDATE users SET role = '$role' WHERE id = '$id'";
        return executeQuery($sql);
    }
    
    // Delete user
    public static function deleteUser($id) {
        $id = escapeString($id);
        $sql = "DELETE FROM users WHERE id = '$id'";
        return executeQuery($sql);
    }
}
?>