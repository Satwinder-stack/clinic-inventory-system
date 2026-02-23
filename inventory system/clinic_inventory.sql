-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 01:00 PM
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
-- Database: `clinic_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `field_changed` varchar(100) NOT NULL,
  `old_value` varchar(255) DEFAULT NULL,
  `new_value` varchar(255) DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `item_id`, `action`, `field_changed`, `old_value`, `new_value`, `action_time`) VALUES
(1, 1, 'delete', 'name', 'item1', '', '2025-07-25 05:10:27'),
(2, 1, 'delete', 'final_quantity', '25', '', '2025-07-25 05:10:27'),
(3, 1, 'delete', 'expiration_date', '2028-07-25', '', '2025-07-25 05:10:27'),
(4, 1, 'delete', 'lot_no', 'blk 7a', '', '2025-07-25 05:10:27'),
(5, 5, 'delete', 'name', 'item5', '', '2025-07-25 05:32:55'),
(6, 5, 'delete', 'final_quantity', '90', '', '2025-07-25 05:32:55'),
(7, 5, 'delete', 'expiration_date', '9202-12-08', '', '2025-07-25 05:32:55'),
(8, 5, 'delete', 'lot_no', 'blk 52c', '', '2025-07-25 05:32:55'),
(9, 6, 'delete', 'name', 'syringe', '', '2025-07-25 09:43:38'),
(10, 6, 'delete', 'final_quantity', '50', '', '2025-07-25 09:43:38'),
(11, 6, 'delete', 'expiration_date', '2024-12-09', '', '2025-07-25 09:43:38'),
(12, 6, 'delete', 'lot_no', 'blk8a', '', '2025-07-25 09:43:38'),
(13, 12, 'add', 'all', '', 'Name: item 2, Qty: 60, Exp: 2029-12-08, Lot: blk 9a', '2025-07-25 09:55:18'),
(14, 12, 'edit', 'final_quantity', '60', '50', '2025-07-25 09:57:32'),
(15, 12, 'delete', 'name', 'item 2', '', '2025-07-25 10:17:04'),
(16, 12, 'delete', 'final_quantity', '50', '', '2025-07-25 10:17:04'),
(17, 12, 'delete', 'expiration_date', '2029-12-08', '', '2025-07-25 10:17:04'),
(18, 12, 'delete', 'lot_no', 'blk 9a', '', '2025-07-25 10:17:04'),
(19, 13, 'add', 'all', '', 'Name: item1, Qty: 200, Exp: 2020-12-08, Lot: blk 7a', '2025-07-25 10:17:17'),
(20, 13, 'delete', 'name', 'item1', '', '2025-07-25 10:20:19'),
(21, 13, 'delete', 'final_quantity', '200', '', '2025-07-25 10:20:19'),
(22, 13, 'delete', 'expiration_date', '2020-12-08', '', '2025-07-25 10:20:19'),
(23, 13, 'delete', 'lot_no', 'blk 7a', '', '2025-07-25 10:20:19'),
(24, 14, 'add', 'all', '', 'Name: item2, Qty: 50, Exp: 2202-02-02, Lot: blk 8a', '2025-07-25 10:20:35'),
(25, 14, 'delete', 'name', 'item2', '', '2025-07-25 10:23:25'),
(26, 14, 'delete', 'final_quantity', '50', '', '2025-07-25 10:23:25'),
(27, 14, 'delete', 'expiration_date', '2202-02-02', '', '2025-07-25 10:23:25'),
(28, 14, 'delete', 'lot_no', 'blk 8a', '', '2025-07-25 10:23:25'),
(29, 15, 'add', 'all', '', 'Name: item5, Qty: 80, Exp: 2024-12-09, Lot: blk 8a', '2025-07-25 10:23:36'),
(30, 15, 'delete', 'name', 'item5', '', '2025-07-25 10:23:44'),
(31, 15, 'delete', 'final_quantity', '80', '', '2025-07-25 10:23:44'),
(32, 15, 'delete', 'expiration_date', '2024-12-09', '', '2025-07-25 10:23:44'),
(33, 15, 'delete', 'lot_no', 'blk 8a', '', '2025-07-25 10:23:44'),
(34, 16, 'add', 'all', '', 'Name: item1, Qty: 50, Exp: 1212-12-12, Lot: blk 1a', '2025-07-25 10:25:17'),
(35, 17, 'add', 'all', '', 'Name: item2, Qty: 40, Exp: 2029-12-09, Lot: blk 2a', '2025-07-25 10:25:37'),
(36, 18, 'add', 'all', '', 'Name: item3, Qty: 30, Exp: 2920-12-12, Lot: blk 3a', '2025-07-25 10:25:48'),
(37, 19, 'add', 'all', '', 'Name: item4, Qty: 60, Exp: 2024-12-12, Lot: blk 4a', '2025-07-25 10:26:13'),
(38, 20, 'add', 'all', '', 'Name: item5, Qty: 90, Exp: 8292-12-12, Lot: blk 5a', '2025-07-25 10:26:25'),
(39, 17, 'delete', 'name', 'item2', '', '2025-07-25 10:26:38'),
(40, 17, 'delete', 'final_quantity', '40', '', '2025-07-25 10:26:38'),
(41, 17, 'delete', 'expiration_date', '2029-12-09', '', '2025-07-25 10:26:38'),
(42, 17, 'delete', 'lot_no', 'blk 2a', '', '2025-07-25 10:26:38'),
(43, 18, 'delete', 'name', 'item3', '', '2025-07-25 10:29:06'),
(44, 18, 'delete', 'final_quantity', '30', '', '2025-07-25 10:29:06'),
(45, 18, 'delete', 'expiration_date', '2920-12-12', '', '2025-07-25 10:29:06'),
(46, 18, 'delete', 'lot_no', 'blk 3a', '', '2025-07-25 10:29:06'),
(47, 21, 'add', 'all', '', 'Name: item5, Qty: 50, Exp: 2005-12-12, Lot: blk 1a', '2025-07-25 10:37:15'),
(48, 21, 'edit', 'final_quantity', '50', '60', '2025-07-25 10:37:25'),
(49, 21, 'edit', 'name', 'item5', 'item1', '2025-07-25 10:37:57'),
(50, 21, 'edit', 'expiration_date', '2005-12-12', '2005-05-12', '2025-07-25 10:37:57'),
(51, 21, 'edit', 'lot_no', 'blk 1a', 'blk 2a', '2025-07-25 10:37:57'),
(52, 22, 'add', 'all', '', 'Name: item2, Qty: 40, Exp: 3030-04-04, Lot: blk 2a', '2025-07-25 10:38:31'),
(53, 21, 'delete', 'name', 'item1', '', '2025-07-25 10:39:17'),
(54, 21, 'delete', 'final_quantity', '60', '', '2025-07-25 10:39:17'),
(55, 21, 'delete', 'expiration_date', '2005-05-12', '', '2025-07-25 10:39:17'),
(56, 21, 'delete', 'lot_no', 'blk 2a', '', '2025-07-25 10:39:17'),
(57, 23, 'add', 'all', '', 'Name: item4, Qty: 50, Exp: 1212-12-12, Lot: blk 9a', '2025-07-25 10:52:53'),
(58, 24, 'add', 'all', '', 'Name: item76, Qty: 2312, Exp: 13213-12-31, Lot: bksdf', '2025-07-25 10:53:09'),
(59, 24, 'edit', 'final_quantity', '2312', '231', '2025-07-25 10:53:19'),
(60, 24, 'edit', 'expiration_date', '0000-00-00', '1212-12-12', '2025-07-25 10:53:19'),
(61, 24, 'delete', 'name', 'item76', '', '2025-07-25 12:36:08'),
(62, 24, 'delete', 'final_quantity', '2340', '', '2025-07-25 12:36:08'),
(63, 24, 'delete', 'expiration_date', '1212-12-12', '', '2025-07-25 12:36:08'),
(64, 24, 'delete', 'lot_no', 'bksdf', '', '2025-07-25 12:36:08'),
(65, 25, 'add', 'all', '', 'Name: item1, Qty: 70, Exp: 1212-12-12, Lot: blk 8a', '2025-07-25 12:36:25'),
(66, 26, 'add', 'all', '', 'Name: item2, Qty: 60, Exp: 12121-12-12, Lot: blk 2a', '2025-07-25 12:36:43'),
(67, 27, 'add', 'all', '', 'Name: item3, Qty: 100, Exp: 1212-12-12, Lot: blk 3a', '2025-07-25 12:36:52'),
(68, 28, 'add', 'all', '', 'Name: item4, Qty: 40, Exp: 1212-12-12, Lot: blk 4a', '2025-07-25 12:37:01'),
(69, 29, 'add', 'all', '', 'Name: item5, Qty: 90, Exp: 1212-12-12, Lot: blk 5a', '2025-07-25 12:37:09'),
(70, 30, 'add', 'all', '', 'Name: item6, Qty: 500, Exp: 1212-12-12, Lot: hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', '2025-07-25 13:04:13'),
(71, 30, 'delete', 'name', 'item6', '', '2025-07-25 13:04:41'),
(72, 30, 'delete', 'final_quantity', '500', '', '2025-07-25 13:04:41'),
(73, 30, 'delete', 'expiration_date', '1212-12-12', '', '2025-07-25 13:04:41'),
(74, 30, 'delete', 'lot_no', 'blk 8a', '', '2025-07-25 13:04:41'),
(75, 31, 'add', 'all', '', 'Name: glucose, Qty: 100, Exp: 1212-12-12, Lot: blk 1a', '2025-07-25 13:19:47'),
(76, 32, 'add', 'all', '', 'Name: glucose1, Qty: 500, Exp: 2029-12-12, Lot: blk 2b', '2025-07-25 13:20:01'),
(77, 52, 'add', 'name', '', 'item10', '2025-07-26 05:06:49'),
(78, 52, 'add', 'expiration_date', '', '1212-12-12', '2025-07-26 05:06:49'),
(79, 52, 'add', 'lot_no', '', 'blk 5a', '2025-07-26 05:06:49'),
(80, 52, 'add', 'initial_quantity', '', '1212', '2025-07-26 05:06:49'),
(81, 52, 'add', 'final_quantity', '', '1212', '2025-07-26 05:06:49'),
(82, 47, 'delete', 'name', 'item6', '', '2025-07-26 05:07:06'),
(83, 47, 'delete', 'expiration_date', '2029-12-12', '', '2025-07-26 05:07:06'),
(84, 47, 'delete', 'lot_no', 'blk 6a', '', '2025-07-26 05:07:06'),
(85, 47, 'delete', 'initial_quantity', '90', '', '2025-07-26 05:07:06'),
(86, 47, 'delete', 'final_quantity', '90', '', '2025-07-26 05:07:06'),
(87, 45, 'delete', 'name', 'item3', '', '2025-12-19 09:19:19'),
(88, 45, 'delete', 'expiration_date', '2029-12-12', '', '2025-12-19 09:19:19'),
(89, 45, 'delete', 'lot_no', 'blk 3a', '', '2025-12-19 09:19:19'),
(90, 45, 'delete', 'initial_quantity', '70', '', '2025-12-19 09:19:19'),
(91, 45, 'delete', 'final_quantity', '70', '', '2025-12-19 09:19:19'),
(92, 46, 'delete', 'name', 'item3', '', '2025-12-19 09:19:19'),
(93, 46, 'delete', 'expiration_date', '0008-12-09', '', '2025-12-19 09:19:19'),
(94, 46, 'delete', 'lot_no', 'blk 4a', '', '2025-12-19 09:19:19'),
(95, 46, 'delete', 'initial_quantity', '80', '', '2025-12-19 09:19:19'),
(96, 46, 'delete', 'final_quantity', '80', '', '2025-12-19 09:19:19'),
(97, 48, 'delete', 'name', 'item7', '', '2025-12-19 09:19:19'),
(98, 48, 'delete', 'expiration_date', '2025-12-12', '', '2025-12-19 09:19:19'),
(99, 48, 'delete', 'lot_no', 'blk 9a', '', '2025-12-19 09:19:19'),
(100, 48, 'delete', 'initial_quantity', '90', '', '2025-12-19 09:19:19'),
(101, 48, 'delete', 'final_quantity', '90', '', '2025-12-19 09:19:19'),
(102, 49, 'delete', 'name', 'item7', '', '2025-12-19 09:19:19'),
(103, 49, 'delete', 'expiration_date', '1212-12-12', '', '2025-12-19 09:19:19'),
(104, 49, 'delete', 'lot_no', 'blk 1a', '', '2025-12-19 09:19:19'),
(105, 49, 'delete', 'initial_quantity', '90', '', '2025-12-19 09:19:19'),
(106, 49, 'delete', 'final_quantity', '90', '', '2025-12-19 09:19:19'),
(107, 50, 'delete', 'name', 'item9', '', '2025-12-19 09:19:19'),
(108, 50, 'delete', 'expiration_date', '1202-12-12', '', '2025-12-19 09:19:19'),
(109, 50, 'delete', 'lot_no', 'blk 8a', '', '2025-12-19 09:19:19'),
(110, 50, 'delete', 'initial_quantity', '90', '', '2025-12-19 09:19:19'),
(111, 50, 'delete', 'final_quantity', '90', '', '2025-12-19 09:19:19'),
(112, 51, 'delete', 'name', 'item0', '', '2025-12-19 09:19:19'),
(113, 51, 'delete', 'expiration_date', '1212-12-12', '', '2025-12-19 09:19:19'),
(114, 51, 'delete', 'lot_no', 'blk 1a', '', '2025-12-19 09:19:19'),
(115, 51, 'delete', 'initial_quantity', '12', '', '2025-12-19 09:19:19'),
(116, 51, 'delete', 'final_quantity', '12', '', '2025-12-19 09:19:19'),
(117, 52, 'delete', 'name', 'item16', '', '2025-12-19 09:19:19'),
(118, 52, 'delete', 'expiration_date', '2025-12-12', '', '2025-12-19 09:19:19'),
(119, 52, 'delete', 'lot_no', 'blk 9a', '', '2025-12-19 09:19:19'),
(120, 52, 'delete', 'initial_quantity', '200', '', '2025-12-19 09:19:19'),
(121, 52, 'delete', 'final_quantity', '200', '', '2025-12-19 09:19:19'),
(122, 53, 'add', 'name', '', 'syringe', '2025-12-19 09:20:24'),
(123, 53, 'add', 'expiration_date', '', '2020-12-19', '2025-12-19 09:20:24'),
(124, 53, 'add', 'lot_no', '', 'n/a', '2025-12-19 09:20:24'),
(125, 53, 'add', 'initial_quantity', '', '23', '2025-12-19 09:20:24'),
(126, 53, 'add', 'final_quantity', '', '23', '2025-12-19 09:20:24'),
(127, 54, 'add', 'name', '', 'item2', '2025-12-19 09:20:37'),
(128, 54, 'add', 'expiration_date', '', '5225-06-07', '2025-12-19 09:20:37'),
(129, 54, 'add', 'lot_no', '', 'na', '2025-12-19 09:20:37'),
(130, 54, 'add', 'initial_quantity', '', '25', '2025-12-19 09:20:37'),
(131, 54, 'add', 'final_quantity', '', '25', '2025-12-19 09:20:37'),
(132, 55, 'add', 'name', '', 'item3', '2025-12-19 09:21:01'),
(133, 55, 'add', 'expiration_date', '', '2024-12-22', '2025-12-19 09:21:01'),
(134, 55, 'add', 'lot_no', '', 'blk 5a', '2025-12-19 09:21:01'),
(135, 55, 'add', 'initial_quantity', '', '70', '2025-12-19 09:21:01'),
(136, 55, 'add', 'final_quantity', '', '70', '2025-12-19 09:21:01'),
(137, 55, 'delete', 'name', 'item3', '', '2025-12-19 10:04:14'),
(138, 55, 'delete', 'expiration_date', '2024-12-22', '', '2025-12-19 10:04:14'),
(139, 55, 'delete', 'lot_no', 'blk 5a', '', '2025-12-19 10:04:14'),
(140, 55, 'delete', 'initial_quantity', '70', '', '2025-12-19 10:04:14'),
(141, 55, 'delete', 'final_quantity', '70', '', '2025-12-19 10:04:14'),
(142, 53, 'delete', 'name', 'syringe', '', '2025-12-19 10:04:18'),
(143, 53, 'delete', 'expiration_date', '2020-12-19', '', '2025-12-19 10:04:18'),
(144, 53, 'delete', 'lot_no', 'n/a', '', '2025-12-19 10:04:18'),
(145, 53, 'delete', 'initial_quantity', '23', '', '2025-12-19 10:04:18'),
(146, 53, 'delete', 'final_quantity', '23', '', '2025-12-19 10:04:18'),
(147, 56, 'add', 'name', '', 'item1', '2025-12-19 10:04:46'),
(148, 56, 'add', 'expiration_date', '', '2002-02-02', '2025-12-19 10:04:46'),
(149, 56, 'add', 'lot_no', '', 'bnlk 1', '2025-12-19 10:04:46'),
(150, 56, 'add', 'initial_quantity', '', '20', '2025-12-19 10:04:46'),
(151, 56, 'add', 'final_quantity', '', '20', '2025-12-19 10:04:46'),
(152, 57, 'add', 'name', '', 'item4', '2025-12-19 10:07:41'),
(153, 57, 'add', 'expiration_date', '', '2005-12-12', '2025-12-19 10:07:41'),
(154, 57, 'add', 'lot_no', '', 'too', '2025-12-19 10:07:41'),
(155, 57, 'add', 'initial_quantity', '', '50', '2025-12-19 10:07:41'),
(156, 57, 'add', 'final_quantity', '', '50', '2025-12-19 10:07:41'),
(157, 56, 'delete', 'name', 'item2', '', '2025-12-19 10:23:44'),
(158, 56, 'delete', 'expiration_date', '2002-02-02', '', '2025-12-19 10:23:44'),
(159, 56, 'delete', 'lot_no', 'bnlk 1', '', '2025-12-19 10:23:44'),
(160, 56, 'delete', 'initial_quantity', '25', '', '2025-12-19 10:23:44'),
(161, 56, 'delete', 'final_quantity', '25', '', '2025-12-19 10:23:44'),
(162, 57, 'delete', 'name', 'item4', '', '2025-12-19 10:23:44'),
(163, 57, 'delete', 'expiration_date', '2005-12-12', '', '2025-12-19 10:23:44'),
(164, 57, 'delete', 'lot_no', 'too', '', '2025-12-19 10:23:44'),
(165, 57, 'delete', 'initial_quantity', '50', '', '2025-12-19 10:23:44'),
(166, 57, 'delete', 'final_quantity', '50', '', '2025-12-19 10:23:44'),
(167, 58, 'add', 'name', '', 'item1', '2025-12-19 10:23:55'),
(168, 58, 'add', 'expiration_date', '', '1111-11-11', '2025-12-19 10:23:55'),
(169, 58, 'add', 'lot_no', '', '11', '2025-12-19 10:23:55'),
(170, 58, 'add', 'initial_quantity', '', '1', '2025-12-19 10:23:55'),
(171, 58, 'add', 'final_quantity', '', '1', '2025-12-19 10:23:55'),
(172, 59, 'add', 'name', '', '2', '2025-12-19 10:23:59'),
(173, 59, 'add', 'expiration_date', '', '0002-02-22', '2025-12-19 10:23:59'),
(174, 59, 'add', 'lot_no', '', '22', '2025-12-19 10:23:59'),
(175, 59, 'add', 'initial_quantity', '', '2', '2025-12-19 10:23:59'),
(176, 59, 'add', 'final_quantity', '', '2', '2025-12-19 10:23:59'),
(177, 60, 'add', 'name', '', '3', '2025-12-19 10:24:10'),
(178, 60, 'add', 'expiration_date', '', '275760-03-31', '2025-12-19 10:24:10'),
(179, 60, 'add', 'lot_no', '', '3', '2025-12-19 10:24:10'),
(180, 60, 'add', 'initial_quantity', '', '3', '2025-12-19 10:24:10'),
(181, 60, 'add', 'final_quantity', '', '3', '2025-12-19 10:24:10'),
(182, 61, 'add', 'name', '', '4', '2025-12-19 10:24:15'),
(183, 61, 'add', 'expiration_date', '', '4444-04-04', '2025-12-19 10:24:15'),
(184, 61, 'add', 'lot_no', '', '4', '2025-12-19 10:24:15'),
(185, 61, 'add', 'initial_quantity', '', '4', '2025-12-19 10:24:15'),
(186, 61, 'add', 'final_quantity', '', '4', '2025-12-19 10:24:15'),
(187, 62, 'add', 'name', '', '5', '2025-12-19 10:24:19'),
(188, 62, 'add', 'expiration_date', '', '5555-05-05', '2025-12-19 10:24:19'),
(189, 62, 'add', 'lot_no', '', '5', '2025-12-19 10:24:19'),
(190, 62, 'add', 'initial_quantity', '', '5', '2025-12-19 10:24:19'),
(191, 62, 'add', 'final_quantity', '', '5', '2025-12-19 10:24:19'),
(192, 63, 'add', 'name', '', '6', '2025-12-19 10:24:24'),
(193, 63, 'add', 'expiration_date', '', '6666-06-06', '2025-12-19 10:24:24'),
(194, 63, 'add', 'lot_no', '', '6', '2025-12-19 10:24:24'),
(195, 63, 'add', 'initial_quantity', '', '6', '2025-12-19 10:24:24'),
(196, 63, 'add', 'final_quantity', '', '6', '2025-12-19 10:24:24'),
(197, 64, 'add', 'name', '', '7', '2025-12-19 10:24:28'),
(198, 64, 'add', 'expiration_date', '', '7777-07-07', '2025-12-19 10:24:28'),
(199, 64, 'add', 'lot_no', '', '77', '2025-12-19 10:24:28'),
(200, 64, 'add', 'initial_quantity', '', '7', '2025-12-19 10:24:28'),
(201, 64, 'add', 'final_quantity', '', '7', '2025-12-19 10:24:28'),
(202, 65, 'add', 'name', '', '8', '2025-12-19 10:24:32'),
(203, 65, 'add', 'expiration_date', '', '0888-08-08', '2025-12-19 10:24:32'),
(204, 65, 'add', 'lot_no', '', '8', '2025-12-19 10:24:32'),
(205, 65, 'add', 'initial_quantity', '', '8', '2025-12-19 10:24:32'),
(206, 65, 'add', 'final_quantity', '', '8', '2025-12-19 10:24:32'),
(207, 66, 'add', 'name', '', '9', '2025-12-19 10:24:37'),
(208, 66, 'add', 'expiration_date', '', '0999-09-09', '2025-12-19 10:24:37'),
(209, 66, 'add', 'lot_no', '', '9', '2025-12-19 10:24:37'),
(210, 66, 'add', 'initial_quantity', '', '9', '2025-12-19 10:24:37'),
(211, 66, 'add', 'final_quantity', '', '9', '2025-12-19 10:24:37'),
(212, 58, 'delete', 'name', 'item1', '', '2025-12-19 10:24:57'),
(213, 58, 'delete', 'expiration_date', '1111-11-11', '', '2025-12-19 10:24:57'),
(214, 58, 'delete', 'lot_no', '11', '', '2025-12-19 10:24:57'),
(215, 58, 'delete', 'initial_quantity', '1', '', '2025-12-19 10:24:57'),
(216, 58, 'delete', 'final_quantity', '1', '', '2025-12-19 10:24:57'),
(217, 59, 'edit', 'name', '2', '222', '2025-12-19 10:25:14'),
(218, 59, 'edit', 'expiration_date', '0002-02-22', '0002-03-03', '2025-12-19 10:25:25'),
(219, 59, 'edit', 'lot_no', '22', '23', '2025-12-19 10:25:34'),
(220, 59, 'edit', 'initial_quantity', '2', '23', '2025-12-19 10:25:34'),
(221, 59, 'update_quantity', 'final_quantity', '2', '23', '2025-12-19 10:25:34'),
(222, 59, 'delete', 'name', '222', '', '2025-12-19 10:26:01'),
(223, 59, 'delete', 'expiration_date', '0002-03-03', '', '2025-12-19 10:26:01'),
(224, 59, 'delete', 'lot_no', '23', '', '2025-12-19 10:26:01'),
(225, 59, 'delete', 'initial_quantity', '23', '', '2025-12-19 10:26:01'),
(226, 59, 'delete', 'final_quantity', '23', '', '2025-12-19 10:26:01'),
(227, 60, 'update_quantity', 'final_quantity', '3', '0', '2025-12-19 10:26:27'),
(228, 60, 'update_quantity', 'final_quantity', '0', '3', '2025-12-19 10:26:57'),
(229, 61, 'update_quantity', 'final_quantity', '4', '0', '2025-12-19 10:27:05'),
(230, 60, 'delete', 'monthly_log_row', '3 | 0000-00-00 | 3 | qty=3', '', '2025-12-19 10:27:27'),
(231, 61, 'delete', 'monthly_log_row', '4 | 4444-04-04 | 4 | qty=0', '', '2025-12-19 10:27:27'),
(232, 62, 'edit', 'name', '5', '51', '2025-12-19 10:28:04'),
(233, 62, 'edit', 'expiration_date', '5555-05-05', '5555-02-22', '2025-12-19 10:28:15'),
(234, 62, 'edit', 'lot_no', '5', '51', '2025-12-19 10:28:27'),
(235, 62, 'edit', 'initial_quantity', '5', '51', '2025-12-19 10:28:32'),
(236, 62, 'update_quantity', 'final_quantity', '5', '51', '2025-12-19 10:28:35'),
(237, 66, 'edit', 'name', '9', '9111', '2025-12-19 10:29:28'),
(238, 66, 'edit', 'name', '9111', '9', '2025-12-19 10:29:37'),
(239, 66, 'update_quantity', 'final_quantity', '9', '0', '2025-12-19 10:29:37'),
(240, 65, 'update_quantity', 'final_quantity', '8', '0', '2025-12-19 10:29:40'),
(241, 64, 'update_quantity', 'final_quantity', '7', '0', '2025-12-19 10:29:43'),
(242, 66, 'update_quantity', 'final_quantity', '0', '9', '2025-12-19 10:29:50'),
(243, 65, 'update_quantity', 'final_quantity', '0', '8', '2025-12-19 10:29:50'),
(244, 64, 'update_quantity', 'final_quantity', '0', '7', '2025-12-19 10:29:50'),
(245, 64, 'delete', 'monthly_log_row', '7 | 7777-07-07 | 77 | qty=7', '', '2025-12-19 10:29:54'),
(246, 66, 'delete', 'monthly_log_row', '9 | 0999-09-09 | 9 | qty=9', '', '2025-12-19 10:33:40'),
(247, 65, 'delete', 'monthly_log_row', '8 | 0888-08-08 | 8 | qty=8', '', '2025-12-19 10:33:40'),
(248, 63, 'delete', 'monthly_log_row', '6 | 6666-06-06 | 6 | qty=6', '', '2025-12-19 10:33:40'),
(249, 62, 'delete', 'monthly_log_row', '51 | 5555-02-22 | 51 | qty=51', '', '2025-12-19 10:33:40');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_log`
--

CREATE TABLE `monthly_log` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `expiration_date` date NOT NULL,
  `lot_no` varchar(100) NOT NULL,
  `initial_quantity` int(11) NOT NULL,
  `final_quantity` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `day` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monthly_log`
--
ALTER TABLE `monthly_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

--
-- AUTO_INCREMENT for table `monthly_log`
--
ALTER TABLE `monthly_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
