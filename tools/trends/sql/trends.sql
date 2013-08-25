-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 13, 2009 at 05:57 PM
-- Server version: 5.1.30
-- PHP Version: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `trends`
--
CREATE DATABASE `trends` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `trends`;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `keyword` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `total` int(10) unsigned NOT NULL,
  PRIMARY KEY (`keyword`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

