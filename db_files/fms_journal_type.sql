-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 05, 2021 at 08:44 AM
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
-- Table structure for table `fms_journal_type`
--

DROP TABLE IF EXISTS `fms_journal_type`;
CREATE TABLE IF NOT EXISTS `fms_journal_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(150) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_journal_type`
--

INSERT INTO `fms_journal_type` (`id`, `type_name`, `status`) VALUES
(1, 'General', 1),
(2, 'Expenses', 1),
(3, 'Bills', 0),
(4, 'Loan disbursement', 0),
(5, 'Loan penalty', 0),
(6, 'Loan repayments', 0),
(7, 'Client deposits', 0),
(8, 'Client withdraws', 0),
(9, 'Withddraw charges', 0),
(10, 'Deposit charges', 0),
(11, 'Subscription payments', 0),
(12, 'Membership payments', 0),
(13, 'Account Maintenance Fees', 0),
(14, 'Income', 1),
(15, 'Bill payment', 0),
(16, 'Invoice', 0),
(17, 'Invoice Payment', 1),
(18, 'Opening Balance', 0),
(19, 'Bad loans', 0),
(20, 'Transfer Charges', 0),
(21, 'Dividend Declaration', 0),
(22, 'Share Calls or Installments', 0),
(23, 'Share payment refund', 0),
(24, 'Share amount transfer', 0),
(25, 'Dividend Payment', 0),
(26, 'Fiscal Year Closure', 0),
(27, 'Adjusting Entries', 0),
(28, 'Loan charge fee', 0),
(29, 'Asset Purchase', 0),
(30, 'Interest Payable', 0),
(31, 'Interest Paid', 0),
(32, 'Share Transaction Charge', 0),
(33, 'Share Transfer Charge', 0),
(34, 'Asset Selling', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
