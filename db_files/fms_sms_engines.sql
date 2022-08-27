-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 25, 2021 at 07:34 AM
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
-- Table structure for table `fms_sms_engines`
--

DROP TABLE IF EXISTS `fms_sms_engines`;
CREATE TABLE IF NOT EXISTS `fms_sms_engines` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `api_key` text NOT NULL,
  `status_id` int(1) NOT NULL DEFAULT 1,
  `organisation_id` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_sms_engines`
--

INSERT INTO `fms_sms_engines` (`id`, `name`, `api_key`, `status_id`, `organisation_id`) VALUES
(1, 'TextUg', '83bb2e5e8bd4ff21846792e687181a', 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
