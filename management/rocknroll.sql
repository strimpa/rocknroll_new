-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 15. Mai 2012 um 00:04
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

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bestellung`
--

CREATE TABLE IF NOT EXISTS `bestellung` (
  `bestellIndNum` int(11) NOT NULL auto_increment,
  `schonKunde` varchar(5) default NULL,
  `nachNameBesteller` varchar(51) NOT NULL default '',
  `kundenNrBesteller` varchar(31) default NULL,
  `bestellungen` text NOT NULL,
  `kommentar` text,
  `bestellDatum` date default '0000-00-00',
  PRIMARY KEY  (`bestellIndNum`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `bestellung`
--

INSERT INTO `bestellung` (`bestellIndNum`, `schonKunde`, `nachNameBesteller`, `kundenNrBesteller`, `bestellungen`, `kommentar`, `bestellDatum`) VALUES
(1, 'nein', 'schenk', NULL, 'Abonnement. berechneter Preis: 35.00&euro;', NULL, '2011-05-29'),
(2, 'nein', 'sdgsg', '343463', 'Abonnement<br>Einzelheft<br>Probeheft. berechneter Preis: 58.00&euro;', NULL, '2011-05-31'),
(3, 'nein', 'sdgsg', '343463', 'Abonnement<br>Probeheft<br>kleines Probepaket. berechneter Preis: 75.00&euro;', NULL, '2011-05-31'),
(4, 'nein', 'sdgsg', '343463', 'Abonnement<br>Probeheft<br>kleines Probepaket. berechneter Preis: 75.00&euro;', NULL, '2011-05-31');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL auto_increment,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `pic` varchar(255) default NULL COMMENT 'event picture',
  `date` date NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `venue` varchar(255) NOT NULL,
  `location` geometrycollection default NULL,
  `time` time default NULL,
  `misc` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `events`
--

INSERT INTO `events` (`id`, `category`, `title`, `pic`, `date`, `type`, `description`, `venue`, `location`, `time`, `misc`) VALUES
(1, 'Oktober', 'Susi und die Strolche', '', '2011-09-15', 0, 'Eine durchaus langhweillige Show. Disney charme verdirbt den Magen.', 'Kaufhaus Draschbach', NULL, '00:00:19', 'Eintritt: 10,-familienfreudnlich.'),
(2, 'Oktober', 'Birfdey', 'http://localhost/rocknroll_new/images/bestellen02.gif', '2011-10-13', 0, 'Mein Geburtsach, wa.', 'zuhause', NULL, '15:00:00', 'verschiedenes'),
(3, 'September', 'Ein Eintrag im September', 'http://localhost/rocknroll_new/images/bestellen01.gif', '2011-09-15', 1, 'EIn anderes Konzert', 'inna KNeipe', NULL, '19:00:00', 'what else?'),
(4, 'Oktober', 'Meine neue Veranstaltung', '', '2012-10-11', 1, 'Ein Benefizkonzert fuer Briefmarkensammler', 'Postamt Schwartau', NULL, '19:00:00', 'Diverses'),
(5, 'September', 'Verstaltung', '', '2012-03-06', 0, 'ueber', 'dort', NULL, '19:00:00', 'http://localhost/rocknroll_new/images/buch_perspektivisch.gif');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kunden`
--

