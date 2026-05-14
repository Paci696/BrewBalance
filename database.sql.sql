-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 10:50 PM
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
-- Database: `brew_balance`
--

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `stock_quantity` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(10) NOT NULL,
  `reorder_level` decimal(10,2) DEFAULT 500.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `name`, `stock_quantity`, `unit`, `reorder_level`) VALUES
(1, 'Coffee Beans', 964.00, 'g', 500.00),
(3, 'Sugar Syrup', 540.00, 'ml', 500.00),
(5, 'Espresso', 4982.00, 'g', 1000.00),
(6, 'Condensed', 5000.00, 'ml', 1000.00),
(7, 'Water', 19750.00, 'ml', 0.00),
(10, 'Caramel', 2000.00, 'g', 1000.00),
(11, 'Milk', 9750.00, 'ml', 1000.00),
(12, 'Choco Sauce', 2000.00, 'g', 500.00),
(13, 'Matcha Powder', 1000.00, 'g', 500.00),
(14, 'Vanilla', 2000.00, 'ml', 1000.00),
(15, 'Brewed Tea', 5000.00, 'ml', 1000.00),
(16, 'Strawberry Puree', 2000.00, 'ml', 1000.00),
(17, 'Choco Powder', 1970.00, 'g', 500.00),
(18, 'Thai Tea', 1000.00, 'g', 500.00),
(19, 'Evap Milk', 5000.00, 'ml', 1000.00),
(20, 'Croissant Base', 30.00, 'pcs', 10.00),
(21, 'Muffin', 29.00, 'pcs', 10.00),
(22, 'Cheesecake Slice', 20.00, 'pcs', 10.00),
(23, 'Bread Slices', 98.00, 'pcs', 50.00),
(24, 'Donut', 40.00, 'pcs', 10.00),
(25, 'Caramel Syrup', 2000.00, 'ml', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `image_path` varchar(255) DEFAULT 'pictures/default_coffee.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `category`, `is_available`, `image_path`) VALUES
(3, 'Spanish Latte', 100.00, 'Coffee', 1, 'pictures/1778788234_Spanish_Latte.jfif'),
(4, 'Americano', 120.00, 'Coffee', 1, 'pictures/1778788786_Americano.webp'),
(5, 'Caramel Macchiato', 130.00, 'Coffee', 1, 'pictures/1778788887_Caramel_Macchiato.jfif'),
(6, 'Cappuccino', 110.00, 'Coffee', 1, 'pictures/1778788922_Cappuccino.webp'),
(8, 'Mocha Latte', 140.00, 'Coffee', 1, 'pictures/1778789506_Mocha_Latte.jfif'),
(9, 'Matcha', 150.00, 'Non-Coffee', 1, 'pictures/1778789555_Matcha.jfif'),
(10, 'Iced Strawberry Tea', 150.00, 'Non-Coffee', 1, 'pictures/1778789614_Iced_Strawberry_Tea.jfif'),
(11, 'Chocolate Milk', 150.00, 'Non-Coffee', 1, 'pictures/1778789646_Chocolate_Milk.jpg'),
(12, 'Vanilla Cream', 130.00, 'Non-Coffee', 1, 'pictures/1778789676_Vanilla_Cream.jpg'),
(13, 'Thai Milk Tea', 150.00, 'Non-Coffee', 1, 'pictures/1778789710_Thai_Milk_Tea.webp'),
(14, 'Classic Croissant', 100.00, 'Pastry', 1, 'pictures/1778789748_Classic_Croissant.webp'),
(15, 'Chocolate Muffin', 90.00, 'Pastry', 1, 'pictures/1778789784_Chocolate_Muffin.webp'),
(16, 'Blueberry Cheesecake', 180.00, 'Pastry', 1, 'pictures/1778789821_Blueberry_Cheesecake.jpg'),
(17, 'Garlic Bread', 100.00, 'Pastry', 1, 'pictures/1778789850_Garlic_Bread.jpg'),
(18, 'Glazed Donut', 100.00, 'Pastry', 1, 'pictures/1778789874_Glazed_Donut.webp');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `ingredient_id` int(11) NOT NULL,
  `required_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `product_id`, `ingredient_id`, `required_amount`) VALUES
(5, 4, 5, 18.00),
(6, 4, 7, 250.00),
(7, 3, 5, 18.00),
(8, 3, 11, 200.00),
(9, 3, 6, 30.00),
(10, 5, 5, 18.00),
(11, 5, 11, 200.00),
(12, 5, 25, 15.00),
(13, 6, 5, 18.00),
(14, 6, 11, 150.00),
(15, 8, 11, 200.00),
(16, 8, 12, 20.00),
(17, 9, 13, 5.00),
(18, 9, 11, 200.00),
(19, 9, 14, 15.00),
(20, 10, 15, 200.00),
(21, 10, 16, 30.00),
(22, 11, 17, 30.00),
(23, 11, 11, 250.00),
(24, 12, 11, 250.00),
(25, 12, 14, 30.00),
(26, 13, 18, 15.00),
(27, 13, 19, 50.00),
(28, 13, 3, 20.00),
(29, 14, 20, 1.00),
(30, 15, 21, 1.00),
(31, 16, 22, 1.00),
(32, 17, 23, 2.00),
(33, 18, 24, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `product_id`, `total_price`, `sale_date`) VALUES
(9, 7, 15, 90.00, '2026-05-14 20:34:00'),
(10, 7, 4, 120.00, '2026-05-14 20:36:38'),
(11, 7, 11, 150.00, '2026-05-14 20:36:38'),
(12, 7, 17, 100.00, '2026-05-14 20:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'admin', '$2y$10$qf95QTe2IgCJjHAAf6QU1.2Ui6mZoiZs94xu5oI3ptWTxddTiyYAi', 'admin', '2026-05-14 18:22:08'),
(7, 'Paci', '$2y$10$aw2gWJUTtSOxnGm9ZUeY5OUCzBR0MamM5iPSYjDNm3BTx6GtkiB9S', 'staff', '2026-05-14 18:26:03'),
(8, 'kent', '$2y$10$kf76gqaY/7/E2sVUp3omhub0rGjORKXY93fJxITs6j3I.ncH5uWFW', 'staff', '2026-05-14 20:42:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `ingredient_id` (`ingredient_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`ingredient_id`) REFERENCES `ingredients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
