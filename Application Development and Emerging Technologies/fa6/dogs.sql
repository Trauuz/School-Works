-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 09:12 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `formative6`
--

-- --------------------------------------------------------

--
-- Table structure for table `dogs`
--

CREATE TABLE `dogs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `breed` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `color` varchar(100) NOT NULL,
  `height` decimal(6,2) NOT NULL,
  `weight` decimal(6,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dogs`
--

INSERT INTO `dogs` (`id`, `name`, `breed`, `age`, `address`, `color`, `height`, `weight`, `created_at`) VALUES
(1, 'Gene', 'Husky', 6, 'San Juan', 'brown', 51.00, 45.00, '2026-06-27 06:50:42'),
(2, 'Buddy', 'Golden Retriever', 3, 'Quezon City, Metro Manila', 'Golden', 58.50, 30.20, '2026-06-27 06:55:44'),
(3, 'Luna', 'Siberian Husky', 2, 'Makati City, Metro Manila', 'Black and White', 54.00, 22.80, '2026-06-27 06:55:44'),
(4, 'Max', 'German Shepherd', 5, 'Pasig City, Metro Manila', 'Black and Tan', 63.00, 34.50, '2026-06-27 06:55:44'),
(5, 'Bella', 'Beagle', 4, 'Taguig City, Metro Manila', 'Tricolor', 38.00, 11.40, '2026-06-27 06:55:44'),
(6, 'Charlie', 'Poodle', 1, 'Manila City, Metro Manila', 'White', 35.50, 7.20, '2026-06-27 06:55:44'),
(7, 'Daisy', 'Shih Tzu', 6, 'Caloocan City, Metro Manila', 'Brown and White', 27.00, 6.10, '2026-06-27 06:55:44'),
(8, 'Rocky', 'Rottweiler', 4, 'Marikina City, Metro Manila', 'Black and Rust', 67.50, 46.30, '2026-06-27 06:55:44'),
(9, 'Milo', 'Labrador Retriever', 2, 'Antipolo City, Rizal', 'Chocolate', 57.00, 28.70, '2026-06-27 06:55:44'),
(10, 'Coco', 'French Bulldog', 3, 'Parañaque City, Metro Manila', 'Fawn', 31.50, 12.90, '2026-06-27 06:55:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dogs`
--
ALTER TABLE `dogs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dogs`
--
ALTER TABLE `dogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
