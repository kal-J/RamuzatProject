-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 08, 2022 at 10:54 AM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `efinanci_mkcc`
--

-- --------------------------------------------------------

--
-- Table structure for table `fms_transaction_date_control`
--

-- DROP TABLE IF EXISTS `fms_transaction_date_control`;
CREATE TABLE IF NOT EXISTS `fms_transaction_date_control` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `organisation_id` int(11) NOT NULL,
  `control_name` text NOT NULL,
  `description` text NOT NULL,
  `past_days` int(11) NOT NULL,
  `future_days` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `status_id` tinyint(2) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_transaction_date_control`
--

INSERT INTO `fms_transaction_date_control` (`id`, `branch_id`, `organisation_id`, `control_name`, `description`, `past_days`, `future_days`, `staff_id`, `status_id`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(1, 1, 1, '30 days before and After', '30 days before and After', 30, 30, 1, 1, '2022-02-08 09:01:23', '0000-00-00 00:00:00', NULL, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