CREATE TABLE IF NOT EXISTS `kunden` (
  `dbIndNum` int(10) unsigned NOT NULL auto_increment,
  `kundenNr` varchar(21) default NULL,
  `anrede` varchar(5) default NULL,
  `vorname` varchar(31) default NULL,
  `nachname` varchar(51) NOT NULL default '',
  `adresse` text,
  `land` varchar(21) default NULL,
  `plz` int(11) default NULL,
  `ort` varchar(21) default NULL,
  `telGesch` varchar(31) default NULL,
  `telPrivat` varchar(31) default NULL,
  `fax` varchar(31) default NULL,
  `email` varchar(51) default NULL,
  `ls` int(11) default NULL,
  `naechsteRechnung` varchar(21) default NULL,
  `ktnr` int(21) default NULL,
  `bankName` varchar(31) default NULL,
  `blz` int(21) default NULL,
  PRIMARY KEY  (`dbIndNum`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `kunden`
--

INSERT INTO `kunden` (`dbIndNum`, `kundenNr`, `anrede`, `vorname`, `nachname`, `adresse`, `land`, `plz`, `ort`, `telGesch`, `telPrivat`, `fax`, `email`, `ls`, `naechsteRechnung`, `ktnr`, `bankName`, `blz`) VALUES
(1, NULL, 'Herr', 'michael', 'schenk', 'poststr.12', 'germany', 9306, 'rochlitz', NULL, NULL, NULL, 'schenk-rochlitz@t-online.de', NULL, NULL, NULL, NULL, NULL),
(2, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL),
(3, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL),
(4, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL),
(5, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL),
(6, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL),
(7, '343463', 'Herr', 'sdgsd', 'sdgsg', 'svxv', 'germany', 23424, 'asdfsgvsgszdg', NULL, '23523235', NULL, 'asra@daslnf.de', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `links`
--

CREATE TABLE IF NOT EXISTS `links` (
  `id` int(51) NOT NULL auto_increment,
  `rubrik` varchar(20) NOT NULL default '',
  `beschreibung` varchar(200) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `angelegtVon` varchar(51) NOT NULL default 'Gunnar',
  `anlegeDatum` date NOT NULL default '2005-03-01',
  PRIMARY KEY  (`id`),
  KEY `eintragNum` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=293 ;

--
-- Daten für Tabelle `links`
--

INSERT INTO `links` (`id`, `rubrik`, `beschreibung`, `url`, `angelegtVon`, `anlegeDatum`) VALUES
(1, 'artists', 'RocknRoll Coverbandmit E.Cochran, L.Richard, Buddy Holly.etc.', 'http://www.stiggi-thunder.de', 'Gunnar', '2005-03-01'),
(2, 'artists', 'Mister Lee and the Rockts - RocknRoll', 'http://www.misterlee.de', 'Gunnar', '2005-03-01'),
(3, 'artists', 'Rock and Roll with a Jungle / Mambo Beat', 'http://www.jungletigers.com', 'Gunnar', '2005-03-09'),
(4, 'artists', 'Bo Diddley - Die offizielle Site', 'http://www.members.tripod.com/~Originator_2l', 'Gunnar', '2005-03-01'),
(5, 'artists', 'Alles über Rocker Vince Taylor', 'http://www.ifrance.com/vince-taylor', 'Gunnar', '2005-03-01'),
(6, 'artists', 'Alles über den britischen Rocker Vince Eager mit Fotos und Diskographie', 'http://www.vinceeager.co.uk', 'Gunnar', '2005-03-01'),
(7, 'artists', 'Die offiziellen Seiten über Billy Fury', 'http://www.geocities.com/~ukeep2/billyfury/index.htm', 'Gunnar', '2005-03-01'),
(8, 'artists', 'Erstklassige internationale Diskographie über Billy Fury', 'http://www.fury.cool.am/', 'Gunnar', '2005-03-01'),
(9, 'artists', 'Seiten über Cliff Richard und die Shadows mit ausführlicher Diskographie', 'http://www.cliffandshads.co.uk/', 'Gunnar', '2005-03-01'),
(10, 'artists', 'Eine Gene Vincent-Biografie', 'http://www.home.wanadoo.ni/rock_and_roll/gene.html', 'Gunnar', '2005-03-01'),
(11, 'artists', 'Französische Seiten über Chris Evans', 'http://www.chris.evans.free.fr/', 'Gunnar', '2005-03-01'),
(12, 'artists', 'Alles über Conway Twitty', 'http://www.conwaytwitty.com/', 'Gunnar', '2005-03-01'),
(13, 'artists', 'Vor allem zahlreiche Fotos über Johnny Burnette mit Kurzbiografie', 'http://www.johnnyburnette.com/', 'Gunnar', '2005-03-01'),
(14, 'artists', 'Die offiziellen Seiten über Connie Francis', 'http://www.conniefrancis.com/', 'Gunnar', '2005-03-01'),
(15, 'artists', 'Alles über Connie Francis Diskographie, Biografie, Fotos u.v.m.', 'http://www.conniefrancis.com', 'Gunnar', '2005-03-01'),
(16, 'artists', 'Die Seiten über Eddie Cochran', 'http://www. eddiecochran.com/', 'Gunnar', '2005-03-01'),
(17, 'artists', 'Eine Biografie der Jodimars', 'http://www.rockabillyhall.com/Jodimars.html', 'Gunnar', '2005-03-01'),
(18, 'artists', 'Die offiziellen Seiten über Narvel Felts', 'http://www.mkoc.com/NarvelFelts/', 'Gunnar', '2005-03-01'),
(19, 'artists', 'Die offiziellen Seiten über Frankie Ford', 'http://www.frankieford.com', 'Gunnar', '2005-03-01'),
(20, 'artists', 'Die offiziellen Seiten über Paul Anka mit ausführlicher Diskografie', 'http://www.paulanka.com', 'Gunnar', '2005-03-01'),
(21, 'artists', 'Biografie über LaVern Baker', 'http://www.hypatia.wright.edu/', 'Gunnar', '2005-03-01'),
(22, 'artists', 'Tolle Seiten über Ral Donner mit vielen Infos', 'http://www.axxent.ca/%7Etcwilson/ral.html', 'Gunnar', '2005-03-01'),
(23, 'artists', 'Chuck Berry, Rock''n''Roll-Biblothek', 'http://www.history-of-rock.com/berry.htm', 'Gunnar', '2005-03-01'),
(24, 'artists', 'Chuck Berry, Fansite', 'http://www.crlf.de/ChuckBerry/', 'Gunnar', '2005-03-01'),
(25, 'artists', 'Bill Haley & The Comets, A sellers site', 'http://www.d4haley.com/haley/index.htm', 'Gunnar', '2005-03-01'),
(26, 'artists', 'Schöne Fan-Seiten über Bill Haley & Comets', 'http://www.billhaley.co.uk', 'Gunnar', '2005-03-01'),
(27, 'artists', 'Bill Haley, vielgelobte Coverbandseite', 'http://www.billhaley.de', 'Gunnar', '2005-03-01'),
(28, 'artists', 'Everly Brothers, official Site', 'http://www.everly.net/main.htm', 'Gunnar', '2005-03-01'),
(29, 'artists', 'Das Buddy Holly-Zentrum in Lubbock, Texas, mit viel Wissenswertem über Charles Hardin Holley', 'http://www.buddyhollycenter.org', 'Gunnar', '2005-03-01'),
(30, 'artists', 'Buddy Holly-Fanpage aus England mit Infos auch zum Big Bopper und Ritchie Valens', 'http://www.pmoorcroft.freeserve.co.uk/index.htm', 'Gunnar', '2005-03-01'),
(31, 'artists', 'Topseite zu den Picks, die Buddy Holly bei Aufnahmen wie ''Oh Boy'' oder ''Maybe Baby'' ab 1957 begleiteten', 'http://www.pickrecords.com/', 'Gunnar', '2005-03-01'),
(32, 'artists', 'Die offiziellen Seiten von Wanda Jackson mit bio- und discographischen Auflistungen etc.', 'http://www.wandajackson.com', 'Gunnar', '2005-03-01'),
(33, 'artists', 'Die offiziellen Seiten von Ray Campi', 'http://www.electricearl.com/campi.html', 'Gunnar', '2005-03-01'),
(34, 'artists', 'Die offiziellen Seiten von Linda Gail Lewis', 'http://www.lindagail.com', 'Gunnar', '2005-03-01'),
(35, 'artists', 'Die offiziellen Seiten von Rip Masters', 'http://www.bermudaschwartz.com/rip/index.html', 'Gunnar', '2005-03-01'),
(36, 'artists', 'Die offiziellen Graceland-Seiten von Elvis Presley', 'http://www.elvis.com/', 'Gunnar', '2005-03-01'),
(37, 'artists', 'Alles über Surf-König Dick Dale', 'http://www.dickdale.com/', 'Gunnar', '2005-03-01'),
(38, 'artists', 'Everly Brothers', 'http://home.hddnet.nl/wm.alberts', 'Gunnar', '2005-03-01'),
(39, 'artists', 'Del Shannon, official Site', 'http://www.delshannon.com', 'Gunnar', '2005-03-01'),
(40, 'artists', 'Bobby Darin, official Site', 'http://www.bobbydarin.net', 'Gunnar', '2005-03-01'),
(41, 'artists', 'Roy Orbison, official Site', 'http://www.orbison.com/', 'Gunnar', '2005-03-01'),
(42, 'artists', 'Ricky Nelson, official Site', 'http://www.RickNelson.com/middle.html', 'Gunnar', '2005-03-01'),
(43, 'artists', 'Die offizielle Seite über Johnny Cash mit allem was das Herz begehrt', 'http://www.johnnycash.com', 'Gunnar', '2005-03-01'),
(44, 'artists', 'Little Richard, Fan Site, no official Site known', 'http://www.littlerichard.com/', 'Gunnar', '2005-03-01'),
(45, 'artists', 'Jerry Lee Lewis, official Site', 'http://www.jerryleelewis.com', 'Gunnar', '2005-03-01'),
(46, 'artists', 'Berliner Elvis-Club', 'http://www.elvisclubberlin.de/', 'Gunnar', '2005-03-01'),
(47, 'artists', 'Ted Herold-Biografie', 'http://online.prevezanos.com/schlager/h/herold.shtml', 'Gunnar', '2005-03-01'),
(48, 'artists', 'Peter Kraus-Biografie', 'http://online.prevezanos.com/schlager/k/kraus.html', 'Gunnar', '2005-03-01'),
(49, 'artists', 'Mit vielen aktuellen Infos zu Chuck, Jerry Lee und Fats Domino', 'http://www.chuckberry.de', 'Gunnar', '2005-03-01'),
(50, 'artists', 'Real Audio Player mit Songs von u.a. Jimmy Cavallo, Bill Haley, Fats Domino, Bill Haley, Little Richard, Louis Prima', 'http://www.hoyhoy.com/artists.html', 'Gunnar', '2005-03-01'),
(51, 'artists', 'Artikel über u.a. Pat Boone, Paul Anka, Little Anthony & Imperials, Big Bopper, Eddie Cochran, Danny & Juniors..', 'http://home.wanadoo.nl/rock_and_roll/', 'Gunnar', '2005-03-01'),
(52, 'artists', 'Die offizielle Seite über Johnny Cash mit allem was das Herz begehrt', 'http://www.johnnycash.com', 'Gunnar', '2005-03-01'),
(53, 'artists', 'Artikel über u.a. Johnny Ace, LaVern Baker, Bobby Darin, Bill Black''s Combo, Eddie Cochran, Sam Cooke, Dion & Belmonts, Frankie Ford etc', 'http://home.wanadoo.nl/rock_and_roll/', 'Gunnar', '2005-03-01'),
(54, 'artists', 'Die offizielle Buddy Holly-Seite', 'http://www.buddyholly.com/', 'Gunnar', '2005-03-01'),
(55, 'artists', 'Rave On - The unofficial Buddy Holly-Site', 'http://www.visuallink.net/kdwilt/', 'Gunnar', '2005-03-01'),
(56, 'artists', 'Unglaubliche Seite über diesen legendären DJ. Etliche alte und neuere Zeitungsartikel stehen zum Download bereit (.pdf-Format), davon jede Menge aus den 50s. Auch Fotos, seltene wie legendäre, sin', 'http://www.alanfreed.com', 'Gunnar', '2005-03-01'),
(57, 'artists', 'Ral Donner, Fan-Site', 'http://www.raldonner.com', 'Gunnar', '2005-03-01'),
(58, 'artists', 'Eddie cochran, A list of multimedia material', 'http://www.oliveweb.clara.net/r-cochran-eddie.htm', 'Gunnar', '2005-03-01'),
(59, 'artists', 'The Barnshakers, official Site', 'http://www.saunalahti.fi/mikalii/', 'Gunnar', '2005-03-01'),
(60, 'artists', 'The Gogetters, official Site', 'http://www.vikingdesign.com/gogetters/welcome.htm', 'Gunnar', '2005-03-01'),
(61, 'artists', 'Deke Dickerson and the ecophonics, official Site', 'http://www.hightone.com/deke/', 'Gunnar', '2005-03-01'),
(62, 'artists', 'Jack Baymooreand the Bandits, official Site', 'http://home9.swipnet.se/~w-92656/', 'Gunnar', '2005-03-01'),
(63, 'artists', 'Chicago artists and label info 1940 -60', 'http://hubcap.clemson.edu/~campber/rsrf.html', 'Gunnar', '2005-03-01'),
(64, 'artists', 'The Restless', 'http://members.aol.com/restlessxx/', 'Gunnar', '2005-03-01'),
(65, 'artists', 'Marti Brom, official Site', 'http://www.marti-rockabillygal.de/', 'Gunnar', '2005-03-01'),
(66, 'artists', 'Beatbops, Coverband aus Kempten, Musik der 50er + 60er Jahre', 'http://www.beatbops.de', 'Gunnar', '2005-03-01'),
(67, 'artists', 'Franny and the Fireballs + Susis Schlagersextett, Coverband', 'http://www.apeconcerts.de', 'Gunnar', '2005-03-01'),
(68, 'artists', 'Bill Haley, official Site', 'http://www.originalcomets.com', 'Gunnar', '2005-03-01'),
(69, 'artists', 'Bill Haley, Fan Site aus Wolfsburg', 'http://www.billhaley.de', 'Gunnar', '2005-03-01'),
(70, 'artists', 'Bandseite LEE''S REVENGE - Österreichische Rockabilly & Rock`n Roll Band', 'http://www.lees-revenge.at.tf/', 'Gunnar', '2005-03-01'),
(71, 'artists', 'Jerry Lee Lewis Site', 'http://www.home.vr-web.de/jerrylee', 'Gunnar', '2005-03-01'),
(72, 'artists', 'Bandseite Lazyboys', 'http://www.lazyboys.de/', 'Gunnar', '2005-03-01'),
(73, 'artists', 'Das deutsche Elvis Presley-Archiv mit vielen Infos', 'http://www.elvis-archiv.de/', 'Gunnar', '2005-03-01'),
(74, 'artists', 'Klasse deutsche Seiten über Jerry Lee Lewis', 'http://home.vr-web.de/jerrylee/html/Seite1.htm', 'Gunnar', '2005-03-01'),
(75, 'artists', 'Seiten über Tex Rubinowitz, Ripsaw Records und Billy Hancock', 'http://www.noclubproductions.com/', 'Gunnar', '2005-03-01'),
(76, 'artists', 'Die offiziellen Seiten über den Big Bopper', 'http://www.officialbigbopper.com/', 'Gunnar', '2005-03-01'),
(77, 'artists', 'Alles über Charlie Gracie', 'http://www.charliegracie.com/', 'Gunnar', '2005-03-01'),
(78, 'artists', 'Schöne Seiten über Gene Vincent mit Fotos und Discographie', 'http://www.rockabillyhall.com/GeneVincent.html', 'Gunnar', '2005-03-01'),
(79, 'artists', 'Alles über Lee Hazlewood', 'http://www.leehazlewood.com/', 'Gunnar', '2005-03-01'),
(80, 'artists', 'Die offiziellen Seiten des Italo-Rockers Little Tony', 'http://www.littletony.it/html/index2.htm', 'Gunnar', '2005-03-01'),
(81, 'artists', 'Matchbox', 'http://www.rockabillyrebel.co.uk', 'Gunnar', '2005-03-01'),
(82, 'artists', 'The Lennerockers', 'http://www.superkonzert.de/sites/frames1.htm', 'Gunnar', '2005-03-01'),
(83, 'artists', 'The Doublecrossers aus Hannover', 'http://www.doublecrossers.de', 'Gunnar', '2005-03-01'),
(84, 'artists', 'Johnny And The Roccos', 'http://www.johnny-and-the-roccos.de/', 'Gunnar', '2005-03-01'),
(85, 'artists', 'The dirty little Crocodiles Gigs m.Lennerockers, Johnny a.t. Roccos', 'http://www.tdlc.de', 'Gunnar', '2005-03-01'),
(86, 'artists', 'The Fairytales Burgdorf', 'http://mitglied.lycos.de/Fairytales/Startseite/Home.htm', 'Gunnar', '2005-03-01'),
(87, 'artists', '50''s DJs aus dem Süden Deutschlands', 'http://www.driftindoughboys.de', 'Gunnar', '2005-03-01'),
(88, 'artists', 'official Website of Johnny Powers', 'http://www.johnnypowers.com', 'Gunnar', '2005-03-01'),
(89, 'tv', 'alles über Bezaubernde Jeannie(139 Folgen, 9/1965-5/1970)', 'http://www.geocities.com/carpet65/astro.html', 'Gunnar', '2005-03-01'),
(90, 'tv', 'alles über die Original-TV-Serie Raumschiff Enterprise (79 Folgen, 1966-1969)', 'http://www.tos.net/', 'Gunnar', '2005-03-01'),
(91, 'tv', 'die offiziellen Seiten über Bonanza (415 Folgen, 9/1959-4/1972)', 'http://www.bonanza1.com/', 'Gunnar', '2005-03-01'),
(92, 'tv', 'klasse Seiten über amerikanische Filmschauspieler der 30er bis 90er Jahre', 'http://www.movieactors.com/index2.htm', 'Gunnar', '2005-03-01'),
(93, 'tv', 'alles über die Westernserie Westlich von Santa FÃ© mit Chuck Connors u. Johnny Crawford (169 Folgen, 1958-4/1963)', 'http://members.tripod.com/~northfork/episodes.html', 'Gunnar', '2005-03-01'),
(94, 'tv', 'Hier werden die singenden B-Movie-Cowboys Roy Rogers und Gene Autry neben anderen vorgestellt', 'http://www.cowboypal.com/', 'Gunnar', '2005-03-01'),
(95, 'tv', 'alles über die 50er-Jahre-TV-Shows, TV- Western u.v.m.', 'http://www.fiftiesweb.com/fifties.htm', 'Gunnar', '2005-03-01'),
(96, 'tv', 'alle Titel der 145 TV-Westernserien der 40er bis 90er Jahre aus dem US-Fernsehen', 'http://www.sptddog.com/sotp/tvwesterns.html', 'Gunnar', '2005-03-01'),
(97, 'tv', 'viele Infos über US-TV-Shows der 60er Jahre', 'http://www.tvparty.com/', 'Gunnar', '2005-03-01'),
(98, 'tv', 'tolle Seiten über TV-Serien wie Hawaiian Eye,77 Sunset Strip oder Wagon Train', 'http://livelyset.tripod.com/', 'Gunnar', '2005-03-01'),
(99, 'tv', 'liebevolle Zusammenstellung von Spion-Filmender 60er Jahre', 'http://www.geocities.com/swcomer/cloak_and_dagger/index.html', 'Gunnar', '2005-03-01'),
(100, 'tv', 'alles über die Spielfilme von Elvis Presley', 'http://www.geocities.com/elvismdb/index1.html', 'Gunnar', '2005-03-01'),
(101, 'tv', 'Rock''n''Roll-Filme 1954-1959', 'http://www.sandlotshrink.com/movierockroll50s.htm', 'Gunnar', '2005-03-01'),
(102, 'tv', 'Rock''n''Roll-Filme 1960-1969', 'http://www.sandlotshrink.com/movierockroll60s.htm', 'Gunnar', '2005-03-01'),
(103, 'tv', 'alles über die Beach- u. Surf-Filme der frühen 60er Jahre mit Links auf viele Actor-Seiten', 'http://www.briansdriveintheater.com/beach.html', 'Gunnar', '2005-03-01'),
(104, 'tv', 'die Nachschlageseiten zu B-Monster-Movies der 50er Jahre und vielen Biografien', 'http://www.bmonster.com/indexa.html', 'Gunnar', '2005-03-01'),
(105, 'tv', 'Alles über den Kultkrimi Mit Schirme, Charme und Melone (161 Folgen, 1/1961-9/1969)', 'http://www.theavengers.tv/forever/', 'Gunnar', '2005-03-01'),
(106, 'tv', 'Alles über den TV-Westernklassiker The Big Valley (112 Folgen, 12.9.1965-19.5.1969)', 'http://classictv.about.com/cs/bigvalley/', 'Gunnar', '2005-03-01'),
(107, 'tv', 'Alles über die TV-Krimiserie Solo für O.N.K.E.L (105 Folgen, 22.9.1964-15.1.1968)', 'http://classictv.about.com/gi/dynamic/offsite.htm?site=http./.3A./.2F./.2Fmembers.aol.com./.2FWmkoen', 'Gunnar', '2005-03-01'),
(108, 'tv', 'Alles über den TV-Kultkrimi Kobra, übernehmen Sie (171 Folgen, 17.9.1966-30.3.1973)', 'http://classictv.about.com/cs/missionimpossible/', 'Gunnar', '2005-03-01'),
(109, 'tv', 'Alles über den TV-Westernklassiker Rauchende Colts (633 Folgen, 10.9.1955-1.9.1975)', 'http://cialab.ee.washington.edu/Marks-Stuff/Gunsmoke/Gunsmoke.html', 'Gunnar', '2005-03-01'),
(110, 'tv', 'Deutscher Episodenführer durch den TV-Krimiklassiker Columbo (seit 2/1968)', 'http://www.columbo-guide.de/', 'Gunnar', '2005-03-01'),
(111, 'tv', 'Viele Infos zu deutschen Filmen 1929-1972', 'http://www.deutscher-tonfilm.de', 'Gunnar', '2005-03-01'),
(112, 'tv', 'Russische Seiten über die Musikfilme der 50er bis 90er Jahre', 'http://www.rocknroll.aha.ru/drivein/1950.html', 'Gunnar', '2005-03-01'),
(113, 'tv', 'alles über Bezaubernde Jeannie(139 Folgen, 9/1965-5/1970)', 'http://www.geocities.com/carpet65/astro.html', 'Gunnar', '2005-03-01'),
(114, 'tv', 'alles über die Original-TV-Serie Raumschiff Enterprise (79 Folgen, 1966-1969)', 'http://www.tos.net/', 'Gunnar', '2005-03-01'),
(115, 'tv', 'die offiziellen Seiten über Bonanza (415 Folgen, 9/1959-4/1972)', 'http://www.bonanza1.com/', 'Gunnar', '2005-03-01'),
(116, 'tv', 'klasse Seiten über amerikanische Filmschauspieler der 30er bis 90er Jahre', 'http://www.movieactors.com/index2.htm', 'Gunnar', '2005-03-01'),
(117, 'tv', 'alles über die Westernserie Westlich von Santa FÃ© mit Chuck Connors u. Johnny Crawford (169 Folgen, 1958-4/1963)', 'http://members.tripod.com/~northfork/episodes.html', 'Gunnar', '2005-03-01'),
(118, 'tv', 'Hier werden die singenden B-Movie-Cowboys Roy Rogers und Gene Autry neben anderen vorgestellt', 'http://www.cowboypal.com/', 'Gunnar', '2005-03-01'),
(119, 'tv', 'alles über die 50er-Jahre-TV-Shows, TV- Western u.v.m.', 'http://www.fiftiesweb.com/fifties.htm', 'Gunnar', '2005-03-01'),
(120, 'tv', 'alle Titel der 145 TV-Westernserien der 40er bis 90er Jahre aus dem US-Fernsehen', 'http://www.sptddog.com/sotp/tvwesterns.html', 'Gunnar', '2005-03-01'),
(121, 'tv', 'viele Infos über US-TV-Shows der 60er Jahre', 'http://www.tvparty.com/', 'Gunnar', '2005-03-01'),
(122, 'tv', 'tolle Seiten über TV-Serien wie Hawaiian Eye,77 Sunset Strip oder Wagon Train', 'http://livelyset.tripod.com/', 'Gunnar', '2005-03-01'),
(123, 'tv', 'liebevolle Zusammenstellung von Spion-Filmender 60er Jahre', 'http://www.geocities.com/swcomer/cloak_and_dagger/index.html', 'Gunnar', '2005-03-01'),
(124, 'tv', 'alles über die Spielfilme von Elvis Presley', 'http://www.geocities.com/elvismdb/index1.html', 'Gunnar', '2005-03-01'),
(125, 'tv', 'Rock''n''Roll-Filme 1954-1959', 'http://www.sandlotshrink.com/movierockroll50s.htm', 'Gunnar', '2005-03-01'),
(126, 'tv', 'Rock''n''Roll-Filme 1960-1969', 'http://www.sandlotshrink.com/movierockroll60s.htm', 'Gunnar', '2005-03-01'),
(127, 'tv', 'alles über die Beach- u. Surf-Filme der frühen 60er Jahre mit Links auf viele Actor-Seiten', 'http://www.briansdriveintheater.com/beach.html', 'Gunnar', '2005-03-01'),
(128, 'tv', 'die Nachschlageseiten zu B-Monster-Movies der 50er Jahre und vielen Biografien', 'http://www.bmonster.com/indexa.html', 'Gunnar', '2005-03-01'),
(129, 'tv', 'Alles über den Kultkrimi Mit Schirme, Charme und Melone (161 Folgen, 1/1961-9/1969)', 'http://theavengers.tv/forever/', 'Gunnar', '2005-03-01'),
(130, 'tv', 'Alles über den TV-Westernklassiker The Big Valley (112 Folgen, 12.9.1965-19.5.1969)', 'http://classictv.about.com/cs/bigvalley/', 'Gunnar', '2005-03-01'),
(131, 'tv', 'Alles über die TV-Krimiserie Solo für O.N.K.E.L (105 Folgen, 22.9.1964-15.1.1968)', 'http://classictv.about.com/gi/dynamic/offsite.htm?site=http./.3A./.2F./.2Fmembers.aol.com./.2FWmkoen', 'Gunnar', '2005-03-01'),
(132, 'tv', 'Alles über den TV-Kultkrimi Kobra, übernehmen Sie (171 Folgen, 17.9.1966-30.3.1973)', 'http://classictv.about.com/cs/missionimpossible/', 'Gunnar', '2005-03-01'),
(133, 'tv', 'Alles über den TV-Westernklassiker Rauchende Colts (633 Folgen, 10.9.1955-1.9.1975)', 'http://cialab.ee.washington.edu/Marks-Stuff/Gunsmoke/Gunsmoke.html', 'Gunnar', '2005-03-01'),
(134, 'tv', 'Deutscher Episodenführer durch den TV-Krimiklassiker Columbo (seit 2/1968)', 'http://www.columbo-guide.de/', 'Gunnar', '2005-03-01'),
(135, 'tv', 'Viele Infos zu deutschen Filmen 1929-1972', 'http://www.deutscher-tonfilm.de', 'Gunnar', '2005-03-01'),
(136, 'tv', 'Russische Seiten über die Musikfilme der 50er bis 90er Jahre', 'http://www.rocknroll.aha.ru/drivein/1950.html', 'Gunnar', '2005-03-01'),
(137, 'clothes', 'Textilien nach original Schnitten, der einzige Anbieter authentischer Chinos. Ausserdem Deutschlands grösste Auswahl von originalen US- Krawatten aus den 40er und 50er Jahren.', 'http://www.juke-jive.de/', 'Gunnar', '2005-03-01'),
(138, 'clothes', 'Daddy-o''s, little shop, Where the coolest get their coolness ;)', 'http://www.daddyos.com/', 'Gunnar', '2005-03-01'),
(139, 'clothes', 'Wackycats, An honest to a real shop in Chicago, Il', 'http://www.wackycats.com/home.htm', 'Gunnar', '2005-03-01'),
(140, 'clothes', 'great site about vintage pattern', 'http://www.lilyabello.com/patternshop/', 'Gunnar', '2005-03-01'),
(141, 'clothes', 'Vintage clothing, jewelry, hats, and accessories to the discriminating collector, professional costumer, stylist, swing dancers, and lovers of fashion across the USA!', 'http://vintagesilhouettes.com/', 'Gunnar', '2005-03-01'),
(142, 'clothes', 'poppin'' shoppin'', real web based clothing shop', 'http://www.zootsuitstore.com/Shopping/default.asp', 'Gunnar', '2005-03-01'),
(143, 'hitparaden', 'Von Joel Whitburn stammen u.a. die Chart-Bücher Top Pop Records, oder aber das bereits legendäre Reviews 1958 (welches jetzt wieder zu haben ist). Diese Webseite führt das komplette Programm, z', 'http://www.recordresearch.com', 'Gunnar', '2005-03-01'),
(144, 'hitparaden', 'Paul Pelletier''s Seiten mit blues- und Schallplattenmeldungen, Bücherfacts u.v.m.', 'http://www.brightguy.demon.co.uk', 'Gunnar', '2005-03-01'),
(145, 'hitparaden', 'Charts-Archiv 1980-2000', 'http://www.charthistory.de/', 'Gunnar', '2005-03-01'),
(146, 'hitparaden', 'Osborne ist der Herausgeber der Price Guides (Rockin Records), sowie der Complete Library of American ...', 'http://www.jerryosborne.com', 'Gunnar', '2005-03-01'),
(147, 'instruments', 'Special for vintage drummers', 'http://www.mastermusicians.com/kingskorner.htm', 'Gunnar', '2005-03-01'),
(148, 'instruments', 'Standel Amps used by Chet Atkins, Wes Montgomery, Merle Travis, Joe Maphis, Buddy Emmons and many other musical greats.', 'http://www.requisiteaudio.com/standel.html', 'Gunnar', '2005-03-01'),
(149, 'instruments', 'Special for vintage drummers 2', 'http://home.t-online.de/home/IngoWinterberg/trixon2.htm', 'Gunnar', '2005-03-01'),
(150, 'instruments', 'A fantastic site to buy old technical stuff like mics, amps, tv''s, radio''s', 'http://www.oaktreeent.com/', 'Gunnar', '2005-03-01'),
(151, 'instruments', 'Guitarsound like Gene''s Cliff Callup?', 'http://www.rockabillyhall.com/soundlikecliff.html', 'Gunnar', '2005-03-01'),
(152, 'instruments', 'Old microphones', 'http://www.digitalvideo.com/mics.htm', 'Gunnar', '2005-03-01'),
(153, 'instruments', 'Old radios', 'http://antiqueradio.org/index.html', 'Gunnar', '2005-03-01'),
(154, 'instruments', 'Vintage Gretsch guitar site', 'http://www.provide.net/~cfh/gretsch.html', 'Gunnar', '2005-03-01'),
(155, 'instruments', 'Vintage Gibson guitar site', 'http://www.provide.net/~cfh/gibson.htm', 'Gunnar', '2005-03-01'),
(156, 'instruments', 'Vintage Fender guitar site & amps', 'http://www.provide.net/~cfh/fender.html', 'Gunnar', '2005-03-01'),
(157, 'instruments', 'For vintage drummers only', 'http://www.vintagelogos.com/', 'Gunnar', '2005-03-01'),
(158, 'instruments', 'For double bass player (Kontrabass)', 'http://www.gollihur.com/kkbass/basslink.htm', 'Gunnar', '2005-03-01'),
(159, 'labels', 'finnische Seite. Hier finden sich u.a. ziemlich komplette Diskografien von Charlie Feathers, Glen Glenn und der Plattenfirma Crest Records.', 'http://www.pcuf.fi/~tapiov/discogra.htm', 'Gunnar', '2005-03-01'),
(160, 'labels', 'Sun Records!', 'http://www.sunstudio.com/', 'Gunnar', '2005-03-01'),
(161, 'labels', 'Ace Rec., GB', 'http://www.acerecords.co.uk/', 'Gunnar', '2005-03-01'),
(162, 'labels', 'Rockhouse Records, Niederlande', 'http://www.musicmailexpress.com/GR/GR.html', 'Gunnar', '2005-03-01'),
(163, 'labels', 'Hier gibt es alles über die Buffalo Bop Rockabilly-Sampler.', 'http://www.buffalobop.2xt.de', 'Gunnar', '2005-03-01'),
(164, 'labels', 'Dahinter verbirgt sich die Plattenfirma Collectable Records in Philadelphia. Eine gute Seite mit Suchfunktion. Natürlich kann man die CDs auch dort bestellen. Zumindest ist man auf dem aktuellen Stan', 'http://www.oldies.com', 'Gunnar', '2005-03-01'),
(165, 'labels', 'Bear Family', 'http://www.bear-family.de', 'Gunnar', '2005-03-01'),
(166, 'labels', 'A C E', 'http://acereco01.uuhost.uk.uu.net/', 'Gunnar', '2005-03-01'),
(167, 'labels', 'Sleazy Records Spain', 'http://es.geocities.com/Sleazyrecords/', 'Gunnar', '2005-03-01'),
(168, 'labels', 'Goofin'' records', 'http://www.goofinrecords.com/', 'Gunnar', '2005-03-01'),
(170, 'magazines', 'Dead Flowers-Webmagazin mit Infos über Punk, Psychobilly, Garagenbands der 50er Jahre bis heute u.v.m.', 'http://www.members.aol.com/Shake6677/DeadFlowers.html', 'Gunnar', '2005-03-01'),
(171, 'magazines', 'u.a. Rockin''Rollin'' Magazin und Andy Widder-Produkte..', 'http://www.rockin-rollin.com', 'Gunnar', '2005-03-01'),
(172, 'magazines', 'Dynamite-Magazin', 'http://www.rockabilly.de', 'Gunnar', '2005-03-01'),
(173, 'magazines', 'American Music Magazine, Schweden', 'http://user.tninet.se/~bpw336i', 'Gunnar', '2005-03-01'),
(174, 'magazines', 'Jamboree-Magazin aus Italien..', 'http://www.oldwoogies.com/jamboree.htm', 'Gunnar', '2005-03-01'),
(175, 'magazines', 'Münchener Rockin'' Fifties-Magazin', 'http://www.rockinfifties.de/magazine.html', 'Gunnar', '2005-03-01'),
(176, 'magazines', 'hier werden Bücher, Magazine etc. zum Thema Rock''n''Roll in England vorgestellt!', 'http://rhis.co.uk/reading/', 'Gunnar', '2005-03-01'),
(177, 'misc', 'Pfingsten - Rock''n''Roll Weekender in Walldorf', 'http://www.weekender-walldorf.de/', 'Gunnar', '2005-03-01'),
(178, 'misc', 'Beatles, Star-Club und mehr', 'http://www.center-of-beat.com', 'Gunnar', '2005-03-01'),
(179, 'misc', 'Alles über die Musik Louisianas', 'http://www.satchmo.com/index2.html', 'Gunnar', '2005-03-01'),
(180, 'misc', 'Nach Kategorien getrennt, gibt es hier Musik der Fifties zum Hören, Auktionslisten und Stories', 'http://www.recordfinders.com/', 'Gunnar', '2005-03-01'),
(181, 'misc', 'Fuller Up - The Dead Musican Directory; hier erfährt man vom Ableben vieler Musiklegenden, aufgelistet nach diversen Todesursachen', 'http://www.elvisprelvis.com/fullerup.htm', 'Gunnar', '2005-03-01'),
(182, 'misc', 'Rock''n''Roll und Fifties Lifestyle', 'http://www.freia.org', 'Gunnar', '2005-03-01'),
(183, 'misc', 'Video Newsletter', 'http://www.thevideobeat.com', 'Gunnar', '2005-03-01'),
(184, 'misc', 'The page covers, artists + Bands, events, fifties lifestyle, jukeboxes, magazines, clubs etc.', 'http://rockabilly.boogolinkgs.nl', 'Gunnar', '2005-03-01'),
(185, 'misc', 'Thema Rockabilly', 'http://homepages.compuserve.de/rockabilly01/homepage.htm', 'Gunnar', '2005-03-01'),
(186, 'misc', 'The Bop Won''t Stop-Links zum Thema Rockabilly', 'http://home.t-online.de/home/fnaehri/boplinks.htm', 'Gunnar', '2005-03-01'),
(187, 'misc', 'Links zum Thema Doo-Wop', 'http://www.wedoowop.com/links.html', 'Gunnar', '2005-03-01'),
(188, 'misc', 'Biografien vieler Doo-Wop-Gruppen!', 'http://www.history-of-rock.com/DooWopSound.htm', 'Gunnar', '2005-03-01'),
(189, 'misc', 'Alles zum Thema Surf Music', 'http://www.history-of-rock.com/surf_music.htm', 'Gunnar', '2005-03-01'),
(190, 'misc', 'Oldies Unlimited = Home of DooWop, R''n''R und R&B mit vielen Hinweisen.', 'http://www.nb.net/~glarkin/', 'Gunnar', '2005-03-01'),
(191, 'misc', 'Ãœbersicht, verschiedene Web-Sites, vorwiegend 60er Jahre', 'http://www.legacylinks.com', 'Gunnar', '2005-03-01'),
(192, 'misc', 'Scottyboy''s Blues Au Go Go = alles zum Thema Blues und R&B!', 'http://www.geocities.com/SunsetStrip/4466/', 'Gunnar', '2005-03-01'),
(193, 'misc', 'Country Music Hall Of Fame', 'http://www.halloffame.org/', 'Gunnar', '2005-03-01'),
(194, 'misc', 'hier gibt es eine recht gute Sammlung von mehr oder weniger interessanten Links.', 'http://acereco01.uuhost.uk.uu.net/hotlinks/hotlinks.html', 'Gunnar', '2005-03-01'),
(195, 'misc', 'gehört mit zur Rock & Roll Hall of Fame (HOF) in Cleveland, Ohio. Hier findet man alle Personen (auch die, die mit Rock & Roll nichts zu tun haben oder hatten) die sich derzeit in der Ruhmeshalle bef', 'http://www.rockhall.com', 'Gunnar', '2005-03-01'),
(196, 'misc', 'Broadcast Music Incorporated ist dann interessant, wenn man den Komponisten eines bestimmten Liedes sucht (den vollständigen Namen, versteht sich).', 'http://www.bmi.com', 'Gunnar', '2005-03-01'),
(197, 'misc', 'dürfte die grösste und umfassendste Webseite für Rock & Roll und Rockabilly-Freunde sein. Allerdings ist sie kommerzieller Natur, was das Niveau dieser Seite und ihrem Betreiber aber nicht senkt', 'http://www.rockabillyhall.com', 'Gunnar', '2005-03-01'),
(198, 'misc', 'wer''s mag. Schön bunt und typisch amerikanisch, aber auch unterhaltsam. Ist aber Geschmackssache.', 'http://www.fiftiesweb.com', 'Gunnar', '2005-03-01'),
(199, 'misc', 'hier geht''s um die guten alten Oldies. Die Webseite ist mit Musik untermalt (Midi). Ausserdem gibt es hier auch Links zu den Webseiten von Danny & the Juniors und Frankie Lymon & the Teenagers.', 'http://www.oldiesforever.net', 'Gunnar', '2005-03-01'),
(200, 'misc', 'die Michigan Country Music Hall Of Fame', 'http://www.angelfire.com/mi/stutesman/halloffame.html', 'Gunnar', '2005-03-01'),
(201, 'misc', 'die besten Bluegrass-Links mit vielen Artists-Infos und Veranstaltungshinweisen', 'http://www.banjo.com/BG-links.html', 'Gunnar', '2005-03-01'),
(202, 'misc', 'die Homepage der Western Swing-Szene mit News und Hinweisen', 'http://www.westernswing.com/', 'Gunnar', '2005-03-01'),
(203, 'misc', 'wolfman himself, mp3s, jingles und vieles mehr', 'http://www.wolfmanjack.org/', 'Gunnar', '2005-03-01'),
(204, 'misc', 'die Michigan Country Music Hall Of Fame', 'http://www.angelfire.com/mi/stutesman/halloffame.html', 'Gunnar', '2005-03-01'),
(205, 'misc', 'Keep It Country- Wissenswertes und viele Biografien über Country-Stars', 'http://www.talentondisplay.com/KIC.html', 'Gunnar', '2005-03-01'),
(206, 'misc', '', 'http://www.pcuf.fi/%7Etapiov/links.htm', 'Gunnar', '2005-03-01'),
(207, 'misc', '', 'http://www.rockabillyhall.com/RABLINKS.html', 'Gunnar', '2005-03-01'),
(208, 'misc', '', 'http://www.rockabillyhall.com/RABLINKS2.html', 'Gunnar', '2005-03-01'),
(209, 'misc', '', 'http://home.earthlink.net/~jaymar41/index.html', 'Gunnar', '2005-03-01'),
(210, 'misc', 'Veranstaltungsservice Burgwedel', 'http://www.vision-of-events.de/sites/frames1.htm', 'Gunnar', '2005-03-01'),
(211, 'misc', 'Rockabilly Weekender Burgwedel', 'http://www.rockabilly-circus.de/', 'Gunnar', '2005-03-01'),
(212, 'misc', 'Original BEAT CLUB Fotos und Merchandiese-Artikel', 'http://www.center-of-beat.com/', 'Gunnar', '2005-03-01'),
(213, 'misc', 'Promotion u. Veranstaltungsservice', 'http://www.music-enterprises.de/intro.html', 'Gunnar', '2005-03-01'),
(214, 'oldtimers', 'Wonderlful Oldsmobile Advertising and others', 'http://www.oldsads.com', 'Gunnar', '2005-03-01'),
(215, 'oldtimers', 'Books & videos about, well, old cars', 'http://www.carsandstripes.com', 'Gunnar', '2005-03-01'),
(216, 'oldtimers', 'Fantastic site about the Ford Edsel', 'http://www.edsel.com/', 'Gunnar', '2005-03-01'),
(217, 'oldtimers', 'Vintage Vacation Trailers, Seller and Organisator of a Rally', 'http://www.vintage-vacations.com/', 'Gunnar', '2005-03-01'),
(218, 'oldtimers', 'Filmsequenzen aus alten raren Movis, in denen Imperial Cars zu sehen sind', 'http://www.imperialclub.com/', 'Gunnar', '2005-03-01'),
(219, 'queens', 'Alles über Mae West (1893-1980)', 'http://www.maewest.net/', 'Gunnar', '2005-03-01'),
(220, 'queens', 'Alles über Marilyn Monroe (1926-1962)', 'http://www.marilynmonroe.com/', 'Gunnar', '2005-03-01'),
(221, 'queens', 'Die offiziellen Seiten von Ann-Margret', 'http://www.ann-margret.com/', 'Gunnar', '2005-03-01'),
(222, 'queens', 'Alles über Jayne Mansfield (1933-1967)', 'http://www.dirtywett.com/thepinkpages/biography.html', 'Gunnar', '2005-03-01'),
(223, 'queens', 'Die offiziellen, teilweise nicht ganz jugendfreien Seiten über Mamie Van Doren', 'http://www.mamievandoren.com/', 'Gunnar', '2005-03-01'),
(224, 'queens', 'Alles über B-Movie-Queen Cleo Moore (1928-1973)', 'http://www.angelfire.com/la/cleomoore/', 'Gunnar', '2005-03-01'),
(225, 'queens', 'Alles über Glamour-Girl Joi Lansing (1928-1972)', 'http://www.briansdriveintheater.com/joilansing.html', 'Gunnar', '2005-03-01'),
(226, 'queens', 'Alles über Glamour-Girl Betty Brosmer', 'http://www.bettyweider.com/', 'Gunnar', '2005-03-01'),
(227, 'queens', 'Alles über Stella Stevens', 'http://www.stellastevens.com/', 'Gunnar', '2005-03-01'),
(228, 'queens', 'Alles über Kult-Queen Bettie Page', 'http://www.grrl.com/betty.html', 'Gunnar', '2005-03-01'),
(229, 'queens', 'Alles über Dorothy Dandridge (1922-1965)', 'http://home.hiwaay.net/~oliver/dandridge.html', 'Gunnar', '2005-03-01'),
(230, 'queens', 'Alles über Gina Lollobrigida in deutsch', 'http://www.lollobrigida.de/ginaII/index.html', 'Gunnar', '2005-03-01'),
(231, 'queens', 'Prisma-Biografie in deutsch über Anita Ekberg', 'http://www.prisma-online.de/tv/person.html?pid=anita_ekberg', 'Gunnar', '2005-03-01'),
(232, 'queens', 'Alles über Glamour-Girl Julie Newmar', 'http://www.geocities.com/Hollywood/Academy/8035/', 'Gunnar', '2005-03-01'),
(233, 'queens', 'Fotos und Infos über Annette Funicello mit zahlreichen Links zu Girl Groups u. Sängerinnen', 'http://ggf.tripod.com/annette.html', 'Gunnar', '2005-03-01'),
(234, 'queens', 'Super-Seiten über Schauspielerinnen und Beauties der 60er Jahre wie Sandra Dee, Donna Loren, Connie Stevens, Yvonne Craig, Dolores Hart, Claudia Cardinale, Sophia Loren u.v.m.', 'http://www.swinginchicks.com/workinitfordaddy.htm', 'Gunnar', '2005-03-01'),
(235, 'queens', 'Fotos und mehr über legendäre Stars wie Marlene Dietrich, Sophia Loren, Jane Russell, Audrey Hepburn, Rita Hayworth oder Jean Harlow', 'http://www.bombshells.com/', 'Gunnar', '2005-03-01'),
(236, 'queens', 'Tolle Seiten über Glamour-Queens', 'http://www.glamourgirlsofthesilverscreen.com/', 'Gunnar', '2005-03-01'),
(237, 'queens', 'Offizielle Seiten von Glamour-Fifties-Queen Bettie Page', 'http://www.bettiepage.com/', 'Gunnar', '2005-03-01'),
(238, 'queens', 'Offizielle Seiten von Burlesque-Fifties-Queen Tempest Storm', 'http://www.tempeststorm.com', 'Gunnar', '2005-03-01'),
(239, 'queens', 'Seiten mit vielen Infos zu Filmsternchen Deborah Walley', 'http://www.briansdriveintheater.com/deborahwalley.html', 'Gunnar', '2005-03-01'),
(240, 'radio', 'Infos, Playlisten etc. von Rainer Umlauf, Bremen - Offener Kanal', 'http://www.members.aol.com/rainerhb/musik.htm', 'Gunnar', '2005-03-01'),
(241, 'radio', 'Fool''s Paradise, music between low brow and no brow', 'http://www.wfmu.org/FPWR/', 'Gunnar', '2005-03-01'),
(242, 'sellers', 'Rockin'' Rudi, Wien, The Rockin'' Fifties', 'http://www.rarerecords.at', 'Gunnar', '2005-03-01'),
(243, 'sellers', 'Informationen über die echten Schallplatten: den 78er, 78rpm oder Schellackplatten. Informationen über die Geschichte der Tonaufzeichnung, Tips zum Kauf von 78er, das Buch Rocking 78s.', 'http://www.rocking78s.de', 'Gunnar', '2005-03-01'),
(244, 'sellers', 'Here you''ll find rare Rockabilly 78s too look at and to buy them as well.', 'http://www.rockabilly78s.com', 'Gunnar', '2005-03-01'),
(245, 'swing', 'Event Site from and about NRW Germany', 'http://www.boogie-lindy-swing.de/', 'Gunnar', '2005-03-01'),
(246, 'swing', 'fantastic font site / find shareware too', 'http://www.fontdiner.com/', 'Gunnar', '2005-03-01'),
(247, 'swing', 'font site', 'http://www.havanastreet.com/', 'Gunnar', '2005-03-01'),
(248, 'swing', 'a font links site / you can find cool stuff here too', 'http://desktoppublishing.com/fonts-free.html', 'Gunnar', '2005-03-01'),
(249, 'texte', 'auch nicht uninteressant was es hier aus Holland gibt, z.B. eine lange Liste von R&R-Songtexten', 'http://www.rockabilly.nl', 'Gunnar', '2005-03-01'),
(250, 'texte', 'Gene Vincent Lyrics', 'http://home.wanadoo.nl/rock_and_roll/genesong.htm', 'Gunnar', '2005-03-01'),
(251, 'tv', 'alles über Bezaubernde Jeannie(139 Folgen, 9/1965-5/1970)', 'http://www.geocities.com/carpet65/astro.html', 'Gunnar', '2005-03-01'),
(252, 'tv', 'alles über die Original-TV-Serie Raumschiff Enterprise (79 Folgen, 1966-1969)', 'http://www.tos.net/', 'Gunnar', '2005-03-01'),
(253, 'tv', 'die offiziellen Seiten über Bonanza (415 Folgen, 9/1959-4/1972)', 'http://www.bonanza1.com/', 'Gunnar', '2005-03-01'),
(254, 'tv', 'klasse Seiten über amerikanische Filmschauspieler der 30er bis 90er Jahre', 'www.movieactors.com/index2.htm', 'Gunnar', '2005-03-01'),
(255, 'tv', 'alles über die Westernserie Westlich von Santa Fee mit Chuck Connors u. Johnny Crawford (169 Folgen, 1958-4/1963)', 'http://members.tripod.com/~northfork/episodes.html', 'Gunnar', '2005-03-01'),
(256, 'tv', 'Hier werden die singenden B-Movie-Cowboys Roy Rogers und Gene Autry neben anderen vorgestellt', 'http://www.cowboypal.com/', 'Gunnar', '2005-03-01'),
(257, 'tv', 'alles über die 50er-Jahre-TV-Shows, TV- Western u.v.m.', 'http://www.fiftiesweb.com/fifties.htm', 'Gunnar', '2005-03-01'),
(258, 'tv', 'alle Titel der 145 TV-Westernserien der 40er bis 90er Jahre aus dem US-Fernsehen', 'http://www.sptddog.com/sotp/tvwesterns.html', 'Gunnar', '2005-03-01'),
(259, 'tv', 'viele Infos über US-TV-Shows der 60er Jahre', 'http://www.tvparty.com/', 'Gunnar', '2005-03-01'),
(260, 'tv', 'tolle Seiten über TV-Serien wie Hawaiian Eye,77 Sunset Strip oder Wagon Train', 'http://livelyset.tripod.com/', 'Gunnar', '2005-03-01'),
(261, 'tv', 'liebevolle Zusammenstellung von Spion-Filmender 60er Jahre', 'http://www.geocities.com/swcomer/cloak_and_dagger/index.html', 'Gunnar', '2005-03-01'),
(262, 'tv', 'alles über die Spielfilme von Elvis Presley', 'http://www.geocities.com/elvismdb/index1.html', 'Gunnar', '2005-03-01'),
(263, 'tv', 'Rock''n''Roll-Filme 1954-1959', 'http://www.sandlotshrink.com/movierockroll50s.htm', 'Gunnar', '2005-03-01'),
(264, 'tv', 'Rock''n''Roll-Filme 1960-1969', 'http://www.sandlotshrink.com/movierockroll60s.htm', 'Gunnar', '2005-03-01'),
(265, 'tv', 'alles über die Beach- u. Surf-Filme der frühen 60er Jahre mit Links auf viele Actor-Seiten', 'http://www.briansdriveintheater.com/beach.html', 'Gunnar', '2005-03-01'),
(266, 'tv', 'die Nachschlageseiten zu B-Monster-Movies der 50er Jahre und vielen Biografien', 'http://www.bmonster.com/indexa.html', 'Gunnar', '2005-03-01'),
(267, 'tv', 'Alles über den Kultkrimi Mit Schirme, Charme und Melone (161 Folgen, 1/1961-9/1969)', 'http://theavengers.tv/forever/', 'Gunnar', '2005-03-01'),
(268, 'tv', 'Alles über den TV-Westernklassiker The Big Valley (112 Folgen, 12.9.1965-19.5.1969)', 'http://classictv.about.com/cs/bigvalley/', 'Gunnar', '2005-03-01'),
(269, 'tv', 'Alles über die TV-Krimiserie Solo für O.N.K.E.L (105 Folgen, 22.9.1964-15.1.1968)', 'http://classictv.about.com/gi/dynamic/offsite.htm?site=http./.3A./.2F./.2Fmembers.aol.com./.2FWmkoen', 'Gunnar', '2005-03-01'),
(270, 'tv', 'Alles über den TV-Kultkrimi Kobra, übernehmen Sie (171 Folgen, 17.9.1966-30.3.1973)', 'http://classictv.about.com/cs/missionimpossible/', 'Gunnar', '2005-03-01'),
(271, 'tv', 'Alles über den TV-Westernklassiker Rauchende Colts (633 Folgen, 10.9.1955-1.9.1975)', 'http://cialab.ee.washington.edu/Marks-Stuff/Gunsmoke/Gunsmoke.html', 'Gunnar', '2005-03-01'),
(272, 'tv', 'Deutscher Episodenführer durch den TV-Krimiklassiker Columbo (seit 2/1968)', 'http://www.columbo-guide.de/', 'Gunnar', '2005-03-01'),
(273, 'tv', 'Viele Infos zu deutschen Filmen 1929-1972', 'http://www.deutscher-tonfilm.de', 'Gunnar', '2005-03-01'),
(274, 'tv', 'Russische Seiten über die Musikfilme der 50er bis 90er Jahre', 'http://www.rocknroll.aha.ru/drivein/1950.html', 'Gunnar', '2005-03-01'),
(275, 'texte', 'Rockabilly Veranstaltungen', 'http://www.rockabillyrumble.nl', 'waltraut', '2005-03-01'),
(276, 'artists', 'Gina Lollobrigida', 'http://www.ginalollobrigida.republika.pl/.nl', 'waltraut', '2005-03-01'),
(277, 'miscalaneous', 'Rockabilly Veranstaltungen', 'http://www.rockabillyrumble.nl', 'waltraut', '2005-03-01'),
(278, 'artists', 'Andy Lee  + Band (Rock''n''Roll / Rockabilly / Country)', 'http://www.andylee.de', 'waltraut', '2005-03-01'),
(279, 'sellers', 'tolle + günstige CDs für Rockabilly, Hillbilly, Psychobilly, Country, Surf CDs + Plattelabel', 'http://www.tcy-records.com', 'waltraut', '2005-03-01'),
(280, 'radio', 'Lizensiertes Webradio', 'http://www.doowopdream.com', 'waltraut', '2005-03-01'),
(281, 'miscalaneous', 'Alles über Doo Wop Musik', 'http://www.german-doowop-king.de', 'waltraut', '2005-03-01'),
(282, 'miscalaneous', 'Alles zum Thema Doo Wop', 'http://www.doowopdreamteam.com', 'waltraut', '2005-03-01'),
(283, 'radio', 'Infos, Playlisten etc. von Rainer Umlauf, Bremen - Offener Kanal', 'http://rainerhb.de', 'waltraut', '2005-03-01'),
(284, 'artists', 'Gut gemachte, persönliche Fan-Seite über Buddy Holly', 'http://www.buddyholley.net', 'waltraut', '2005-03-01'),
(286, 'artists', '17. Festival Golden Oldies - Deutschlands schönste Oldiefete - vom 28.-30 Juli 2006 in Wettenberg: auf 9 Bühnen 50 Musikgruppen, Zeitreise in die 50er, 60er, 70er Jahre', 'http://www.golden-oldies.de', 'waltraut', '2005-03-01'),
(287, 'artists', 'Elvis Presley Artikel Versand, Collectors Service, Bamberg', 'http://www.collectors-service.de.vu', 'waltraut', '2005-03-01'),
(288, 'artists', 'Band aus München: Rock''n''Roll, Rockabilly, Surf & Cowpunk since 1980', 'http://www.thecontinentals.de', 'waltraut', '2005-03-01'),
(289, 'artists', 'Elvis Presley Fan Club Interessengemeinschaft', 'http://www.elvispresley-fanclub.de/41938.html', 'waltraut', '2005-03-01'),
(290, 'artists', 'Elvis-Interpret "CHRIS KAYE"  aus Wien', 'http://www.chriskaye.at', 'waltraut', '2005-03-01'),
(291, 'miscalaneous', 'das Forum für die Subkultur', 'http://www.sub-cultures.de', 'waltraut', '2005-03-01'),
(292, 'miscalaneous', 'RocknRoll Weekender in Walldorf', 'http://www.walldorf-weekender.de', 'waltraut', '2005-03-01');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `pageRef` int(11) NOT NULL,
  `priority` int(255) NOT NULL COMMENT 'The order to show the menu items',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `navigation`
--

INSERT INTO `navigation` (`id`, `title`, `pageRef`, `priority`) VALUES
(2, 'Termine', 7, 0),
(7, 'Bestellen', 11, 4),
(8, 'History', 12, 1),
(9, 'Gallery', 13, 4),
(10, 'Links', 14, 5),
(11, 'Gästebuch', 15, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `pages`
--

INSERT INTO `pages` (`id`, `identifier`, `menuRef`, `title`, `paragraphs`, `timestamp`, `cache`) VALUES
(1, 'index', 1, 'Willkommen', '27,1,,,', '2011-03-12 00:44:54', NULL),
(7, 'events', 4, 'Termine', '23,3,', '2011-04-03 12:10:30', NULL),
(11, 'order', 11, 'Bestellen', 'NULL', '2011-05-07 17:52:10', NULL),
(12, 'History', 12, 'history', '26,27,28,,', '2011-06-15 00:52:07', NULL),
(13, 'plogger', 14, 'Gallery', NULL, '2011-08-01 21:40:39', NULL),
(14, 'links', NULL, 'Links', NULL, '2012-02-15 23:43:56', NULL),
(15, 'guestbook', 15, 'Gästebuch', NULL, '2012-03-25 20:35:39', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `paragraphs`
--

CREATE TABLE IF NOT EXISTS `paragraphs` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `type` int(11) NOT NULL,
  `meta` varchar(255) character set utf8 collate utf8_bin default NULL,
  `content` text character set utf8 collate utf8_bin,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

--
-- Daten für Tabelle `paragraphs`
--

INSERT INTO `paragraphs` (`id`, `title`, `type`, `meta`, `content`) VALUES
(1, '', 0, 'height=300;table=;category=', 0x3c6469763e3c703e4469652049646565207a756d20e2809e526f636be280996ee28099526f6c6c204d7573696b6d6167617a696ee2809c2077757264652031393737206175662065696e656d204f6c64656e62757267657220466c6f686d61726b74203c7374726f6e673e6765626f72656e3c2f7374726f6e673e2e20446f72743c7370616e207374796c653d22746578742d6465636f726174696f6e3a20756e6465726c696e653b223e2074726166656e207369636820436c6175732d4469657465722052c3b6676c696e2c2057696c667269656420427572746b6520756e64204865696e7a2d47c3bc6e74686572204861727469672e20416c6c65206472656920776172656e2073656974204a616872656e20526f636be280996ee28099526f6c6c2d46616e7320756e6420617563682053616d6d6c657220656e74737072656368656e64657220536368616c6c706c617474653c2f7370616e3e6e2e204162657220616c6c65206472656920776172656e2062697320646168696e206d697420696872656d20486f62627920616c6c65696e206175662077656974657220466c75722e204461636874653c656d3e6e207369652e20446572205a7566616c6c20686174746520736965206e756e207a7573616d6d656e676566c3bc68727420756e64206175662064696573656d20466c6f686d61726b742062656b6c616774656e207369652c2064617373206573206b65696e65204c697465726174757220756e64206b65696e65205a65697473636872696674656e20c3bc62657220526f636be280996ee28099526f6c6c20676962742e436c6175732d4469657465722052c3b6676c696e20776172204275636862696e6465722c204865696e7a2d47c3bc6e746865722048617274696720447275636b6572202d20756e6420736f20776172207363686e656c6c206469652049646565206765626f72656e2c2073656c6273742065696e204d6167617a696e206865726175737a756272696e67656e2e20446965206572737465204e756d6d6572207374656c6c746520436c6175732d446965743c2f656d3e65722052c3b6676c696e20756e7465722064656d204e616d656e20e2809e524f434be2809c20613c7374726f6e673e6c6c65696e207a7573616d6d656e2c20646f6368206265726569747320616220646572207a77656974656e204175736761626520776172656e2064616e6e204865696e7a2d47c3bc6e7468657220483c2f7374726f6e673e617274696720756e642057696c667269656420427572746b652064616265692e2045732077757264656e2048616e647a657474656c206765647275636b7420756e642062656920526f636be280996ee28099526f6c6c2d4b6f6e7a657274656e206d6974204661747320446f6d696e6f20756e6420436875636b20426572727920696e204272656d656e2c2048616d6275726720756e64204dc3bc6e73746572207665727465696c742e2045696e204272656d657220536368616c6c706c617474656e76657273616e64206c65677465207365696e656d204b6174616c6f67206562656e66616c6c732042657374656c6c7a657474656c2062656920756e6420736f206b616d656e206469652065727374656e207a77656968756e646572742041626f6e6e656e74656e207a7573616d6d656e2e3c2f703e3c2f6469763e),
(2, 'The 2nd entry', 0, 'height=300;table=;category=;image=2', 0x4f6b617920616c736f20646965732069737420646572205a57454954452045696e747261672065696e657220686f6666656e746c696368206c61656e676572656e20476573636869636874652075656265722064696573657320434d532e20486162206d6972206a612061756368206d616c20776965646572206e757220766f7267656e6f6d6d656e20616c6c65732073656c626572207a75206d616368656e2e2e2e206861686168612e53636865697373652e6e616a612c2076657273756368656e20776972732065696e6766616368206d616c2065696e66616368207a752068616c74656e2c2077612e),
(3, 'September', 2, 'height=300;table=events;category=September', NULL),
(4, 'Somethign entirely unrelated', 0, 'height=400;table=;category=;image=4', NULL),
(7, 'Die Geschichte', 0, 'height=300;table=;category=', NULL),
(8, 'Noch ein geschichtsabsatz', 0, 'height=300;table=;category=', NULL),
(9, 'Noch ein neuer Absatz', 0, 'height=500;table=;category=;image=9', ''),
(10, 'Noch ein Versuch', 0, 'height=300;table=;category=;image=10', NULL),
(11, 'Huah Gaehn', 0, 'height=300;table=;category=;image=11', NULL),
(12, 'Nur um das nochmal eben klarzustllen', 0, 'height=330;table=;category=;image=12', 0x3c703e6f6b2c2066756567656e2077697220646f6368206e6f6368206d616c206562656e2065696e2077656e696720636f6e74656e742065696e2e2e2e0d0a0d0a4f4b617920756e64206a65747a74206e6f6368206d6974206c697374653a0d0a0d0a0d0a3c756c3e0d0a3c6c693e45696e2070756e6b743c2f6c693e0d0a3c6c693e45696e20322e2070756e6b743c2f6c693e0d0a3c6c693e45696e20332e2070756e6b743c2f6c693e0d0a3c2f756c3e0d0a3c2f703e),
(13, 'mush absatz', 0, 'height=300;table=;category=;image=13', NULL),
(14, 'hm, oder so evtl', 0, 'height=300;table=;category=;image=14', NULL),
(15, 'Sollte es das etwa...', 0, 'height=300;table=;category=;image=15', NULL),
(16, 'Neu macht Mai', 0, 'height=300;table=;category=;image=16', NULL),
(17, 'So und jetzt mal NUR fuer geschichte', 0, 'height=500;table=;category=;image=17', ''),
(18, 'fadfsdgfsdg', 0, 'height=300;table=;category=;image=18', NULL),
(19, 'Sollte es etwa ...nummer 2', 0, 'height=300;table=;category=;image=19', NULL),
(20, 'Neues Heft erschienen...', 0, 'height=300;table=;category=;image=20', NULL),
(21, 'LANoire', 0, 'height=600;table=;category=;image=21', 0x45696e2077656e696720436f6e74656e743f),
(22, 'ein versuch', 0, 'height=600;table=;category=;image=22', 0x62616c6421),
(23, 'Octubre, und +mehr', 2, 'height=500;table=events;category=Oktober', 0x3c6469763e3c2f6469763e),
(24, 'Herausgeber und Redaktionsleitung', 0, 'height=300;table=;category=;image=24', 0x67656c65726e7465722042756368647275636b65722c20686575746520616c7320536163686265617262656974657220696e2065696e6572204f6c64656e62757267657220447275636b657265692074c383c2a47469672e20536569742064656e20736563687a69676572204a616872656e20536368616c6c706c617474656e73616d6d6c65722c2053636877657270756e6b74653a20427564647920486f6c6c792c20526f636b202620526f6c6c2c20426561742e204175c383c5b8657264656d2053616d6d6c657220766f6e2074797069736368656e20476567656e7374c383c2a46e64656e206175732064656e2046c383c2bc6e667a69676572204a616872656e2c204dc383c2b662656c2c20566173656e2c20526164696f7320752e612e2d2064696573206dc383c2bc6e6465746520696e20646572204d697467657374616c74756e672065696e6573204d757365756d7320696e2064656d204f72742042656e7468756c6c656e20626569204f6c64656e627572672e202835306572204a61687265204175737374656c6c756e6729204175666772756e64207365696e657320225370657a69616c77697373656e732220c383c2bc62657220427564647920486f6c6c7920c383c2bc6265726e61686d20657220646965204265726174756e6720646573204d75736963616c7320c3a2e282acc5be4275646479c3a2e282acc59320696e2048616d627572672e204a65747a7420656e676167696572742065722073696368206265692064657220546f75726e656520225472696275746520546f204275646479222e20204865696e7a2d47c383c2bc6e74686572204861727469672c205a776569677374722e20352c20203236313335204f6c64656e627572672054656c2e2030343431202f2039322035302036303020652d6d61696c3a206861727469672d64726f65676540742d6f6e6c696e652e6465),
(26, 'Mein erster Absatz', 1, 'height=400;table=;category=;image=40', 0x3c6469763e3c6469763e0a3c6469763e0a3c703e756e64206e6f6368203c62723e6d6568723f213f213f3c2f703e0a3c703e65696e207a7765692076696572203c62723e46616161616265206d69742053c3b66e6465727a65696368656e3c2f703e0a3c2f6469763e0a3c2f6469763e3c2f6469763e),
(27, '', 0, 'height=300;table=;category=;image=38', 0x3c6469763e3c703e44617320526f636b6e526f6c6c204d7573696b6d6167617a696e2069737420646173204d6167617a696e2066c3bc7220616c6c65206a756e67656e20756e64206a756e676765626c696562656e656e20526f636b6e526f6c6c2d467265756e64652c202d506c617474656e73616d6d6c65722c202d4b656e6e657220756e6420616c6c652c206469652065732077657264656e20776f6c6c656e2e3c2f703e0a3c703e266e6273703b3c2f703e0a3c703e3c6120687265663d22687474703a2f2f726e722e6c6f63616c686f73742f696e6465782f616b7475656c6c6573223e57656c63686520496e68616c74652068617420756e736572204d6167617a696e3f3c2f613e3c2f703e3c2f6469763e),
(28, '', 0, 'height=300;table=;category=;image=42', 0x3c6469763e3c6469763e0a3c703e26616d703b266e6273703b5365697464656d20676562656e204865696e7a2d47c3bc6e746865722048617274696720756e642057616c7472617574204472c3b6676520646173204d6167617a696e206865726175732c2064617320696e20646572205a7769736368656e7a65697420c383c692c382c692c383e2809ac382c2bc626572203830302041626f6e6e656e74656e206861742e20486572617573676562657220756e64204d697461726265697465722073746568656e20766f6c6c20696d206e6f726d616c656e204265727566736c6562656e2c20646965204d7573696b7a656974736368726966742077697264206b6f6d706c65747420696e2064657220467265697a6569742068657267657374656c6c742e3c2f703e0a3c703e266e6273703b3c2f703e0a3c703e44617320526f636bc383c692c382c2a2c383c2afc382c2bfc382c2bdc383c2afc382c2bfc382c2bd6ec383c692c382c2a2c383c2afc382c2bfc382c2bdc383c2afc382c2bfc382c2bd526f6c6c204d7573696b6d6167617a696e2069737420696e7a7769736368656e2065696e65206465722077656c747765697420616d206cc383c692c382c692c383e2809ac382c2a46e677374656e20626573746568656e64656e204d7573696b7a65697473636872696674656e2c206469652073696368207370657a69656c6c206d697420526f636bc383c692c382c2a2c383c2afc382c2bfc382c2bdc383c2afc382c2bfc382c2bd6ec383c692c382c2a2c383c2afc382c2bfc382c2bdc383c2afc382c2bfc382c2bd526f6c6c2d4d7573696b206265736368c383c692c382c692c383e2809ac382c2a466746967656e2e204469652065727374656e20417573676162656e2073696e642073656974204a616872656e207665726772696666656e20756e642064616865722073656c627374207a752067657375636874656e2053616d6d6c65727374c383c692c382c692c383e2809ac382c2bc636b656e206765776f7264656e3c2f703e0a3c2f6469763e3c2f6469763e);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Daten für Tabelle `pictures`
--

INSERT INTO `pictures` (`id`, `url`, `title`, `link`) VALUES
(1, '/images/logo.png', '', NULL),
(2, '/images/hgh_55-neu.gif', 'Heinz-Guenther Hartig', NULL),
(4, 'http://25.media.tumblr.com/tumblr_l8wey3FMf21qdhhtjo1_500.png', 'an unrelated image.', NULL),
(6, '/images/dia1.gif', 'Ein der ersten Cover', NULL),
(7, 'http://26.media.tumblr.com/tumblr_l96jbxTdG01qdhhtjo1_500.jpg', 'Die Geschichte begann...', NULL),
(8, 'http://25.media.tumblr.com/tumblr_l97er7Uz2t1qdhhtjo1_500.jpg', 'crypto smasher', NULL),
(9, 'http://29.media.tumblr.com/tumblr_l97eocla8Q1qdhhtjo1_500.jpg', 'Aufsicht', NULL),
(10, 'http://29.media.tumblr.com/tumblr_l97jexbpx91qdhhtjo1_500.jpg', 'titelomatic', NULL),
(11, 'http://24.media.tumblr.com/tumblr_l9keni96a81qdhhtjo1_500.png', 'titoel', NULL),
(12, '', 'raaaandom', NULL),
(13, 'http://27.media.tumblr.com/tumblr_l9qlf1zwvz1qdhhtjo1_500.jpg', 'kuh bild', NULL),
(14, 'http://28.media.tumblr.com/tumblr_l9qhscblhm1qdhhtjo1_500.jpg', 'wild ', NULL),
(15, 'http://29.media.tumblr.com/tumblr_la7s49bFln1qdhhtjo1_500.jpg', 'gewesen  sein?!', NULL),
(16, 'http://29.media.tumblr.com/tumblr_la7s1jreap1qdhhtjo1_500.jpg', 'Tse tung', NULL),
(17, 'http://29.media.tumblr.com/tumblr_la7tup93go1qdhhtjo1_500.jpg', 'welt', NULL),
(18, 'http://30.media.tumblr.com/tumblr_la7sby0ISf1qdhhtjo1_500.jpg', 'sfghdyhtr', NULL),
(19, 'http://24.media.tumblr.com/tumblr_lavowx0M351qdhhtjo1_500.jpg', 'dummerchen', NULL),
(20, '', '', NULL),
(21, 'http://a7.sphotos.ak.fbcdn.net/hphotos-ak-snc6/217618_10150243832715097_51752540096_9042180_7573569_n.jpg', 'LAGelb', NULL),
(22, 'http://a7.sphotos.ak.fbcdn.net/hphotos-ak-snc6/217618_10150243832715097_51752540096_9042180_7573569_n.jpg', 'LAN', NULL),
(23, '/images/hgh_55-neu.gif', 'Heinz-Guenther Hartig', NULL),
(24, '/images/hgh_55-neu.gif', 'Heinz-Guenther Hartig', NULL),
(25, '', '', NULL),
(26, 'https://d3nwyuy0nl342s.cloudfront.net/images/modules/header/logov5-hover.png', 'gitti', NULL),
(27, '', '', NULL),
(28, 'http://localhost/rocknroll_new/images/bestellen01.gif', '', NULL),
(29, 'http://localhost/rocknroll_new/images/bestellen02.gif', '', NULL),
(32, 'http://localhost/rocknroll_new/images/buch_perspektivisch.gif', 'gj', NULL),
(38, 'http://localhost/rocknroll_new/images/collage.gif', 'collection', NULL),
(39, 'http://localhost/rocknroll_new/images/truecolor.jpg', '', NULL),
(40, 'http://localhost/rocknroll_new/images/dia5.gif', '', NULL),
(41, 'http://localhost/rocknroll_new/images/dia1.gif', '', NULL),
(42, 'http://localhost/rocknroll_new/images/cover_blue-with_circle.gif', '', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `submenus`
--

INSERT INTO `submenus` (`id`, `pic`, `entries`, `links`) VALUES
(1, 'images/images/Elements_06.png', '', ''),
(4, NULL, '|and yet another|the first working one?|nah there was another', '|September|Octubre, und +mehr|September'),
(6, NULL, '', ''),
(9, NULL, '', ''),
(10, NULL, '', ''),
(11, NULL, '', ''),
(12, NULL, '', ''),
(13, NULL, '', ''),
(14, NULL, '', ''),
(15, NULL, NULL, NULL);
