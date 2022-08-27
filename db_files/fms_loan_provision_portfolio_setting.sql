-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 13, 2022 at 03:44 PM
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
-- Database: `efinanci_nanamsacco`
--

-- --------------------------------------------------------

--
-- Table structure for table `fms_loan_provision_portfolio_setting`
--

CREATE TABLE `fms_loan_provision_portfolio_setting` (
  `id` int(11) NOT NULL,
  `start_range_in_days` int(8) NOT NULL,
  `end_range_in_days` int(8) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `provision_percentage` int(11) NOT NULL,
  `provision_loan_loss_account_id` int(11) NOT NULL,
  `asset_account_id` int(20) NOT NULL,
  `provision_method_id` int(2) NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT 1,
  `date_created` int(8) DEFAULT NULL,
  `created_by` int(2) DEFAULT NULL,
  `modified_by` int(2) NOT NULL,
  `modified_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_loan_provision_portfolio_setting`
--

INSERT INTO `fms_loan_provision_portfolio_setting` (`id`, `start_range_in_days`, `end_range_in_days`, `name`, `description`, `provision_percentage`, `provision_loan_loss_account_id`, `status_id`, `date_created`, `created_by`, `modified_by`, `modified_at`) VALUES
(1, 1, 30, 'Performing', 'Loans performing according to contractual terms', 1, 8, 1, 1649843126, 1, 0, '2022-04-13 18:21:25'),
(2, 1, 30, 'Watch loans', 'Loans whose principal or interest has remained unpaid for 1-30 days or one installment is outstanding', 5, 12, 1, 1649843228, 1, 0, '2022-04-13 12:47:08'),
(3, 31, 90, 'Substandard', 'Loans whose principal or interest has remained unpaid for 31-90 days 2-3 installments installment are outstanding', 25, 8, 1, 1649843313, 1, 0, '2022-04-13 12:48:33'),
(4, 91, 180, 'Doubtfull', 'Loans whose principal or interst has remained unpaid for 91-180  days or 4-6 installments are outstanding', 50, 8, 1, 1649843352, 1, 0, '2022-04-13 12:49:12'),
(5, 181, 500, 'Loss', 'Loanswhose principal or interest has remained unpaide for more than 180 days or more than 6 installment are outstanding', 100, 8, 2, 1649843457, 1, 1, '2022-04-13 13:00:11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
