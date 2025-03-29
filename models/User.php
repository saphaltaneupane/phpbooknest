<?php
class User {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
        $result = mysqli_query($this->conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }
    
    public function register($username, $email, $password, $full_name, $address = null, $phone = null) {
        $query = "INSERT INTO users (username, email, password, full_name, address, phone) 
                  VALUES ('$username', '$email', '$password', '$full_name', '$address', '$phone')";
        
        if (mysqli_query($this->conn, $query)) {
            return mysqli_insert_id($this->conn);
        } else {
            return false;
        }
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = $id";
        $result = mysqli_query($this->conn, $query);
        
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            return false;
        }
    }
    
    public function getAllUsers() {
        $query = "SELECT * FROM users ORDER BY id DESC";
        $result = mysqli_query($this->conn, $query);
        $users = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    public function updateUser($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = '$value', ";
        }
        $fields = rtrim($fields, ', ');
        
        $query = "UPDATE users SET $fields WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }
    
    public function deleteUser($id) {
        $query = "DELETE FROM users WHERE id = $id";
        return mysqli_query($this->conn, $query);
    }
}
?>