-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2024 at 07:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `scandiweb_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `SKU` varchar(256) NOT NULL,
  `name` varchar(256) NOT NULL,
  `price` double NOT NULL,
  `active` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `SKU`, `name`, `price`, `active`) VALUES
(99, 'bookz', 'Book full of letters', 100.55, 1),
(100, 'books2', 'Washing machine manual', 33.55, 1),
(101, 'star-trek-V', 'Star Trek V - The Final Frontier', 10.15, 1),
(113, 'DVD-LOR-1', 'Lord of the Rings: The Fellowship of the Ring', 25.99, 1),
(114, 'Book-HP1', 'Harry Potter and the Philosopher\'s Stone', 12.49, 1),
(127, 'book-001', 'Classic Novel', 15.99, 1),
(128, 'book-002', 'Science Fiction Book', 22.5, 1),
(129, 'book-003', 'Cookbook', 18.75, 1),
(130, 'book-004', 'Biology Textbook', 35, 1),
(131, 'dvd-001', 'Inception', 14.99, 1),
(132, 'dvd-002', 'The Matrix', 12.99, 1),
(133, 'dvd-003', 'Interstellar', 19.99, 1),
(134, 'dvd-004', 'The Shawshank Redemption', 16.99, 1),
(135, 'furn-001', 'Office Chair', 89.99, 1),
(136, 'furn-002', 'Wooden Desk', 149.99, 1),
(137, 'furn-003', 'Bookshelf', 120, 1),
(138, 'furn-004', 'Dining Table', 299.99, 1),
(139, 'book-005', 'Travel Guide', 25, 1),
(140, 'book-006', 'Mystery Novel', 29.99, 1),
(141, 'dvd-005', 'The Godfather', 20, 1),
(142, 'dvd-006', 'The Dark Knight', 18, 1),
(143, 'furn-005', 'Coffee Table', 75, 1),
(144, 'furn-006', 'Bed Frame', 199.99, 1),
(145, 'furn-007', 'Nightstand', 49.99, 1),
(146, 'furn-008', 'Wardrobe', 349.99, 1),
(153, '123', '123', 123, 1),
(155, '213123', '213', 213.23, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_details`
--

CREATE TABLE `product_details` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute` varchar(256) NOT NULL,
  `value` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_details`
--

INSERT INTO `product_details` (`id`, `product_id`, `attribute`, `value`) VALUES
(161, 99, 'weight', '1.2'),
(167, 99, 'typeID', '2'),
(170, 100, 'weight', '0.5'),
(176, 100, 'typeID', '2'),
(179, 101, 'size', '900'),
(185, 101, 'typeID', '1'),
(214, 113, 'size', '4700'),
(215, 113, 'typeID', '1'),
(218, 114, 'weight', '0.7'),
(219, 114, 'typeID', '2'),
(242, 127, 'weight', '0.5'),
(243, 127, 'typeID', '2'),
(244, 128, 'weight', '0.7'),
(245, 128, 'typeID', '2'),
(246, 129, 'weight', '0.6'),
(247, 129, 'typeID', '2'),
(248, 130, 'weight', '1.0'),
(249, 130, 'typeID', '2'),
(250, 131, 'size', '700'),
(251, 131, 'typeID', '1'),
(252, 132, 'size', '750'),
(253, 132, 'typeID', '1'),
(254, 133, 'size', '800'),
(255, 133, 'typeID', '1'),
(256, 134, 'size', '650'),
(257, 134, 'typeID', '1'),
(258, 135, 'height', '95'),
(259, 135, 'width', '65'),
(260, 135, 'length', '65'),
(261, 135, 'typeID', '3'),
(262, 136, 'height', '75'),
(263, 136, 'width', '120'),
(264, 136, 'length', '60'),
(265, 136, 'typeID', '3'),
(266, 137, 'height', '150'),
(267, 137, 'width', '80'),
(268, 137, 'length', '40'),
(269, 137, 'typeID', '3'),
(270, 138, 'height', '75'),
(271, 138, 'width', '150'),
(272, 138, 'length', '90'),
(273, 138, 'typeID', '3'),
(274, 139, 'height', '60'),
(275, 139, 'width', '80'),
(276, 139, 'length', '40'),
(277, 139, 'typeID', '3'),
(278, 140, 'height', '190'),
(279, 140, 'width', '120'),
(280, 140, 'length', '60'),
(281, 140, 'typeID', '3'),
(294, 153, 'weight', '213'),
(295, 153, 'typeID', '2'),
(298, 155, 'size', '213.88'),
(299, 155, 'typeID', '1');

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(256) NOT NULL,
  `input_HTML` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`id`, `title`, `input_HTML`) VALUES
(1, 'DVD', '<div class=\"form-group\">\n    <label for=\"size\">Size (MB):</label>\n    <input id=\"size\" type=\"text\" id=\"size\" name=\"size\" class=\"form-control form-control-lg\" placeholder=\"Size in MB\">\n    <div class=\"invalid-feedback d-none\"></div>\n</div>\n\n <small class=\"form-text text-white mt-2\">\n        Enter the size of the DVD in megabytes. This value should be the total size of the DVD\'s content.\n    </small>'),
(2, 'Book', '<div class=\"form-group\">\n    <label for=\"weight\">Weight (kg):</label>\n    <input id=\"weight\" type=\"text\" id=\"weight\" name=\"weight\" class=\"form-control form-control-lg\" placeholder=\"Weight in kg\">\n    <div class=\"invalid-feedback d-none\"></div>\n\n</div>\n <small class=\"form-text text-white mt-2\">\n        Enter the weight of the book in KG. This value should be the total weight of the book.\n    </small>'),
(3, 'Furniture', '<div class=\"form-group\"> \n    <label for=\"height\">Height (cm):</label> \n    <input id=\"height\" type=\"text\" id=\"height\" name=\"height\" class=\"form-control form-control-lg \" placeholder=\"Height in cm\"> \n    <div class=\"invalid-feedback d-none\"></div>\n</div>\n<div class=\"form-group\"> \n    <label for=\"width\">Width (cm):</label> \n    <input id=\"width\" type=\"text\" id=\"width\" name=\"width\" class=\"form-control form-control-lg \" placeholder=\"Width in cm\"> \n    <div class=\"invalid-feedback d-none\"></div>\n</div>\n<div class=\"form-group\"> \n    <label for=\"length\">Length (cm):</label> \n    <input id=\"length\" type=\"text\" id=\"length\" name=\"length\" class=\"form-control form-control-lg \" placeholder=\"Length in cm\"> \n    <div class=\"invalid-feedback d-none\"></div>\n</div>\n<small class=\"form-text text-white mt-2\">\n    Enter the dimensions of the furniture in CM. This value will be in the form of HxWxL.\n</small>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `SKU` (`SKU`);

--
-- Indexes for table `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product` (`product_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `product_details`
--
ALTER TABLE `product_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=300;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `product` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
