-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 24, 2020 at 01:27 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sacco_fms` 
--

-- --------------------------------------------------------

--
-- Table structure for table `fms_account_balance_for_interest`
--

DROP TABLE IF EXISTS `fms_account_balance_for_interest`;
CREATE TABLE IF NOT EXISTS `fms_account_balance_for_interest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` tinytext NOT NULL,
  `created_by` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_id` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_account_balance_for_interest`
--

INSERT INTO `fms_account_balance_for_interest` (`id`, `name`, `description`, `created_by`, `status_id`) VALUES
(1, 'Daily Ending Balance on Account', 'None', '2018-08-23 07:58:52', 1),
(2, 'Monthly Ending Balance on Account', 'None', '2018-08-23 07:58:52', 1),
(3, 'Monthly Qualifying Balance on Account', 'None', '2018-08-23 07:58:52', 1);

-- --------------------------------------------------------

--
-- Table structure for table `savings_interest_payment`
--

DROP TABLE IF EXISTS `fms_savings_interest_payment`;
CREATE TABLE IF NOT EXISTS `fms_savings_interest_payment` (
  `id` bigint(19) NOT NULL AUTO_INCREMENT,
  `transaction_no` varchar(30) NOT NULL,
  `savings_account_id` int(11) NOT NULL,
  `interest_amount` decimal(15,2) NOT NULL,
  `date_calculated` datetime NOT NULL DEFAULT current_timestamp(),
  `date_paid` datetime DEFAULT NULL,
  `status_id` int(2) NOT NULL DEFAULT 2,
  `created_by` int(11) NOT NULL,
  `date_created` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
