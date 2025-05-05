-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 10:13 AM
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
(13, 'User le rakheko', 'Saphalta', 'hii', 100.00, 'default-book.jpg', 'sold', 0, 3, 1, '2025-04-19 03:39:05'),
(14, 'admin le rakheko', 'sss', 'sss', 100.00, 'default-book.jpg', 'available', 1, NULL, 0, '2025-04-19 03:40:28'),
(15, 'admin', 'wwwww', 'www', 100.00, 'default-book.jpg', 'available', 6, NULL, 0, '2025-04-19 04:30:56'),
(16, 'admin 2', '2222', '222', 100.00, 'default-book.jpg', 'sold', 0, NULL, 0, '2025-04-19 05:13:28'),
(17, 'admin kept', 'sss', 'sssssss', 100.00, 'default-book.jpg', 'sold', 0, NULL, 0, '2025-04-20 11:06:40'),
(18, 'a Horror', 'J.K rowling', 'sss', 100.00, 'default-book.jpg', 'available', 1, NULL, 0, '2025-05-03 02:35:47'),
(19, 'a bhoot', 'J.K rowling', 'sss', 100.00, 'default-book.jpg', 'sold', 0, NULL, 0, '2025-05-03 02:36:10'),
(20, 'a horror 2', 'J.K rowling', 'ss', 100.00, 'default-book.jpg', 'available', 6, NULL, 0, '2025-05-03 02:40:35'),
(21, 'a ss', 'saphalta', '', 100.00, 'default-book.jpg', 'available', 5, NULL, 0, '2025-05-03 10:50:42'),
(22, 'a bbb', 'saphalta', 'sss', 100.00, 'default-book.jpg', 'available', 10, NULL, 0, '2025-05-03 10:50:59');

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
(6, NULL, 2, 500.00, 'cash', 'completed', 'completed', '2025-04-10 06:13:01', NULL),
(7, NULL, 3, 1000.00, 'cash', 'pending', 'completed', '2025-04-10 06:40:25', NULL),
(8, 'ORD-1744280731-8', 2, 1000.00, 'khalti', 'pending', 'pending', '2025-04-10 10:24:00', NULL),
(9, 'ORD-1744281506-9', 2, 2000.00, 'khalti', 'completed', 'completed', '2025-04-10 10:25:55', 'GCSfeiWCrpbaKfVvTUxMwR'),
(10, 'ORD-1744282013-10', 2, 1000.00, 'khalti', 'completed', 'pending', '2025-04-10 10:43:25', 'pCbwZRq3V3gw2xA8QS3yKk'),
(11, 'ORD-1744282032-11', 2, 500.00, 'khalti', 'completed', 'pending', '2025-04-10 10:47:11', '2pQmPdCbT6BZ36PwPFeqJX'),
(12, 'ORD-1744282613-12', 2, 100.00, 'khalti', 'completed', 'completed', '2025-04-10 10:53:52', 'LjFZzj72HTKbT8XyJUtrDY'),
(13, 'ORD-1744282625-13', 2, 100.00, 'khalti', 'completed', 'pending', '2025-04-10 10:57:05', NULL),
(14, 'ORD-1744283418-14', 2, 100.00, 'khalti', 'completed', 'completed', '2025-04-10 11:10:18', 'pe5RNKGWjjoMirGtghuEfd'),
(15, 'ORD-1744285102-15', 1, 100.00, 'khalti', 'pending', 'pending', '2025-04-10 11:38:21', NULL),
(16, NULL, 2, 100.00, 'cash', 'pending', 'completed', '2025-04-10 11:41:34', NULL),
(17, NULL, 2, 500.00, 'cash', 'pending', 'completed', '2025-04-10 11:42:00', NULL),
(18, NULL, 2, 300.00, 'cash', 'completed', 'completed', '2025-04-10 11:46:20', NULL),
(19, 'ORD-1744380366-19', 2, 400.00, 'khalti', 'pending', 'pending', '2025-04-11 14:06:06', NULL),
(20, 'ORD-1744380410-20', 2, 2000.00, 'khalti', 'pending', 'pending', '2025-04-11 14:06:50', NULL),
(21, 'ORD-1744380474-21', 3, 350.00, 'khalti', 'pending', 'pending', '2025-04-11 14:07:54', NULL),
(22, 'ORD-1744380907-22', 2, 2000.00, 'khalti', 'pending', 'completed', '2025-04-11 14:15:07', NULL),
(23, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-04-12 04:23:01', NULL),
(24, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-04-12 04:28:00', NULL),
(25, NULL, 3, 450.00, 'cash', 'completed', 'completed', '2025-04-12 04:29:56', NULL),
(26, 'ORD-1744460025-26', 2, 100.00, 'khalti', 'pending', 'pending', '2025-04-12 12:13:45', NULL),
(27, 'ORD-1744595854-27', 2, 100.00, 'khalti', 'completed', 'pending', '2025-04-14 01:57:34', '5bckhBQJDTHbg5sy6c6CoU'),
(28, 'ORD-1744596021-28', 2, 100.00, 'khalti', 'completed', 'completed', '2025-04-14 02:00:21', 'HAnrVJ6HjoCHmRJmvrvTqG'),
(29, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-18 12:27:32', NULL),
(30, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-18 12:42:04', NULL),
(31, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-18 12:46:47', NULL),
(32, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-18 12:56:02', NULL),
(33, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-18 15:26:23', NULL),
(34, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 02:22:29', NULL),
(35, NULL, 3, 2000.00, 'cash', 'pending', 'pending', '2025-04-19 02:23:24', NULL),
(36, NULL, 3, 2000.00, 'cash', 'pending', 'pending', '2025-04-19 02:30:23', NULL),
(37, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 02:30:46', NULL),
(38, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:19:23', NULL),
(39, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:19:43', NULL),
(40, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:19:52', NULL),
(41, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:20:07', NULL),
(42, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:20:14', NULL),
(43, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:20:23', NULL),
(44, NULL, 3, 400.00, 'cash', 'pending', 'pending', '2025-04-19 03:20:32', NULL),
(45, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 04:31:51', NULL),
(46, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 04:40:36', NULL),
(47, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 04:45:38', NULL),
(48, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-04-19 05:07:17', NULL),
(49, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 05:14:51', NULL),
(50, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 05:14:59', NULL),
(51, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-04-19 05:15:09', NULL),
(52, 'ORD-1745039902-52', 2, 100.00, 'khalti', 'completed', 'pending', '2025-04-19 05:18:22', 'USn2Dc7GC6dSTq7f72vTWT'),
(53, 'ORD-1745040155-53', 2, 100.00, 'khalti', 'completed', 'pending', '2025-04-19 05:22:35', 'iVYW9U7Ych4RKEJnhD7YLb'),
(54, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-04-20 10:03:15', NULL),
(55, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-20 11:16:05', NULL),
(56, NULL, 3, 100.00, 'cash', 'completed', 'completed', '2025-04-20 11:19:14', NULL),
(57, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-04-25 05:46:56', NULL),
(58, NULL, 2, 300.00, 'cash', 'pending', 'pending', '2025-04-26 03:44:12', NULL),
(59, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-04-26 14:26:46', NULL),
(60, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-04-26 14:30:33', NULL),
(61, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-04-28 09:54:57', NULL),
(62, NULL, 2, 200.00, 'cash', 'pending', 'pending', '2025-04-28 10:36:52', NULL),
(63, NULL, 2, 200.00, 'cash', 'pending', 'pending', '2025-04-30 15:06:42', NULL),
(64, 'ORD-1746025911-64', 2, 100.00, 'khalti', 'pending', 'pending', '2025-04-30 15:07:42', NULL),
(65, 'ORD-1746026528-65', 2, 100.00, 'khalti', 'pending', 'pending', '2025-04-30 15:12:32', NULL),
(66, 'ORD-1746026559-66', 2, 100.00, 'khalti', 'pending', 'pending', '2025-04-30 15:22:38', NULL),
(67, 'ORD-1746027224-67', 2, 100.00, 'khalti', 'completed', 'pending', '2025-04-30 15:33:42', 'zecZrs2Hoo8onPGm5HXnpU'),
(68, 'ORD-1746028774-68', 2, 200.00, 'khalti', 'completed', 'pending', '2025-04-30 15:59:33', '7rMzcvUppfV6LV9p58awrR'),
(69, 'ORD-1746028966-69', 2, 300.00, 'khalti', 'completed', 'pending', '2025-04-30 16:02:44', 'vEBJS8XMXPBnEaQVUHauMB'),
(70, 'ORD-1746088036-70', 2, 200.00, 'khalti', 'completed', 'pending', '2025-05-01 08:27:14', 'SsLPXDw8p24oNaWBhxXT88'),
(71, 'ORD-1746088929-71', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-01 08:42:06', 'msrjFqWPh6xuCmN4X3tH2Y'),
(72, 'ORDER-1746089539-72', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-01 08:52:18', '4tC5BoaDC3s9Bq3Wd5WY45'),
(73, 'ORDER-1746090023-73', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-01 09:00:21', 'Dicr59jo2CPteEsx7EXghb'),
(74, 'ORDER-1746090344-74', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-01 09:05:39', 'ADEF5riybF8miHyK3BRcJV'),
(75, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-05-01 09:15:18', NULL),
(76, 'ORDER-1746090936-76', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-01 09:15:35', 'h6njgPpiKJBetcfdnJDQfF'),
(77, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-05-02 13:31:45', NULL),
(78, 'ORDER-1746193120-78', 2, 100.00, 'khalti', 'completed', 'pending', '2025-05-02 13:32:08', 'FayqQYeEZydBuLzCgrRGLh'),
(79, 'ORDER-1746194295-79', 2, 100.00, 'khalti', 'pending', 'pending', '2025-05-02 13:50:32', NULL),
(80, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-05-03 02:41:21', NULL),
(81, NULL, 3, 100.00, 'cash', 'pending', 'pending', '2025-05-03 10:52:05', NULL),
(82, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-05-04 12:18:27', NULL),
(83, NULL, 2, 100.00, 'cash', 'pending', 'pending', '2025-05-05 04:57:23', NULL),
(84, NULL, 2, 300.00, 'cash', 'pending', 'pending', '2025-05-05 08:06:41', NULL),
(85, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-05-05 08:08:10', NULL);

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
(45, 45, 14, 1, 100.00),
(46, 46, 14, 1, 100.00),
(47, 47, 14, 1, 100.00),
(48, 48, 14, 1, 100.00),
(49, 49, 14, 1, 100.00),
(50, 50, 14, 1, 100.00),
(51, 51, 14, 1, 100.00),
(52, 52, 15, 1, 100.00),
(53, 53, 16, 1, 100.00),
(54, 54, 14, 1, 100.00),
(55, 55, 17, 1, 100.00),
(56, 56, 15, 1, 100.00),
(57, 57, 15, 1, 100.00),
(58, 58, 14, 3, 100.00),
(59, 59, 14, 1, 100.00),
(60, 60, 14, 1, 100.00),
(61, 61, 15, 1, 100.00),
(62, 62, 15, 1, 100.00),
(63, 62, 14, 1, 100.00),
(64, 63, 14, 2, 100.00),
(65, 64, 15, 1, 100.00),
(66, 65, 15, 1, 100.00),
(67, 66, 13, 1, 100.00),
(68, 67, 14, 1, 100.00),
(69, 68, 15, 2, 100.00),
(70, 69, 15, 3, 100.00),
(71, 70, 15, 2, 100.00),
(72, 71, 15, 1, 100.00),
(73, 72, 15, 1, 100.00),
(74, 73, 15, 1, 100.00),
(75, 74, 15, 1, 100.00),
(76, 75, 15, 1, 100.00),
(77, 76, 15, 1, 100.00),
(78, 77, 15, 1, 100.00),
(79, 78, 15, 1, 100.00),
(80, 79, 15, 1, 100.00),
(81, 80, 19, 1, 100.00),
(82, 81, 21, 1, 100.00),
(83, 82, 21, 1, 100.00),
(84, 83, 20, 1, 100.00),
(85, 84, 20, 3, 100.00),
(86, 85, 22, 1, 100.00);

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
(16, 3, 14, 5, 'ssss', '2025-04-19 04:45:26'),
(17, 2, 14, 3, 'okay', '2025-04-19 05:07:14'),
(18, 2, 15, 5, 'okay', '2025-04-19 05:18:08'),
(19, 2, 16, 5, 'okayy', '2025-04-19 05:22:29'),
(20, 3, 17, 3, 'goood', '2025-04-20 11:17:21');

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
(2, 'Nila Neupane', 'neupanesaphalta@gmail.com', '9848591283', 'nilaneupane123@', 'Kathmandu', 0, '2025-04-09 08:48:17'),
(3, 'Krishna Maya', 'krishnamaya@gmail.com', '9848591283', 'krishnamaya123', 'Kathmandu', 0, '2025-04-10 02:00:01'),
(4, 'Sita', 'sita@gmail.com', '9876543210', 'sita123', 'Chakrapath Kathmandu Narayan Gopal Chowk', 0, '2025-05-04 08:15:09');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
