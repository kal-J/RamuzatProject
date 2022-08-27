-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 28, 2021 at 08:24 PM
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
-- Table structure for table `fms_alert_setting`
--

DROP TABLE IF EXISTS `fms_alert_setting`;
CREATE TABLE IF NOT EXISTS `fms_alert_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alert_method` int(2) NOT NULL COMMENT '1=Email,2=SMS',
  `alert_type` varchar(25) NOT NULL,
  `number_of_reminder` int(6) NOT NULL,
  `type_of_reminder` varchar(254) NOT NULL,
  `date_created` int(11) NOT NULL,
  `created_by` tinyint(2) NOT NULL,
  `modified_by` int(2) NOT NULL,
  `modified_at` int(11) NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_alert_setting`
--

INSERT INTO `fms_alert_setting` (`id`, `alert_method`, `alert_type`, `number_of_reminder`, `type_of_reminder`, `date_created`, `created_by`, `modified_by`, `modified_at`, `status_id`) VALUES
(1, 1, '1', 3, '1', 1627503519, 1, 0, 0, 1),
(2, 1, '2', 2, '1', 1627503794, 1, 0, 0, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
