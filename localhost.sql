-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. März 2011 um 09:27
-- Server Version: 5.0.27
-- PHP-Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `rocknroll`
--
CREATE DATABASE `rocknroll` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rocknroll`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `menuRef` int(11) NOT NULL,
  `title` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `paragraphs` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `cache` longblob,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `menuRef`, `title`, `paragraphs`, `timestamp`, `cache`) VALUES
(1, 1, 'First Page', '1,2', '2011-03-12 00:44:54', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `paragraphs`
--

CREATE TABLE IF NOT EXISTS `paragraphs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `meta` varchar(255) default NULL,
  `content` longtext character set ascii collate ascii_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `paragraphs`
--

INSERT INTO `paragraphs` (`id`, `title`, `type`, `meta`, `content`) VALUES
(1, 'The first entry', 0, 'height=320;image=1', 0x4f6b617920616c736f206469657320697374206465722065727374652045696e747261672065696e657220686f6666656e746c696368206c61656e676572656e20476573636869636874652075656265722064696573657320434d532e20486162206d6972206a612061756368206d616c20776965646572206e757220766f7267656e6f6d6d656e20616c6c65732073656c626572207a75206d616368656e2e2e2e206861686168612e3c6272202f3e0d0a53636865697373652e3c6272202f3e0d0a6e616a612c2076657273756368656e20776972732065696e6766616368206d616c2065696e66616368207a752068616c74656e2c2077612e),
(2, 'The second entry', 1, 'height=320;image=2', 0x4f6b617920616c736f20646965732069737420646572205a57454954452045696e747261672065696e657220686f6666656e746c696368206c61656e676572656e20476573636869636874652075656265722064696573657320434d532e20486162206d6972206a612061756368206d616c20776965646572206e757220766f7267656e6f6d6d656e20616c6c65732073656c626572207a75206d616368656e2e2e2e206861686168612e3c6272202f3e0d0a53636865697373652e3c6272202f3e0d0a6e616a612c2076657273756368656e20776972732065696e6766616368206d616c2065696e66616368207a752068616c74656e2c2077612e);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pictures`
--

CREATE TABLE IF NOT EXISTS `pictures` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `title` varchar(255) default NULL,
  `link` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `pictures`
--

INSERT INTO `pictures` (`id`, `url`, `title`, `link`) VALUES
(1, 'images/Waltraut-neu.jpg', 'Waltraut Droege', NULL),
(2, 'images/hgh_55-neu.gif', 'Heinz-Guenther Hartig', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `submenus`
--

CREATE TABLE IF NOT EXISTS `submenus` (
  `id` int(11) NOT NULL auto_increment,
  `pic` varchar(255) NOT NULL,
  `entries` varchar(255) NOT NULL,
  `links` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `submenus`
--

INSERT INTO `submenus` (`id`, `pic`, `entries`, `links`) VALUES
(1, 'images/images/Elements_06.png', 'eins,zwei,drei,vier,fuenf,sechs,sieben,acht,neun,zehn', 'index.php, termine.php, aktuelles.php');
