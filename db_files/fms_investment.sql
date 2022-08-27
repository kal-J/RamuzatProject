-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 21, 2021 at 06:52 AM
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
-- Table structure for table `fms_investment`
--

DROP TABLE IF EXISTS `fms_investment`;
CREATE TABLE IF NOT EXISTS `fms_investment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_account_id` int(15) NOT NULL,
  `investment_account_id` int(16) DEFAULT NULL,
  `income_account_id` int(16) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `tenure` int(11) NOT NULL,
  `description` varchar(245) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `date_created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_investment`
--

INSERT INTO `fms_investment` (`id`, `expense_account_id`, `investment_account_id`, `income_account_id`, `type`, `tenure`, `description`, `status_id`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES
(1, 60, 119, 36, 1, 2, 'We test', 1, 1622552409, 2, '2021-06-01 15:52:13', 0),
(2, 60, 119, 36, 2, 3, 'Testing ', 1, 1622613260, 2, '2021-06-02 08:54:20', 0),
(3, 48, 119, 48, 2, 2, 'testing ', 1, 1622811376, 2, '2021-06-04 15:56:16', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
