-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2021 at 09:06 PM
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
-- Table structure for table `fms_loan_attached_share_accounts`
--

DROP TABLE IF EXISTS `fms_loan_attached_share_accounts`;
CREATE TABLE IF NOT EXISTS `fms_loan_attached_share_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `share_account_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT 1,
  `date_created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_loan_id_attached_savings` (`loan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_loan_attached_share_accounts`
--

INSERT INTO `fms_loan_attached_share_accounts` (`id`, `share_account_id`, `loan_id`, `status_id`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES
(1, 1, 33, 1, 1617667229, 1, '2021-04-06 03:00:29', 1),
(2, 1, 34, 1, 1618124656, 1, '2021-04-11 10:04:16', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
