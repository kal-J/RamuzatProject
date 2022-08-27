-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 21, 2021 at 06:51 AM
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
-- Table structure for table `fms_appreciation`
--

DROP TABLE IF EXISTS `fms_appreciation`;
CREATE TABLE IF NOT EXISTS `fms_appreciation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fixed_asset_id` int(11) DEFAULT NULL,
  `amount` decimal(15,2) DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `financial_year_id` int(11) DEFAULT NULL,
  `narrative` varchar(245) DEFAULT NULL,
  `status_id` tinyint(1) DEFAULT '1',
  `created_by` int(11) DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_appreciation`
--

INSERT INTO `fms_appreciation` (`id`, `fixed_asset_id`, `amount`, `transaction_date`, `financial_year_id`, `narrative`, `status_id`, `created_by`, `date_created`, `modified_by`, `date_modified`) VALUES
(15, 4, '240000.00', '2021-06-18', 2019, 'test', 1, 1, 1623966935, 1, NULL),
(16, 4, '244800.00', '2021-06-18', 2020, 'Test', 1, 1, 1623966948, 1, NULL),
(17, 4, '249696.00', '2021-06-18', 2021, 'test', 1, 1, 1623966967, 1, NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
