-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 03:56 AM
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
(24, 'Harry Potter and the Sorcerer’s Stone', 'J.K ROWLING', 'Harry Potter and the Sorcerer’s Stone is a spellbinding adventure that whisks readers into a hidden world of magic, mystery, and friendship. From discovering his true identity to facing dark forces at Hogwarts, Harry’s journey will enchant and captivate you from the very first page.', 900.00, '1747050269_Harry Potter And SORCERERS STONE By J.K ROWLING 1.jpg', 'available', 3, NULL, 0, '2025-05-12 11:44:29'),
(25, 'Harry Potter and the Chamber of Secrets part 2', 'J.K ROWLING', 'Harry Potter and the Chamber of Secrets plunges readers into a thrilling mystery as a dark force stalks the halls of Hogwarts. With danger lurking behind every corner and ancient secrets waiting to be uncovered, Harry must uncover the truth before the school is doomed.', 1000.00, '1747050425_2. Harry Potter and the Chamber of Secrets.jpg', 'available', 3, NULL, 0, '2025-05-12 11:47:05'),
(26, 'Harry Potter and the Prisoner of Azkaban Part 3', 'J.K ROWLING', 'Harry Potter and the Prisoner of Azkaban delivers a gripping tale of suspense as a dangerous fugitive escapes from Azkaban, seemingly in pursuit of Harry. With dark secrets, time-turning twists, and shocking revelations, this third installment takes the magic—and the stakes—to a whole new level.', 1000.00, '1747050743_3. Harry Potter and the Prisoner of Azkaban.jpg', 'available', 3, NULL, 0, '2025-05-12 11:48:13'),
(27, 'Harry Potter and the Goblet of Fire Part 4', 'J.K ROWLING', 'Harry Potter and the Goblet of Fire ignites with high-stakes magic as Harry is unexpectedly entered into the deadly Triwizard Tournament. Amid thrilling challenges and rising darkness, he faces the return of Lord Voldemort in a shocking twist that changes everything.', 900.00, '1747050545_4. Harry Potter and the Goblet of Fire.jpg', 'available', 2, NULL, 0, '2025-05-12 11:49:05'),
(28, 'Harry Potter and the Order of the Phoenix Part 5', 'J.K ROWLING', 'Harry Potter and the Order of the Phoenix dives into a darker, more intense chapter as Harry battles disbelief, government interference, and haunting visions. With the rise of Voldemort denied by the Ministry, Harry and his friends form Dumbledore’s Army to fight back—and uncover powerful truths.', 900.00, '1747050604_5. Harry Potter and the Order of the Phoenix.jpg', 'available', 3, NULL, 0, '2025-05-12 11:50:04'),
(29, 'Physics', 'Dharm Bahadur Rokaya', 'It  is a comprehensive guide designed to support students in understanding fundamental and advanced physics concepts. It features clear explanations, solved numerical problems, and exam-focused content tailored to the Nepali academic curriculum.', 200.00, '1747051595_Physics Dharm Bahadur Rokaya.jpg', 'available', 1, 6, 1, '2025-05-12 12:06:35'),
(30, 'Old Book Question', 'Asmita Publication', 'Help for student', 200.00, '1747052177_OLD IS GOLD.jpg', 'available', 1, 3, 1, '2025-05-12 12:13:11'),
(33, 'ssss', 'sss', 'ssss', 100.00, '1747098997_Screenshot 2025-05-04 190902.png', 'sold', 0, 2, 1, '2025-05-13 01:16:37'),
(34, 'The Plague Dogs', 'Richard Adams', 'The Plague Dogs by Richard Adams is a powerful and emotional novel about two dogs, Rowf and Snitter, who escape from a cruel animal testing lab in England. As they struggle to survive in the wild, rumors spread that they carry the plague, turning them into hunted outcasts. Through their journey, the story explores themes of freedom, loyalty, and the ethics of animal experimentation.', 900.00, '1747099475_The Plague Dogs .jpg', 'available', 3, NULL, 0, '2025-05-13 01:24:35'),
(35, 'Shardik', 'Richard Adams', 'Shardik by Richard Adams is a dark, epic fantasy about a giant bear believed to be a god, whose appearance changes the fate of an entire empire.', 900.00, '1747099561_Shardik.jpg', 'available', 3, NULL, 0, '2025-05-13 01:26:01'),
(36, 'WaterShip Down', 'Richard Adams', 'Watership Down by Richard Adams is a classic adventure about a group of rabbits who flee their warren to find a new home, facing danger, leadership struggles, and survival along the way. It’s a moving tale of courage, freedom, and hope.', 800.00, '1747099691_WaterShip Down.jpg', 'available', 2, NULL, 0, '2025-05-13 01:28:11'),
(37, 'This Cursed House', 'Del Sandeen', 'A haunted inheritance, deadly secrets, and a house that won’t let go—This Cursed House is a spine-tingling gothic thriller you won’t forget.', 900.00, '1747099796_This Cursed House.jpg', 'available', 3, NULL, 0, '2025-05-13 01:29:56'),
(38, 'Intercepts', 'T.J. Payne', 'A mind-bending horror thriller about secret experiments, twisted science, and the terrifying cost of control—Intercepts will haunt you long after the final page', 900.00, '1747100435_Intercepts.jpg', 'available', 3, NULL, 0, '2025-05-13 01:40:35');

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
(85, NULL, 2, 100.00, 'cash', 'completed', 'completed', '2025-05-05 08:08:10', NULL),
(86, NULL, 6, 2700.00, 'cash', 'completed', 'completed', '2025-05-12 12:18:25', NULL),
(87, NULL, 6, 900.00, 'cash', 'completed', 'completed', '2025-05-12 12:21:38', NULL),
(88, NULL, 2, 2700.00, 'cash', 'completed', 'completed', '2025-05-12 12:25:25', NULL),
(89, NULL, 6, 3000.00, 'cash', 'completed', 'completed', '2025-05-12 12:53:48', NULL),
(90, NULL, 2, 3000.00, 'cash', 'completed', 'completed', '2025-05-12 13:05:25', NULL),
(91, NULL, 6, 100.00, 'cash', 'completed', 'completed', '2025-05-13 01:17:52', NULL),
(92, NULL, 3, 800.00, 'cash', 'completed', 'completed', '2025-05-13 01:42:57', NULL);

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
(87, 86, 28, 3, 900.00),
(88, 87, 27, 1, 900.00),
(89, 88, 28, 3, 900.00),
(90, 89, 25, 3, 1000.00),
(91, 90, 25, 3, 1000.00),
(92, 91, 33, 1, 100.00),
(93, 92, 36, 1, 800.00);

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
(21, 6, 27, 5, 'Must Buy', '2025-05-12 12:22:57');

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
(2, 'Nila Neupane', 'neupanesaphalta@gmail.com', '9848591283', 'nilaneupane123', 'Chakrapath,Kathmandu', 0, '2025-04-09 08:48:17'),
(3, 'Krishna Maya', 'krishnamaya@gmail.com', '9848591283', 'krishnamaya123', 'Kathmandu', 0, '2025-04-10 02:00:01'),
(4, 'Sita', 'sita@gmail.com', '9876543210', 'sita123', 'Chakrapath Kathmandu Narayan Gopal Chowk', 0, '2025-05-04 08:15:09'),
(6, 'saphalta neupane', 'saphalta@gmail.com', '9848591283', 'saphalta123', 'Chakrapath, Kathmandu', 0, '2025-05-12 02:13:27');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
