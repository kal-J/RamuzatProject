-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 23, 2021 at 10:04 AM
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
-- Table structure for table `fms_charge_trigger`
--

DROP TABLE IF EXISTS `fms_charge_trigger`;
CREATE TABLE IF NOT EXISTS `fms_charge_trigger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge_trigger_name` varchar(100) NOT NULL,
  `charge_trigger_description` varchar(500) NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_charge_trigger`
--

INSERT INTO `fms_charge_trigger` (`id`, `charge_trigger_name`, `charge_trigger_description`, `status_id`) VALUES
(1, 'Manual', 'the desc for trigger 1', 1),
(2, 'Monthly ', 'This applies to monthly charges', 1),
(3, 'Applicable on withdraw', 'Fee is applied to withdraw', 1),
(4, 'Applicable on deposit', 'Applicable on every deposit ', 1),
(5, 'Applicable on Transfers', 'Applicable on Transfers', 1),
(6, 'Applicable on MM Withdraw', 'This charge is applied when client withdraws using Mobile money', 1),
(7, 'Applicable Upon Approval', 'This type of fee will be deducted on a client\'s savings upon approval of the account', 1),
(8, 'Applicable on Cash Withdraw', '', 1),
(9, 'Applicable on Bank Withdraw', '', 1),
(10, 'Applicable on Bank deposit RTGS ', 'Applicable on Bank RTGS Transfers - Inwards', 1),
(11, 'Applicable on Bank deposit EFT ', 'Applicable on Bank EFT Transfers - Incoming', 1),
(12, 'Applicable on Bank Withdraw (EFT)', 'Applicable on Bank Withdraw (EFT)', 1),
(13, 'Applicable on Bank Withdraw (RTGS)', 'Applicable on Bank Withdraw (RTGS)', 1),
(14, 'Applicable on Bank Withdraw (Internal Transfers)', '', 1),
(15, 'Applicable on Bank Deposit -Internal Transfer\r\n', 'Applicable on Bank Deposit -Internal Transfer\r\n', 1);

-- --------------------------------------------------------

--
-- Table structure for table `fms_loan_charge_trigger`
--

DROP TABLE IF EXISTS `fms_loan_charge_trigger`;
CREATE TABLE IF NOT EXISTS `fms_loan_charge_trigger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `charge_trigger_name` varchar(100) NOT NULL,
  `charge_trigger_description` varchar(500) NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_loan_charge_trigger`
--

INSERT INTO `fms_loan_charge_trigger` (`id`, `charge_trigger_name`, `charge_trigger_description`, `status_id`) VALUES
(1, 'Manual', 'The charge is applied manually by the responsible person', 1),
(2, 'Applicable on application ', 'This charge is applied at application', 1),
(3, 'Applicable on disbursement', 'Fee is applied on disbursement', 1),
(4, 'Applicable on disbursement-Cash', 'Fee is applied on cash disbursement', 1),
(5, 'Applicable on disbursement-Bank', 'Fee is applied on bank disbursement', 1),
(6, 'Applicable on disbursement-MM', 'Fee is applied on Mobile money disbursement', 1),
(7, 'Pay Off', 'Fee is applied on pay off', 1),
(8, 'Applicable on disbursement-EFT', 'Applicable on disbursement-EFT', 1),
(9, 'Applicable on disbursement-RTGS', 'Applicable on disbursement-RTGS', 1),
(10, 'Applicable on disbursement-(Bank) Internal Transfers', 'Applicable on disbursement-Internal Transfers', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
