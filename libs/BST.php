<?php
class BookNode {
    public $price;
    public $book;
    public $left;
    public $right;
    
    function __construct($price, $book) {
        $this->price = $price;
        $this->book = $book;
        $this->left = null;
        $this->right = null;
    }
}

class BookBST {
    public $root = null;
    
    function insert($price, $book) {
        $this->root = $this->insertNode($this->root, $price, $book);
    }
    
    private function insertNode($node, $price, $book) {
        if (!$node) return new BookNode($price, $book);
        if ($price < $node->price) {
            $node->left = $this->insertNode($node->left, $price, $book);
        } else {
            $node->right = $this->insertNode($node->right, $price, $book);
        }
        return $node;
    }
    
    function searchByPriceRange($minPrice, $maxPrice) {
        $result = [];
        $this->searchRange($this->root, $minPrice, $maxPrice, $result);
        return $result;
    }
    
    private function searchRange($node, $minPrice, $maxPrice, &$result) {
        if (!$node) return;
        if ($node->price >= $minPrice) $this->searchRange($node->left, $minPrice, $maxPrice, $result);
        if ($node->price >= $minPrice && $node->price <= $maxPrice) $result[] = $node->book;
        if ($node->price <= $maxPrice) $this->searchRange($node->right, $minPrice, $maxPrice, $result);
    }
    
    function inOrderTraversal($order = 'asc') {
        $result = [];
        $this->inOrder($this->root, $result);
        
        if ($order === 'desc') {
            return array_reverse($result);
        }
        
        return $result;
    }
    
    private function inOrder($node, &$result) {
        if (!$node) return;
        $this->inOrder($node->left, $result);
        $result[] = $node->book;
        $this->inOrder($node->right, $result);
    }
}
?>