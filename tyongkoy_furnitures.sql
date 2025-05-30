-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 01:58 PM
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
-- Database: `tyongkoy_furnitures`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(1, 2, 1, 2),
(2, 2, 3, 1),
(6, 9, 1, 1),
(7, 9, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `checkouts`
--

CREATE TABLE `checkouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('Pending','Approved','Disapproved') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkouts`
--

INSERT INTO `checkouts` (`id`, `user_id`, `product_id`, `quantity`, `status`) VALUES
(1, 2, 2, 1, 'Approved'),
(2, 2, 4, 1, 'Approved'),
(3, 7, 3, 1, 'Approved'),
(4, 7, 2, 1, 'Approved'),
(5, 7, 1, 1, 'Approved'),
(6, 7, 4, 1, 'Approved'),
(7, 7, 1, 3, 'Approved'),
(8, 7, 6, 1, 'Approved'),
(9, 7, 1, 1, 'Approved'),
(10, 7, 2, 1, 'Disapproved'),
(11, 7, 4, 1, 'Approved'),
(12, 7, 1, 1, 'Disapproved'),
(13, 7, 2, 1, 'Disapproved'),
(14, 10, 3, 1, 'Approved'),
(15, 10, 1, 1, 'Approved'),
(16, 10, 4, 1, 'Disapproved'),
(17, 7, 2, 1, 'Disapproved');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','approved','disapproved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `status`) VALUES
(1, 2, 1, 2, 'pending'),
(2, 2, 3, 1, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('chair','table','bed','cabinet','door') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `type`, `price`, `quantity`, `image`) VALUES
(1, 'Modern Chair', 'chair', 1500.00, 4, 'uploads/1742786955_bed2.jpg'),
(2, 'Dining Table', 'table', 5000.00, 5, 'uploads/1742788636_table5.jpg'),
(3, 'King Bed', 'bed', 12000.00, 3, 'uploads/1742788812_bed2.jpg'),
(4, 'Wooden Cabinet', 'cabinet', 3000.00, 8, 'uploads/1742788840_cabinet2.jpg'),
(5, 'Solid Wood Door', 'door', 2500.00, 0, 'uploads/1742788867_door3.jpg'),
(6, 'Dining Chair', 'chair', 2500.00, 6, 'uploads/1742789056_m1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `profile_image`, `bio`) VALUES
(1, 'admin1', '$2y$10$abc123...', 'admin1@example.com', 'admin', NULL, NULL),
(2, 'user1', '$2y$10$YOUR_HASHED_PASSWORD_HERE', 'user1@example.com', 'user', NULL, NULL),
(3, 'user2', '$2y$10$YOUR_HASHED_PASSWORD_HERE', 'user2@example.com', 'user', NULL, NULL),
(7, 'louie500', '$2y$10$QWbzyooqYhPY8K1ey5RYiOyb5D6iPI0DXt2rqy.fwZet9.szvvQTO', 'louietiongson43@gmail.com', 'user', 'uploads/1744255628_2gmjdfY6Po-AnimeGirlNunWithTattoo4kWallpaper.jpg', 'HI'),
(8, 'admin12', '$2y$10$8EMUICAC5LzGRKNgxmIROeNaxMGv/0jTxMFuXxCMgjY6rTEVrvQw2', 'louietoingson7@gmail.com', 'admin', 'uploads/1742835654_mamsie.jpg', 'Prettiest and Nicest Teacher in the World <3'),
(9, 'esang500', '$2y$10$JRowr.UGJqgZEiknM6e.IOlpLo1rjdTr7cg5vLARQukJL8VfThEkO', 'jessat@yahoo.com', 'user', NULL, NULL),
(10, 'lowe0909', '$2y$10$MEUBelc0rPf2U02j4jvcaO9QkcpYtcCAM.9Jeu8fJORORZNYIr8I6', 'lowerbit12@gmail.com', 'user', 'uploads/1744255728_bcDZMRth8C-LuffyFirePunchOnePieceDesktopWallpaper4k.jpg', 'LODIIII');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `checkouts`
--
ALTER TABLE `checkouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `checkouts`
--
ALTER TABLE `checkouts`
  ADD CONSTRAINT `checkouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkouts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
