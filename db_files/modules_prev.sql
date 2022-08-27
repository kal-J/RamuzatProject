-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 01, 2021 at 01:00 PM
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
-- Table structure for table `fms_modules`
--

DROP TABLE IF EXISTS `fms_modules`;
CREATE TABLE IF NOT EXISTS `fms_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(150) NOT NULL,
  `description` tinytext NOT NULL,
  `status_id` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_modules`
--

INSERT INTO `fms_modules` (`id`, `module_name`, `description`, `status_id`, `date_created`) VALUES
(1, 'Staff Management', 'None', 1, '2018-08-24 08:01:13'),
(2, 'Client Management', 'None', 1, '2018-08-24 08:01:13'),
(3, 'Loan Product', 'Nones', 1, '2018-08-24 08:32:43'),
(4, 'Client Loan', 'None', 1, '2018-08-24 08:32:43'),
(5, 'Deposit Product', 'None', 1, '2018-08-30 07:12:38'),
(6, 'Savings', 'None', 1, '2018-08-30 07:12:38'),
(7, 'Role Privileges ', 'None', 1, '2018-08-30 07:14:04'),
(8, 'Accounting', 'None', 1, '2018-08-30 07:14:04'),
(9, 'Subscription ', 'None', 1, '2018-08-30 07:14:50'),
(10, 'Reports', 'None', 1, '2018-08-30 07:14:50'),
(11, 'General Settings', 'None', 1, '2018-09-04 10:58:49'),
(12, 'Shares', 'A part or portion of a larger amount which is divided among a number of people, or to which a number of people contribute.', 1, '2018-10-01 06:09:05'),
(13, 'Client Groups', 'client groups', 1, '2018-10-18 09:38:33'),
(14, 'Group Loan', 'For group loans', 1, '2019-02-08 12:56:33'),
(15, 'Loan Guarantor', 'None', 1, '2019-02-13 09:05:51'),
(16, 'User Roles', 'None', 1, '2019-02-13 09:05:51'),
(17, 'Share Issuance', 'None', 1, '2019-02-13 09:05:51'),
(18, 'Organization Formats ', 'None', 1, '2019-02-13 09:05:51'),
(19, 'Approval Settings', 'None', 1, '2019-02-13 09:05:51'),
(20, 'Fiscal Year', 'None', 1, '2019-02-13 09:05:51'),
(21, 'Membership Fees', 'None', 1, '2019-02-18 13:02:58'),
(22, 'SMS', 'Send sms on actions that require clients to be notified', 1, '2019-06-27 14:22:15'),
(23, 'Lock Months', 'Close / lock a month', 1, '2019-08-20 12:36:38'),
(24, 'Emails', 'Send email notification', 1, '2020-11-10 08:07:20'),
(25, 'Billing', 'Billing', 1, '2021-01-27 16:32:23'),
(26, 'Till', 'Till', 1, '2021-11-19 10:52:50');

-- --------------------------------------------------------

--
-- Table structure for table `fms_module_privilege`
--

