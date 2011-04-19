-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 20. April 2011 um 00:24
-- Server Version: 5.0.27
-- PHP-Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `rocknroll`
--
DROP DATABASE `rocknroll`;
CREATE DATABASE `rocknroll` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `rocknroll`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `venue` varchar(255) NOT NULL,
  `location` geometrycollection default NULL,
  `time` time default NULL,
  `misc` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `events`
--

INSERT INTO `events` (`id`, `category`, `title`, `date`, `type`, `description`, `venue`, `location`, `time`, `misc`) VALUES
(1, 'September', 'Susi und die Strolche', '2011-09-15', 2, 'Eine durchaus langhweillige Show. Disney charme verdirbt den Magen.', 'Kaufhaus Draschbach', NULL, '00:00:19', 'Eintritt: 10,-\r\nfamilienfreudnlich.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `pageRef` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `navigation`
--

INSERT INTO `navigation` (`id`, `title`, `pageRef`) VALUES
(1, 'Aktuelles', 1),
(2, 'Termine', 7);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL auto_increment,
  `identifier` varchar(255) NOT NULL,
  `menuRef` int(11) default NULL,
  `title` varchar(255) character set utf8 collate utf8_bin default NULL,
  `paragraphs` varchar(255) character set utf8 collate utf8_bin default NULL,
  `timestamp` timestamp NULL default CURRENT_TIMESTAMP,
  `cache` longblob,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `identifier`, `menuRef`, `title`, `paragraphs`, `timestamp`, `cache`) VALUES
