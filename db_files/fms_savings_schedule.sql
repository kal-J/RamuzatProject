-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 29, 2021 at 07:17 AM
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
-- Table structure for table `fms_savings_schedule`
--

DROP TABLE IF EXISTS `fms_savings_schedule`;
CREATE TABLE IF NOT EXISTS `fms_savings_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saving_acc_id` int(11) NOT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  `fulfillment_code` int(2) NOT NULL DEFAULT 1,
  `notified_counter` int(2) NOT NULL DEFAULT 0,
  `date_created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_savings_schedule`
--

INSERT INTO `fms_savings_schedule` (`id`, `saving_acc_id`, `from_date`, `to_date`, `fulfillment_code`, `notified_counter`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES
(1, 3, '2020-06-01 00:00:00', '2020-06-30 00:00:00', 3, 0, 1607772079, 1, '0000-00-00 00:00:00', 0),
(2, 3, '2020-06-01 00:00:00', '2020-06-30 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(3, 3, '2020-07-01 00:00:00', '2020-07-31 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(4, 3, '2020-08-01 00:00:00', '2020-08-31 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(5, 3, '2020-09-01 00:00:00', '2020-09-30 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(6, 3, '2020-10-01 00:00:00', '2020-10-31 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(7, 3, '2020-11-01 00:00:00', '2020-11-30 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(8, 3, '2020-12-01 00:00:00', '2020-12-31 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(9, 3, '2021-01-01 00:00:00', '2021-01-31 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(10, 3, '2021-02-01 00:00:00', '2021-02-28 00:00:00', 3, 0, 1607813120, 1, '0000-00-00 00:00:00', 0),
(11, 2, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1616656979, 1, '0000-00-00 00:00:00', 0),
(12, 3, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1616656979, 1, '0000-00-00 00:00:00', 0),
(13, 5, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1616656979, 1, '0000-00-00 00:00:00', 0),
(14, 6, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1616656979, 1, '0000-00-00 00:00:00', 0),
(15, 7, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1616656979, 1, '0000-00-00 00:00:00', 0),
(16, 7, '2020-04-01 00:00:00', '2020-06-30 00:00:00', 3, 0, 1616657168, 1, '0000-00-00 00:00:00', 0),
(17, 7, '2020-07-01 00:00:00', '2020-09-30 00:00:00', 3, 0, 1616657168, 1, '0000-00-00 00:00:00', 0),
(18, 7, '2020-10-01 00:00:00', '2020-12-31 00:00:00', 3, 0, 1616657168, 1, '0000-00-00 00:00:00', 0),
(19, 8, '2020-01-01 00:00:00', '2020-03-31 00:00:00', 1, 0, 1619118665, 1, '0000-00-00 00:00:00', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
