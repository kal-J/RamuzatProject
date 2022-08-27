-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 21, 2021 at 06:53 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
-- Table structure for table `fms_investment_transaction`
--

DROP TABLE IF EXISTS `fms_investment_transaction`;
CREATE TABLE IF NOT EXISTS `fms_investment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_no` bigint(16) NOT NULL,
  `investment_id` tinyint(2) NOT NULL,
  `account_no_id` int(11) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT NULL,
  `credit` decimal(15,2) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `payment_mode` int(16) NOT NULL,
  `transaction_type_id` int(2) DEFAULT NULL,
  `transaction_date` datetime(6) NOT NULL,
  `ref_no` int(16) NOT NULL,
  `description` text NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT '1',
  `date_created` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `reverse_msg` text,
  `reversed_by` int(3) DEFAULT NULL,
  `reversed_date` int(11) DEFAULT NULL,
  `created_by` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_investment_transaction`
--

INSERT INTO `fms_investment_transaction` (`id`, `transaction_no`, `investment_id`, `account_no_id`, `debit`, `credit`, `amount`, `payment_mode`, `transaction_type_id`, `transaction_date`, `ref_no`, `description`, `status_id`, `date_created`, `date_modified`, `reverse_msg`, `reversed_by`, `reversed_date`, `created_by`) VALUES
(1, 210608120649735, 2, 27, NULL, '300000.00', NULL, 1, 1, '2021-06-08 12:22:49.562585', 0, 'Testing ', 1, 1623144169, NULL, NULL, NULL, NULL, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
