-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: mysql.izuc.net
-- Generation Time: Oct 27, 2010 at 10:59 PM
-- Server version: 5.1.39
-- PHP Version: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `library_system`
--
CREATE DATABASE `library_system` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `library_system`;

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE IF NOT EXISTS `account_types` (
  `account_type` int(11) NOT NULL,
  `account_type_name` varchar(25) NOT NULL,
  PRIMARY KEY (`account_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `account_types`
--

INSERT INTO `account_types` (`account_type`, `account_type_name`) VALUES
(1, 'Normal'),
(2, 'Super'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `field`
--

CREATE TABLE IF NOT EXISTS `field` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_label` varchar(25) NOT NULL,
  `field_type` int(11) NOT NULL,
  `field_required` tinyint(1) NOT NULL,
  PRIMARY KEY (`field_id`),
  KEY `type_id` (`field_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=44 ;

--
-- Dumping data for table `field`
--

INSERT INTO `field` (`field_id`, `field_label`, `field_type`, `field_required`) VALUES
(4, 'Content', 3, 0),
(21, 'Venue', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `field_available`
--

CREATE TABLE IF NOT EXISTS `field_available` (
  `available_id` int(11) NOT NULL AUTO_INCREMENT,
  `library_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  PRIMARY KEY (`available_id`),
  UNIQUE KEY `library_id_2` (`library_id`,`session_id`,`field_id`),
  KEY `library_id` (`library_id`,`field_id`),
  KEY `field_id` (`field_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `field_available`
--

INSERT INTO `field_available` (`available_id`, `library_id`, `session_id`, `field_id`) VALUES
(8, 1, 1, 4),
(14, 1, 1, 21),
(22, 2, 6, 4);

-- --------------------------------------------------------

--
-- Table structure for table `field_value`
--

CREATE TABLE IF NOT EXISTS `field_value` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) NOT NULL,
  `library_id` int(11) NOT NULL,
  `field_value` varchar(35) NOT NULL,
  PRIMARY KEY (`value_id`),
  KEY `field_id` (`field_id`,`library_id`),
  KEY `library_id` (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `field_value`
--

INSERT INTO `field_value` (`value_id`, `field_id`, `library_id`, `field_value`) VALUES
(10, 21, 1, 'Training Lab 1'),
(11, 21, 1, 'Training Lab 2'),
(12, 21, 2, 'Room 1'),
(21, 21, 2, 'Room 2f');

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE IF NOT EXISTS `library` (
  `library_id` int(11) NOT NULL AUTO_INCREMENT,
  `library_name` varchar(25) NOT NULL,
  PRIMARY KEY (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `library`
--

INSERT INTO `library` (`library_id`, `library_name`) VALUES
(1, 'Ourimbah'),
(2, 'Callaghan');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(25) NOT NULL,
  `module_settings` varchar(25) NOT NULL,
  `account_type` int(11) NOT NULL,
  PRIMARY KEY (`module_id`),
  KEY `account_type` (`account_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`module_id`, `module_name`, `module_settings`, `account_type`) VALUES
(1, 'add_statistics', '', 1),
(2, 'view_statistics', '', 1),
(3, 'admin_statistics', '', 2),
(4, 'admin_form', '', 3),
(5, 'admin_user', '', 3),
(6, 'admin_library', '', 3),
(7, 'admin_session', '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `session_type`
--

CREATE TABLE IF NOT EXISTS `session_type` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `session_name` varchar(50) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `session_type`
--

INSERT INTO `session_type` (`session_id`, `session_name`) VALUES
(1, 'Library Tour'),
(4, 'EndNote Demonstration'),
(5, 'Orientation Tour'),
(6, 'Information Literacy Tutorial'),
(7, 'EndNote Advanced');

-- --------------------------------------------------------

--
-- Table structure for table `statistic`
--

CREATE TABLE IF NOT EXISTS `statistic` (
  `statistic_id` int(11) NOT NULL AUTO_INCREMENT,
  `library_id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `date_lodged` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_occurred` date NOT NULL,
  PRIMARY KEY (`statistic_id`),
  KEY `user_id` (`user_id`),
  KEY `session_id` (`session_id`),
  KEY `library_id` (`library_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `statistic`
--

INSERT INTO `statistic` (`statistic_id`, `library_id`, `session_id`, `user_id`, `date_lodged`, `date_occurred`) VALUES
(13, 2, 1, 9, '2010-10-26 16:47:08', '2010-10-26'),
(19, 1, 1, 5, '2010-10-26 19:14:40', '2010-10-26'),
(21, 1, 7, 6, '2010-10-27 20:16:51', '2010-10-27'),
(22, 1, 4, 6, '2010-10-27 20:17:16', '2010-10-18'),
(23, 1, 6, 6, '2010-10-27 20:17:50', '2010-10-13'),
(24, 1, 7, 1, '2010-10-27 20:23:32', '2010-10-26'),
(25, 1, 1, 1, '2010-10-27 20:24:17', '2010-10-26'),
(26, 1, 7, 1, '2010-10-27 20:25:23', '2010-10-27'),
(27, 1, 7, 1, '2010-10-27 20:27:34', '2010-10-05');

-- --------------------------------------------------------

--
-- Table structure for table `statistic_data`
--

CREATE TABLE IF NOT EXISTS `statistic_data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `statistic_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `value_id` int(11) DEFAULT NULL,
  `value_text` text,
  PRIMARY KEY (`data_id`),
  KEY `statistic_id` (`statistic_id`,`field_id`,`value_id`),
  KEY `field_id` (`field_id`),
  KEY `value_id` (`value_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=270 ;

--
-- Dumping data for table `statistic_data`
--

INSERT INTO `statistic_data` (`data_id`, `statistic_id`, `field_id`, `value_id`, `value_text`) VALUES
(256, 19, 4, NULL, ''),
(257, 19, 21, 11, NULL),
(260, 21, 4, NULL, 'Hello world'),
(261, 21, 21, 10, NULL),
(262, 24, 4, NULL, 'djslvbhsa'),
(263, 24, 21, 10, NULL),
(264, 25, 4, NULL, 'gfed'),
(265, 25, 21, 11, NULL),
(266, 26, 4, NULL, 'rebneljg'),
(267, 26, 21, 10, NULL),
(268, 27, 4, NULL, 'dfhgal'),
(269, 27, 21, 10, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `statistic_time`
--

CREATE TABLE IF NOT EXISTS `statistic_time` (
  `time_id` int(11) NOT NULL AUTO_INCREMENT,
  `statistic_id` int(11) NOT NULL,
  `attendees` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`time_id`),
  KEY `statistic_id` (`statistic_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=141 ;

--
-- Dumping data for table `statistic_time`
--

INSERT INTO `statistic_time` (`time_id`, `statistic_id`, `attendees`, `start_time`, `end_time`) VALUES
(113, 13, 1, '13:00:00', '15:00:00'),
(121, 19, 1, '13:00:00', '14:00:00'),
(124, 21, 53, '00:00:00', '04:30:00'),
(125, 21, 53, '12:00:00', '23:00:00'),
(126, 21, 23, '02:00:00', '13:00:00'),
(127, 21, 99, '16:00:00', '14:15:00'),
(128, 22, 5, '14:00:00', '14:00:00'),
(129, 22, 43, '12:00:00', '00:00:00'),
(130, 23, 3, '12:00:00', '13:00:00'),
(131, 23, 11, '14:00:00', '13:15:00'),
(132, 24, 32, '01:00:00', '12:15:00'),
(133, 25, 12, '01:15:00', '12:30:00'),
(134, 25, 67, '13:30:00', '16:45:00'),
(135, 25, 45, '12:00:00', '13:15:00'),
(136, 26, 53, '12:00:00', '13:00:00'),
(137, 26, 87, '13:00:00', '14:00:00'),
(138, 27, 12, '00:00:00', '01:00:00'),
(139, 27, 34, '02:00:00', '03:00:00'),
(140, 27, 87, '04:00:00', '05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE IF NOT EXISTS `user_account` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_account` varchar(25) NOT NULL,
  `library_id` int(11) NOT NULL,
  `account_password` char(40) NOT NULL,
  `account_type` int(11) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `staff_account` (`staff_account`),
  KEY `library_id` (`library_id`),
  KEY `account_type` (`account_type`),
  KEY `account_type_2` (`account_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`user_id`, `staff_account`, `library_id`, `account_password`, `account_type`) VALUES
(1, 'TEST02', 1, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 2),
(5, 'TEST01', 1, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 1),
(6, 'TEST03', 1, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 3),
(7, 'TEST05', 2, 'dc724af18fbdd4e59189f5fe768a5f8311527050', 2),
(8, 'TEST06', 2, 'dc724af18fbdd4e59189f5fe768a5f8311527050', 3),
(9, 'TEST04', 2, 'dc724af18fbdd4e59189f5fe768a5f8311527050', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `field_available`
--
ALTER TABLE `field_available`
  ADD CONSTRAINT `field_available_ibfk_5` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `field_available_ibfk_6` FOREIGN KEY (`library_id`) REFERENCES `library` (`library_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `field_available_ibfk_7` FOREIGN KEY (`session_id`) REFERENCES `session_type` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `field_value`
--
ALTER TABLE `field_value`
  ADD CONSTRAINT `field_value_ibfk_4` FOREIGN KEY (`library_id`) REFERENCES `library` (`library_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `field_value_ibfk_3` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`account_type`) REFERENCES `account_types` (`account_type`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `statistic`
--
ALTER TABLE `statistic`
  ADD CONSTRAINT `statistic_ibfk_6` FOREIGN KEY (`user_id`) REFERENCES `user_account` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `statistic_ibfk_4` FOREIGN KEY (`library_id`) REFERENCES `library` (`library_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `statistic_ibfk_5` FOREIGN KEY (`session_id`) REFERENCES `session_type` (`session_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `statistic_data`
--
ALTER TABLE `statistic_data`
  ADD CONSTRAINT `statistic_data_ibfk_6` FOREIGN KEY (`value_id`) REFERENCES `field_value` (`value_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `statistic_data_ibfk_4` FOREIGN KEY (`statistic_id`) REFERENCES `statistic` (`statistic_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `statistic_data_ibfk_5` FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `statistic_time`
--
ALTER TABLE `statistic_time`
  ADD CONSTRAINT `statistic_time_ibfk_1` FOREIGN KEY (`statistic_id`) REFERENCES `statistic` (`statistic_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `user_account_ibfk_2` FOREIGN KEY (`account_type`) REFERENCES `account_types` (`account_type`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`library_id`) REFERENCES `library` (`library_id`) ON DELETE NO ACTION ON UPDATE CASCADE;
