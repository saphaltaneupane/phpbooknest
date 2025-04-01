<?php
class Order {
    // Create new order
    public static function createOrder($user_id, $book_id, $payment_method, $payment_status = 'pending', $transaction_id = null) {
        $user_id = escapeString($user_id);
        $book_id = escapeString($book_id);
        $payment_method = escapeString($payment_method);
        $payment_status = escapeString($payment_status);
        $transaction_id = escapeString($transaction_id);
        
        $sql = "INSERT INTO orders (user_id, book_id, payment_method, payment_status, order_status, transaction_id, created_at) 
                VALUES ('$user_id', '$book_id', '$payment_method', '$payment_status', 'pending', '$transaction_id', NOW())";
        
        $result = executeQuery($sql);
        
        if ($result) {
            // Update book status to sold
            Book::updateBookStatus($book_id, 'sold');
            return getLastInsertId();
        }
        
        return false;
    }
    
    // Update order payment status
    public static function updatePaymentStatus($order_id, $payment_status, $transaction_id = null) {
        $order_id = escapeString($order_id);
        $payment_status = escapeString($payment_status);
        $transaction_id = escapeString($transaction_id);
        
        $sql = "UPDATE orders SET payment_status = '$payment_status'";
        
        if ($transaction_id) {
            $sql .= ", transaction_id = '$transaction_id'";
        }
        
        $sql .= " WHERE id = '$order_id'";
        
        return executeQuery($sql);
    }
    
    // Update order status
    public static function updateOrderStatus($order_id, $order_status) {
        $order_id = escapeString($order_id);
        $order_status = escapeString($order_status);
        
        $sql = "UPDATE orders SET order_status = '$order_status' WHERE id = '$order_id'";
        return executeQuery($sql);
    }
    
    // Get order by ID
    public static function getOrderById($id) {
        $id = escapeString($id);
        $sql = "SELECT orders.*, books.title, books.price, users.username 
                FROM orders 
                JOIN books ON orders.book_id = books.id 
                JOIN users ON orders.user_id = users.id 
                WHERE orders.id = '$id'";
        return fetchOne($sql);
    }
    
    // Get all orders
    public static function getAllOrders() {
        $sql = "SELECT orders.*, books.title, books.price, users.username 
                FROM orders 
                JOIN books ON orders.book_id = books.id 
                JOIN users ON orders.user_id = users.id 
                ORDER BY orders.created_at DESC";
        return fetchAll($sql);
    }
    
    // Get orders by user
    public static function getOrdersByUser($user_id) {
        $user_id = escapeString($user_id);
        $sql = "SELECT orders.*, books.title, books.price 
                FROM orders 
                JOIN books ON orders.book_id = books.id 
                WHERE orders.user_id = '$user_id' 
                ORDER BY orders.created_at DESC";
        return fetchAll($sql);
    }
}
?>