DROP TABLE IF EXISTS `fms_module_privilege`;
CREATE TABLE IF NOT EXISTS `fms_module_privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `privilege_code` int(11) NOT NULL,
  `description` tinytext NOT NULL,
  `status_id` tinyint(1) NOT NULL,
  `date_created` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ids` (`module_id`,`privilege_code`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_module_privilege`
--

INSERT INTO `fms_module_privilege` (`id`, `module_id`, `privilege_code`, `description`, `status_id`, `date_created`, `created_by`, `date_modified`, `modified_by`) VALUES
(1, 1, 1, 'Create Staff ', 1, 1535615436, 1, '2018-08-30 04:50:36', 0),
(4, 1, 2, 'View/Read Staff', 1, 1535617029, 1, '2018-08-30 05:17:09', 0),
(5, 2, 1, 'Create Client', 1, 1535617060, 1, '2018-08-30 05:17:40', 0),
(7, 1, 3, 'Update Staff Data', 1, 1535617382, 1, '2018-10-03 09:39:36', 0),
(8, 10, 2, 'View/ Read Reports', 1, 1535631757, 1, '2018-08-30 09:22:37', 0),
(9, 9, 1, 'Create Subscription', 1, 1535631819, 1, '2019-02-18 12:24:05', 0),
(10, 4, 2, 'View/Read Client Loans', 1, 1535631972, 1, '2018-08-30 09:26:12', 0),
(11, 4, 1, 'Create a client Loan', 1, 1535632005, 1, '2018-08-30 09:26:45', 0),
(12, 4, 3, 'Update Client Loans', 1, 1535632028, 1, '2018-08-30 09:27:08', 0),
(13, 5, 1, 'Create Deposit Product', 1, 1535632077, 1, '2018-08-30 09:27:57', 0),
(14, 6, 1, 'Create a Savings Account', 1, 1535632125, 1, '2018-08-30 09:28:45', 0),
(15, 4, 14, 'Approve Client Loans', 1, 1535632194, 1, '2018-09-06 11:12:11', 0),
(17, 8, 2, 'View/Read', 1, 1535638331, 1, '2018-10-12 08:23:26', 0),
(18, 3, 1, 'Create a Loan Product', 1, 1535638561, 1, '2018-08-30 11:16:01', 0),
(19, 3, 2, 'View/Read Loan Products', 1, 1535638579, 1, '2018-08-30 11:16:19', 0),
(20, 3, 7, 'Deactivate Loan Product', 1, 1535638613, 1, '2018-08-30 11:16:53', 0),
(21, 9, 2, 'View Subscription', 1, 1535638678, 1, '2019-02-18 12:23:31', 0),
(22, 9, 6, 'Print Subscription', 1, 1535638702, 1, '2019-02-18 12:23:38', 0),
(24, 10, 6, 'Print Reports', 1, 1535638773, 1, '2018-08-30 11:23:06', 0),
(26, 1, 4, 'Delete Staff', 1, 1535698879, 1, '2018-08-31 04:01:19', 0),
(27, 1, 14, 'Approve Staff', 1, 1535699527, 1, '2018-09-06 11:11:49', 0),
(29, 11, 2, 'View/Read Settings', 1, 1536059105, 1, '2018-09-06 11:11:44', 0),
(30, 2, 2, 'View/Read Client Data', 1, 1536065099, 1, '2018-09-04 09:44:59', 0),
(31, 2, 3, 'Update Client Data', 1, 1536065125, 1, '2018-09-04 09:45:25', 0),
(32, 2, 4, 'Delete Client ', 1, 1536065160, 1, '2018-09-04 09:46:00', 0),
(33, 11, 1, 'Create Settings', 1, 1536242903, 1, '2018-09-06 11:11:09', 0),
(34, 11, 3, 'Update Settings', 1, 1536242918, 1, '2018-09-06 11:08:38', 0),
(35, 11, 4, 'Delete Settings', 1, 1536242941, 1, '2018-09-06 11:09:01', 0),
(36, 6, 2, 'View/Read Savings A/C', 1, 1536243953, 1, '2018-09-06 11:25:53', 0),
(37, 6, 3, 'Update Savings A/C', 1, 1536243978, 1, '2018-09-06 11:26:18', 0),
(38, 6, 4, 'Delete Savings A/C', 1, 1536243998, 1, '2018-09-06 11:26:38', 0),
(39, 4, 10, 'Reject Client Loan', 1, 1536244110, 1, '2018-09-06 11:28:30', 0),
(40, 4, 11, 'Cancel Client Loan', 1, 1536244144, 1, '2018-09-06 11:29:04', 0),
(41, 4, 12, 'Withdraw Client Loan', 1, 1536244171, 1, '2018-09-06 11:29:31', 0),
(42, 4, 13, 'Make installment Payments', 1, 1536244222, 1, '2018-10-13 09:22:57', 0),
(44, 4, 15, 'Write off Client Loan', 1, 1536244454, 1, '2018-09-06 11:34:14', 0),
(45, 4, 16, 'Pay Off Client Loan', 1, 1536244487, 1, '2018-09-06 11:34:47', 0),
(46, 4, 17, 'Reschedule Client Loan', 1, 1536244530, 1, '2018-09-06 11:35:30', 0),
(47, 4, 18, 'Lock Client Loan', 1, 1536244550, 1, '2018-09-06 11:35:50', 0),
(48, 4, 19, 'Refinance Client Loan', 1, 1536244594, 1, '2018-09-06 11:36:34', 0),
(49, 4, 20, 'Forward Client Loan', 1, 1536244617, 1, '2018-09-06 11:36:57', 0),
(50, 12, 1, 'Create Share', 1, 1538385065, 1, '2018-10-01 06:11:05', 0),
(52, 12, 2, 'View/Read share', 1, 1538385101, 1, '2018-10-01 06:11:41', 0),
(53, 12, 3, 'Update Share', 1, 1538385119, 1, '2018-10-01 06:11:59', 0),
(54, 12, 4, 'Delete share', 1, 1538385142, 1, '2018-10-01 06:12:22', 0),
(55, 12, 5, 'Activate share', 1, 1538385170, 1, '2018-10-01 06:12:50', 0),
(56, 12, 6, 'Print share', 1, 1538385192, 1, '2018-10-01 06:13:12', 0),
(57, 12, 7, 'Deactivate share', 1, 1538385208, 1, '2018-10-01 06:13:28', 0),
(58, 11, 7, 'Deactivate ', 1, 1538479904, 4, '2018-10-02 11:31:44', 0),
(59, 1, 7, 'Deactivate', 1, 1538559697, 1, '2018-10-03 09:41:37', 0),
(60, 2, 7, 'Deactivate', 1, 1538559750, 1, '2018-10-03 09:42:30', 0),
(61, 11, 6, 'Print ', 1, 1539245630, 1, '2018-10-11 08:13:50', 0),
(62, 4, 4, 'Delete Attachments', 1, 1539245767, 1, '2018-10-17 13:52:49', 0),
(63, 3, 3, 'Update Loan Product', 1, 1539245869, 1, '2018-10-11 08:17:49', 0),
(64, 3, 4, 'Delete Loan Product', 1, 1539245889, 1, '2018-10-11 08:18:09', 0),
(65, 5, 2, 'View / Read ', 1, 1539246231, 1, '2018-10-11 08:23:51', 0),
(66, 5, 3, 'Update Product', 1, 1539246251, 1, '2018-10-11 08:24:11', 0),
(67, 5, 4, 'Delete Product', 1, 1539246266, 1, '2018-10-11 08:24:26', 0),
(68, 5, 7, 'Deactivate Product', 1, 1539246280, 1, '2018-10-11 08:24:40', 0),
(69, 8, 1, 'Create', 1, 1539332522, 1, '2018-10-12 08:22:02', 0),
(70, 8, 3, 'Update', 1, 1539332545, 1, '2018-10-12 08:22:25', 0),
(71, 8, 4, 'Delete', 1, 1539332556, 1, '2018-10-12 08:22:36', 0),
(77, 8, 6, 'Print', 1, 1539332784, 1, '2018-10-12 08:26:24', 0),
(78, 1, 6, 'Print', 1, 1539423024, 1, '2018-10-13 09:30:24', 0),
(79, 2, 6, 'Print', 1, 1539423054, 1, '2018-10-13 09:30:54', 0),
(80, 4, 6, 'Print', 1, 1539423094, 1, '2018-10-13 09:31:34', 0),
(81, 6, 6, 'Print', 1, 1539423125, 1, '2018-10-13 09:32:05', 0),
(82, 6, 14, 'Approve Savings A/C', 1, 1539423719, 1, '2018-10-13 09:41:59', 0),
(83, 4, 21, 'Reverse', 1, 1539761719, 1, '2018-10-17 07:35:19', 0),
(84, 4, 22, 'Disburse', 1, 1539772344, 1, '2018-10-17 10:32:24', 0),
(86, 6, 23, 'Withdraw Money', 1, 1539779906, 1, '2018-10-17 12:38:26', 0),
(87, 6, 24, 'Deposit Money', 1, 1539779938, 1, '2018-10-17 12:38:58', 0),
(88, 6, 8, 'Change to Pending', 1, 1539785396, 1, '2018-10-17 14:09:56', 0),
(89, 6, 5, 'Activate Savings A/C', 1, 1539787544, 1, '2018-10-17 14:45:44', 0),
(90, 6, 18, 'Lock Savings A/C', 1, 1539788101, 1, '2018-10-17 14:55:01', 0),
(91, 13, 1, 'Create group', 1, 1539855961, 1, '2018-10-18 09:46:01', 0),
(92, 13, 2, 'View/Read ', 1, 1539855979, 1, '2018-10-18 09:46:19', 0),
(93, 13, 3, 'Update Group', 1, 1539855993, 1, '2018-10-18 09:46:33', 0),
(94, 13, 4, 'Delete Group', 1, 1539856010, 1, '2018-10-18 09:46:50', 0),
(95, 13, 7, 'Deactivate Group', 1, 1539856048, 1, '2018-10-18 09:47:28', 0),
(96, 13, 6, 'Print', 1, 1539856059, 1, '2018-10-18 09:47:39', 0),
(97, 14, 1, 'Create a Group Loan', 1, 1549631939, 8, '2019-02-08 13:18:59', 0),
(98, 14, 2, 'View/Read Group Loans', 1, 1549632012, 8, '2019-02-08 13:20:12', 0),
(99, 14, 3, 'Update Group Loans', 1, 1549632167, 8, '2019-02-08 13:22:47', 0),
(100, 15, 1, 'Create ', 1, 1550050075, 8, '2019-07-26 11:57:21', 0),
(101, 15, 3, 'Update ', 1, 1550050127, 8, '2019-07-26 11:57:26', 0),
(102, 15, 6, 'Print', 1, 1550050154, 8, '2019-07-26 11:57:30', 0),
(103, 15, 2, 'View/Read ', 1, 1550050175, 8, '2019-07-26 11:57:35', 0),
(104, 15, 4, 'Delete ', 1, 1550050204, 8, '2019-07-26 12:47:27', 0),
(105, 15, 7, 'Deactivate ', 1, 1550050237, 8, '2019-02-13 09:30:37', 0),
(106, 16, 1, 'Create Roles', 1, 1550050417, 8, '2019-02-13 09:33:37', 0),
(107, 16, 2, 'View/Read Roles', 1, 1550050557, 8, '2019-02-13 09:35:57', 0),
(108, 16, 3, 'Update Roles', 1, 1550051183, 8, '2019-02-13 09:46:23', 0),
(109, 16, 4, 'Delete Roles', 1, 1550054404, 8, '2019-02-13 10:40:04', 0),
(110, 16, 6, 'Print Roles', 1, 1550054450, 8, '2019-02-13 10:40:50', 0),
(111, 16, 7, 'Deactivate', 1, 1550054567, 8, '2019-02-13 10:42:47', 0),
(112, 17, 1, 'Create Share issuance', 1, 1550054659, 8, '2019-02-13 10:44:19', 0),
(113, 17, 2, 'View/Read', 1, 1550054771, 8, '2019-02-13 10:46:11', 0),
(114, 17, 3, 'Update Share issuance', 1, 1550054806, 8, '2019-02-13 10:46:46', 0),
(115, 17, 4, 'Delete Share issuance', 1, 1550054873, 8, '2019-02-13 10:47:53', 0),
(116, 17, 7, 'Deactivate Share Issuance', 1, 1550054990, 8, '2019-02-13 10:49:50', 0),
(117, 17, 6, 'Print Share Issuance', 1, 1550055431, 8, '2019-02-13 10:57:11', 0),
(118, 18, 3, 'Update Formats', 1, 1550055536, 8, '2019-02-13 10:58:56', 0),
(119, 19, 2, 'View/Read Settings', 1, 1550055592, 8, '2019-02-13 10:59:52', 0),
(120, 19, 1, 'Create Settings', 1, 1550055651, 8, '2019-02-13 11:03:40', 0),
(123, 19, 3, 'Update Settings', 1, 1550055718, 8, '2019-02-13 11:01:58', 0),
(124, 19, 6, 'Print Settings', 1, 1550055839, 8, '2019-02-13 11:03:59', 0),
(125, 19, 4, 'Delete Settings', 1, 1550055866, 8, '2019-02-13 11:04:26', 0),
(126, 19, 7, 'Deactivate', 1, 1550055889, 8, '2019-02-13 11:04:49', 0),
(127, 20, 40, 'Close', 1, 1550055922, 8, '2021-02-16 15:17:27', 0),
(128, 20, 2, 'View/Read', 1, 1550055943, 8, '2019-02-13 11:05:43', 0),
(129, 20, 41, 'Rollback', 1, 1550056008, 8, '2021-02-16 15:18:06', 0),
(130, 20, 4, 'Delete', 1, 1550056028, 8, '2019-02-13 11:07:08', 0),
(131, 20, 7, 'Deactivate', 1, 1550056102, 8, '2019-02-13 11:08:22', 0),
(132, 20, 6, 'Print', 1, 1550056123, 8, '2019-02-13 11:08:43', 0),
(133, 7, 30, 'Assign Privilege', 1, 1550067428, 8, '2019-02-13 14:17:08', 0),
(134, 7, 31, 'Remove', 1, 1550067455, 8, '2019-02-13 14:17:35', 0),
(136, 9, 3, 'Update Subscription', 1, 1550492484, 1, '2019-02-18 12:21:24', 0),
(138, 9, 4, 'Delete', 1, 1550492522, 1, '2019-02-18 12:22:02', 0),
(139, 9, 5, 'Deactivate Subscription', 1, 1550492560, 1, '2019-02-18 12:22:40', 0),
(140, 21, 1, 'Create', 1, 1550498066, 1, '2019-02-18 13:54:26', 0),
(141, 21, 2, 'View/Read Membership', 1, 1550498092, 1, '2019-02-18 13:54:52', 0),
(142, 21, 3, 'Update Membership', 1, 1550498108, 1, '2019-02-18 13:55:08', 0),
(143, 21, 4, 'Delete Membership', 1, 1550498125, 1, '2019-02-18 13:55:25', 0),
(144, 21, 5, 'Activate Membership', 1, 1550498145, 1, '2019-02-18 13:55:45', 0),
(145, 21, 6, 'Print', 1, 1550498166, 1, '2019-02-18 13:56:06', 0),
(146, 21, 7, 'Deactivate', 1, 1550498184, 1, '2019-02-18 13:56:24', 0),
(147, 8, 26, 'Edit Transactions', 1, 1565323398, 1, '2019-08-09 04:03:18', 0),
(148, 8, 27, 'Delete Transaction', 1, 1565323414, 1, '2019-08-09 04:03:34', 0),
(149, 8, 28, 'Reverse Transaction', 1, 1565323435, 1, '2019-08-09 04:03:55', 0),
(150, 6, 28, 'Reverse Transaction', 1, 1565323496, 1, '2019-08-09 04:04:56', 0),
(151, 6, 26, 'Edit Transaction', 1, 1565323516, 1, '2019-08-09 04:05:16', 0),
(152, 6, 27, 'Delete Transaction', 1, 1565323546, 1, '2019-08-09 04:05:46', 0),
(153, 6, 25, 'Unlock Savings A/C', 1, 1565323742, 1, '2019-08-09 04:09:02', 0),
(154, 25, 1, 'Create', 1, 1611819398, 1, '2021-01-28 07:36:38', 0),
(155, 25, 2, 'View/Read', 1, 1611819412, 1, '2021-01-28 07:36:52', 0),
(156, 25, 3, 'Update', 1, 1611819430, 1, '2021-01-28 07:37:10', 0),
(157, 25, 4, 'Delete', 1, 1611819442, 1, '2021-01-28 07:37:22', 0),
(158, 25, 5, 'Activate', 0, 1611819461, 1, '2021-01-28 07:38:09', 0),
(159, 25, 6, 'Print', 1, 1611819478, 1, '2021-01-28 07:37:58', 0),
(161, 26, 2, 'View/Read', 1, 1637319297, 1, '2021-11-19 10:55:47', 0),
(162, 26, 6, 'Print ', 1, 1637319317, 1, '2021-11-19 10:55:17', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
