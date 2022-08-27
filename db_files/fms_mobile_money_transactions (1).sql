-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 26, 2021 at 11:19 PM
-- Server version: 10.5.4-MariaDB
-- PHP Version: 7.4.9

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
-- Table structure for table `fms_mobile_money_transactions`
--

-- DROP TABLE IF EXISTS `fms_mobile_money_transactions`;
CREATE TABLE IF NOT EXISTS `fms_mobile_money_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `client_contact` varchar(20) DEFAULT NULL,
  `merchant_transaction_id` text NOT NULL,
  `checkout_request_id` int(20) DEFAULT NULL,
  `remote_transaction_id` varchar(120) DEFAULT NULL,
  `payment_id` int(12) DEFAULT NULL,
  `requested_amount` decimal(17,2) DEFAULT NULL,
  `paid_amount` decimal(17,2) DEFAULT NULL,
  `request_date` datetime(6) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `client_loan_id` int(11) DEFAULT NULL,
  `payment_status` int(20) DEFAULT NULL,
  `status_description` varchar(300) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