(1, 'index', 1, 'First Seite', '17,12,9,1', '2011-03-12 00:44:54', NULL),
(7, 'events', 4, 'Termine', '3,1', '2011-04-03 12:10:30', NULL),
(9, 'history', 6, 'Geschichte', ',15,16,19', '2011-04-08 01:03:14', NULL),
(10, 'anfang', 9, 'Startseite', ',20', '2011-04-10 23:03:39', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `paragraphs`
--

CREATE TABLE IF NOT EXISTS `paragraphs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `type` int(11) NOT NULL,
  `meta` varchar(255) character set utf8 collate utf8_bin default NULL,
  `content` longtext character set ascii collate ascii_bin,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Daten für Tabelle `paragraphs`
--

INSERT INTO `paragraphs` (`id`, `title`, `type`, `meta`, `content`) VALUES
(1, 'The first entry', 0, 'height=330;image=1', 0x4f6b617920616c736f206469657320697374206465722065727374652045696e747261672065696e657220686f6666656e746c696368206c61656e676572656e20476573636869636874652075656265722064696573657320434d532e20486162206d6972206a612061756368206d616c20776965646572206e757220766f7267656e6f6d6d656e20616c6c65732073656c626572207a75206d616368656e2e2e2e206861686168612e3c6272202f3e0d0a53636865697373652e3c6272202f3e0d0a6e616a612c2076657273756368656e20776972732065696e6766616368206d616c2065696e66616368207a752068616c74656e2c2077612e),
(2, 'The 2nd entry', 0, 'height=300;table=;category=;image=2', 0x4f6b617920616c736f20646965732069737420646572205a57454954452045696e747261672065696e657220686f6666656e746c696368206c61656e676572656e20476573636869636874652075656265722064696573657320434d532e20486162206d6972206a612061756368206d616c20776965646572206e757220766f7267656e6f6d6d656e20616c6c65732073656c626572207a75206d616368656e2e2e2e206861686168612e53636865697373652e6e616a612c2076657273756368656e20776972732065696e6766616368206d616c2065696e66616368207a752068616c74656e2c2077612e),
(3, 'September', 2, 'height=300;table=events;category=September', NULL),
(4, 'Somethign entirely unrelated', 0, 'height=400;table=;category=;image=4', NULL),
(7, 'Die Geschichte', 0, 'height=300;table=;category=', NULL),
(8, 'Noch ein geschichtsabsatz', 0, 'height=300;table=;category=', NULL),
(9, 'Noch ein neuer Absatz', 0, 'height=500;table=;category=;image=9', ''),
(10, 'Noch ein Versuch', 0, 'height=300;table=;category=;image=10', NULL),
(11, 'Huah Gaehn', 0, 'height=300;table=;category=;image=11', NULL),
(12, 'Nur um das nochmal eben klarzustllen', 0, 'height=330;table=;category=;image=12', 0x6f6b2c2066756567656e2077697220646f6368206e6f6368206d616c206562656e2065696e2077656e696720636f6e74656e742065696e2e2e2e),
(13, 'mush absatz', 0, 'height=300;table=;category=;image=13', NULL),
(14, 'hm, oder so evtl', 0, 'height=300;table=;category=;image=14', NULL),
(15, 'Sollte es das etwa...', 0, 'height=300;table=;category=;image=15', NULL),
(16, 'Neu macht Mai', 0, 'height=300;table=;category=;image=16', NULL),
(17, 'So und jetzt mal NUR fuer geschichte', 0, 'height=500;table=;category=;image=17', ''),
(18, 'fadfsdgfsdg', 0, 'height=300;table=;category=;image=18', NULL),
(19, 'Sollte es etwa ...nummer 2', 0, 'height=300;table=;category=;image=19', NULL),
(20, 'Neues Heft erschienen...', 0, 'height=300;table=;category=;image=20', NULL),
(21, 'LANoire', 0, 'height=600;table=;category=;image=21', 0x45696e2077656e696720436f6e74656e743f),
(22, 'ein versuch', 0, 'height=600;table=;category=;image=22', 0x62616c6421);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

--
-- Daten für Tabelle `pictures`
--

INSERT INTO `pictures` (`id`, `url`, `title`, `link`) VALUES
(1, '/images/Waltraut-neu.jpg', 'Waltraut Droege', NULL),
(2, '/images/hgh_55-neu.gif', 'Heinz-Guenther Hartig', NULL),
(4, 'http://25.media.tumblr.com/tumblr_l8wey3FMf21qdhhtjo1_500.png', 'an unrelated image.', NULL),
(6, '/images/dia1.gif', 'Ein der ersten Cover', NULL),
(7, 'http://26.media.tumblr.com/tumblr_l96jbxTdG01qdhhtjo1_500.jpg', 'Die Geschichte begann...', NULL),
(8, 'http://25.media.tumblr.com/tumblr_l97er7Uz2t1qdhhtjo1_500.jpg', 'crypto smasher', NULL),
(9, 'http://29.media.tumblr.com/tumblr_l97eocla8Q1qdhhtjo1_500.jpg', 'Aufsicht', NULL),
(10, 'http://29.media.tumblr.com/tumblr_l97jexbpx91qdhhtjo1_500.jpg', 'titelomatic', NULL),
(11, 'http://24.media.tumblr.com/tumblr_l9keni96a81qdhhtjo1_500.png', 'titoel', NULL),
(12, 'http://27.media.tumblr.com/tumblr_l9vxx5FFyx1qdhhtjo1_500.jpg', 'raaaandom', NULL),
(13, 'http://27.media.tumblr.com/tumblr_l9qlf1zwvz1qdhhtjo1_500.jpg', 'kuh bild', NULL),
(14, 'http://28.media.tumblr.com/tumblr_l9qhscblhm1qdhhtjo1_500.jpg', 'wild ', NULL),
(15, 'http://29.media.tumblr.com/tumblr_la7s49bFln1qdhhtjo1_500.jpg', 'gewesen  sein?!', NULL),
(16, 'http://29.media.tumblr.com/tumblr_la7s1jreap1qdhhtjo1_500.jpg', 'Tse tung', NULL),
(17, 'http://29.media.tumblr.com/tumblr_la7tup93go1qdhhtjo1_500.jpg', 'welt', NULL),
(18, 'http://30.media.tumblr.com/tumblr_la7sby0ISf1qdhhtjo1_500.jpg', 'sfghdyhtr', NULL),
(19, 'http://24.media.tumblr.com/tumblr_lavowx0M351qdhhtjo1_500.jpg', 'dummerchen', NULL),
(20, '', '', NULL),
(21, 'http://a7.sphotos.ak.fbcdn.net/hphotos-ak-snc6/217618_10150243832715097_51752540096_9042180_7573569_n.jpg', 'LAGelb', NULL),
(22, 'http://a7.sphotos.ak.fbcdn.net/hphotos-ak-snc6/217618_10150243832715097_51752540096_9042180_7573569_n.jpg', 'LAN', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `submenus`
--

CREATE TABLE IF NOT EXISTS `submenus` (
  `id` int(11) NOT NULL auto_increment,
  `pic` varchar(255) default NULL,
  `entries` varchar(255) default NULL,
  `links` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `submenus`
--

INSERT INTO `submenus` (`id`, `pic`, `entries`, `links`) VALUES
(1, 'images/images/Elements_06.png', ',Waltraut,Guenther', ',The first entry,The second entry'),
(4, NULL, ',Waltraut', ',The first entry'),
(6, NULL, ',Waltraut', ',The first entry'),
(9, NULL, ',Waltraut', ',The first entry');
