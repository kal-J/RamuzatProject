-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 24, 2021 at 02:11 PM
-- Server version: 10.3.29-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `efinanci`
--

-- --------------------------------------------------------

--
-- Table structure for table `fms_state`
--
DROP TABLE IF EXISTS `fms_state`;
CREATE TABLE `fms_state` (
  `id` int(11) NOT NULL,
  `state_name` varchar(45) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_state`
--

INSERT INTO `fms_state` (`id`, `state_name`, `description`) VALUES
(1, 'Partial Application', ''),
(2, 'Rejected', ''),
(3, 'Canceled', ''),
(4, 'Withdrawn', ''),
(5, 'Pending Approval', ''),
(6, 'Approved', ''),
(7, 'Active', ''),
(8, 'Written Off', ''),
(9, 'Paid Off', ''),
(10, 'Obligations met', ''),
(11, 'Rescheduled', ''),
(12, 'Locked', ''),
(13, 'In arrears', ''),
(14, 'Refinanced', ''),
(15, 'Closed', ''),
(16, 'Matured', 'Works for fixed accounts. when this is true, then client can withdraw from a fixed deposit account'),
(17, 'Dormant', 'The account has spent a specified time without making any transactions.'),
(18, 'Deleted', 'State for deleted components ie savings account'),
(19, 'Deactivated', 'Account Inactive'),
(20, 'Pending', 'Non-paid fees'),
(21, 'Partial', 'Partially paid fees');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fms_state`
--
ALTER TABLE `fms_state`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fms_state`
--
ALTER TABLE `fms_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
