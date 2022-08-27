-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 16, 2021 at 01:00 PM
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
-- Table structure for table `fms_emails`
--

DROP TABLE IF EXISTS `fms_emails`;
CREATE TABLE IF NOT EXISTS `fms_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(255) NOT NULL,
  `receiver` text NOT NULL,
  `subject` varchar(255) NOT NULL,
  `copy_to` text,
  `message` text NOT NULL,
  `date_created` int(11) NOT NULL,
  `created_by` int(8) NOT NULL,
  `status_id` int(2) NOT NULL DEFAULT '1' COMMENT '1=active',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fms_emails`
--

INSERT INTO `fms_emails` (`id`, `sender`, `receiver`, `subject`, `copy_to`, `message`, `date_created`, `created_by`, `status_id`) VALUES
(1, 'test@gmail.com', 'test@gmail.com', 'Welcome', 'walter.o@gmail.com', 'Dear All, \r\nWe cordially invite you to schedule a meeting at 4 pm today 15 July 2021.\r\nBest regards \r\nAmbrose ', 1626335959, 1, 1),
(2, 'test@gmail.com', 'test@gmail.com', 'ICT Department meeting', 'ambroseogwang@gmtconsults.com', 'Hello Everyone ,\r\n\r\nTo serves to remind you that our weekly ICT Department meeting has been rescheduled at 4:00 pm today .\r\nKindly keep time .\r\n\r\nBest regards \r\nAmbrose Ogwang \r\nMeeting Chairperson .\r\n', 1626337612, 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
