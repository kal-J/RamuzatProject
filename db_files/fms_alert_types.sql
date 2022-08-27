-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 09, 2021 at 09:18 AM
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
-- Table structure for table `fms_alert_types`
--

DROP TABLE IF EXISTS `fms_alert_types`;
CREATE TABLE IF NOT EXISTS `fms_alert_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_type` varchar(200) NOT NULL,
  `description` varchar(45) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_alert_types`
--

INSERT INTO `fms_alert_types` (`id`, `alert_type`, `description`, `status_id`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES
(1, 'General', 'General alert ', 1, '2021-08-08 22:58:41', 1, '2021-08-08 22:58:41', NULL),
(2, 'Due loan installment', 'Alerts for loan due installment ', 1, '2021-08-08 22:59:55', 1, '2021-08-08 22:59:55', NULL),
(3, 'Due fees', 'Alerts for  due fees ', 1, '2021-08-08 23:00:34', 1, '2021-08-08 23:00:34', NULL),
(4, 'Loan in arrears', 'Alerts for loan loan in arrears due', 1, '2021-08-08 23:02:04', 1, '2021-08-08 23:02:04', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
