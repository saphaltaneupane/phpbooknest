-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 08:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `booktrading`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT 'default-book.jpg',
  `status` enum('available','sold','pending') DEFAULT 'available',
  `quantity` int(11) DEFAULT 1,
  `added_by` int(11) DEFAULT NULL,
  `is_old` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `price`, `image`, `status`, `quantity`, `added_by`, `is_old`, `created_at`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', 'The Great Gatsby is a 1925 novel by American writer F. Scott Fitzgerald.', 450.00, 'default-book.jpg', 'pending', 5, NULL, 0, '2025-04-09 08:27:38'),
(2, 'To Kill a Mockingbird', 'Harper Lee', 'To Kill a Mockingbird is a novel by Harper Lee published in 1960.', 350.00, 'default-book.jpg', 'pending', 3, NULL, 0, '2025-04-09 08:27:38'),
(3, '1984', 'George Orwell', '1984 is a dystopian novel by George Orwell published in 1949.', 400.00, 'default-book.jpg', 'pending', 7, NULL, 0, '2025-04-09 08:27:38'),
(4, 'Pride and Prejudice', 'Jane Austen', 'Pride and Prejudice is a romantic novel by Jane Austen published in 1813.', 300.00, 'default-book.jpg', 'pending', 4, NULL, 0, '2025-04-09 08:27:38'),
(5, 'The Hobbit', 'J.R.R. Tolkien', 'The Hobbit is a childrens fantasy novel by J. R. R. Tolkien.', 500.00, 'default-book.jpg', 'pending', 2, NULL, 0, '2025-04-09 08:27:38'),
(6, 'A Brief History Of Time', 'Stephen Hawking', 'A Brief History of Time&quot; by Stephen Hawking', 2000.00, '1744249208_a brief history of time.jpeg', 'available', 25, 2, 1, '2025-04-10 01:40:08'),
(7, 'Harry Potter', 'J.K rowling', '', 1000.00, '1744266759_harry potter.jpeg', 'available', 5, 2, 1, '2025-04-10 06:32:39');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `purchase_order_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','khalti') NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `purchase_order_id`, `user_id`, `total_amount`, `payment_method`, `payment_status`, `status`, `created_at`, `transaction_id`) VALUES
(6, NULL, 2, 500.00, 'cash', 'pending', '', '2025-04-10 06:13:01', NULL),
(7, NULL, 3, 1000.00, 'cash', 'pending', 'completed', '2025-04-10 06:40:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(6, 6, 5, 1, 500.00),
(7, 7, 7, 1, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `book_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 1, 5, 'Excellent book!', '2025-04-09 08:27:38'),
(2, 1, 2, 4, 'Very good read', '2025-04-09 08:27:38'),
(3, 1, 3, 4, 'Thought-provoking', '2025-04-09 08:27:38'),
(4, 1, 4, 5, 'Classic masterpiece', '2025-04-09 08:27:38'),
(5, 1, 5, 4, 'Amazing fantasy story', '2025-04-09 08:27:38'),
(6, 3, 6, 3, 'okayy', '2025-04-10 02:09:05'),
(7, 2, 5, 4, 'its gooding', '2025-04-10 06:12:53'),
(8, 3, 7, 5, 'its good', '2025-04-10 06:40:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `address`, `is_admin`, `created_at`) VALUES
(1, 'admin', 'admin@booktrading.com', '9800000000', 'admin123', NULL, 1, '2025-04-09 08:27:38'),
(2, 'Nila Neupane', 'neupanesaphalta@gmail.com', '9848591283', 'nilaneupane123', 'Kathmandu', 0, '2025-04-09 08:48:17'),
(3, 'Krishna Maya', 'krishnamaya@gmail.com', '9848591283', 'krishnamaya123', 'Kathmandu', 0, '2025-04-10 02:00:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
