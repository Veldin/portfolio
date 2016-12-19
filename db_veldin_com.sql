-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u6
-- http://www.phpmyadmin.net
--
-- Machine: db.veldin.com
-- Genereertijd: 19 dec 2016 om 12:02
-- Serverversie: 5.5.38
-- PHP-Versie: 5.4.45-0+deb7u5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databank: `md253219db370063`
--
CREATE DATABASE `md253219db370063` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `md253219db370063`;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `userid` int(10) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `targetid` int(10) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`userid`,`timestamp`,`targetid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `level`
--

CREATE TABLE IF NOT EXISTS `level` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(30) NOT NULL,
  `blocked` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `level`
--

INSERT INTO `level` (`id`, `name`, `description`, `blocked`) VALUES
(1, 'student', 'Een student account.', ''),
(2, 'docent', 'Een docent account.', ''),
(3, 'admin', 'Een beheerder account.', '');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `module`
--

CREATE TABLE IF NOT EXISTS `module` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `portfolioid` int(10) NOT NULL,
  `moduleid` int(10) NOT NULL,
  `position` int(3) NOT NULL,
  `size` int(4) NOT NULL,
  `input` text NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `portfolioid` (`portfolioid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Gegevens worden uitgevoerd voor tabel `module`
--

INSERT INTO `module` (`id`, `portfolioid`, `moduleid`, `position`, `size`, `input`, `timestamp`) VALUES
(1, 1, 1, 1, 100, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s. when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries. but also the leap into electronic typesetting. remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages. and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1481710851),
(2, 1, 2, 2, 50, 'http://i.imgur.com/jK3olvn.gif,ImageTitleFromDb', 1481710961),
(3, 1, 1, 3, 50, 'There are many variations of passages of Lorem Ipsum available. but the majority have suffered alteration in some form. by injected humour. or randomised words which don''t look even slightly believable. If you are going to use a passage of Lorem Ipsum. you need to be sure there isn''t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary. making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words. combined with a handful of model sentence structures. to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition. injected humour. or non-characteristic words etc.\r\n\r\nIt is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters. as opposed to using ''Content here. content here''. making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text. and a search for ''lorem ipsum'' will uncover many web sites still in their infancy. Various versions have evolved over the years. sometimes by accident. sometimes on purpose (injected humour and the like).', 1481714860),
(4, 1, 3, 0, 100, 'Dit is de header text,1', 1481716308),
(5, 1, 1, 4, 100, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s. when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries. but also the leap into electronic typesetting. remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages. and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 1481732349),
(6, 1, 2, 5, 33, 'http://i.imgur.com/mKAtbJ3.jpg,lel', 1481732349),
(7, 1, 2, 6, 33, 'http://i.imgur.com/1olmVpO.jpg,kat', 1481732349),
(8, 1, 2, 7, 33, 'http://i.imgur.com/PNrtwD4.gif,welp', 1481732349);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `moduletemplate`
--

CREATE TABLE IF NOT EXISTS `moduletemplate` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `function` varchar(30) NOT NULL,
  `field` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `moduletemplate`
--

INSERT INTO `moduletemplate` (`id`, `name`, `description`, `function`, `field`) VALUES
(1, 'paragraph', 'Een paragraaf toevoegen.', 'paragraph', 'textarea'),
(2, 'imageFromLink', 'Een afbeelding toevoegen vanaf een link.', 'imageFromLink', 'text,text'),
(3, 'header', 'Voeg een header toe.', 'header', 'text,number');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `portfolio`
--

CREATE TABLE IF NOT EXISTS `portfolio` (
  `userid` int(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `colour` varchar(6) NOT NULL,
  `secondarycolour` varchar(6) NOT NULL,
  `tertiarycolour` varchar(6) NOT NULL,
  PRIMARY KEY (`userid`),
  KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Gegevens worden uitgevoerd voor tabel `portfolio`
--

INSERT INTO `portfolio` (`userid`, `url`, `approved`, `colour`, `secondarycolour`, `tertiarycolour`) VALUES
(1, 'veldin', 0, 'ffffff', 'ffffff', 'ffffff');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `levelid` int(1) NOT NULL,
  `slb` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `phone` int(10) NOT NULL,
  `zipcode` varchar(6) NOT NULL,
  `address` varchar(3) NOT NULL,
  `timestamp` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `levelid` (`levelid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Gegevens worden uitgevoerd voor tabel `user`
--

INSERT INTO `user` (`id`, `levelid`, `slb`, `email`, `password`, `firstname`, `lastname`, `phone`, `zipcode`, `address`, `timestamp`) VALUES
(1, 1, 2, 'Leerling@Student.com', 'lol', 'Test', 'Student', 0, '', '', 0),
(2, 2, 2, 'Leeraar@leraar.com', 'lel', 'Leeraar', 'McLeeraar', 0, '', '', 0),
(3, 1, 0, 'amr.jonkman@gmail.com', '$2y$10$xyzCGUOb3f2fOi64SUWfgecooI0uAYmRhrndakBGGt5Bf4yAkYBuq', '', '', 0, '', '', 2016);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
