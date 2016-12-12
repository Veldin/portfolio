-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Gegenereerd op: 12 dec 2016 om 21:12
-- Serverversie: 10.1.16-MariaDB
-- PHP-versie: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portfolio`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `chat`
--

CREATE TABLE `chat` (
  `userid` int(10) NOT NULL,
  `timestamp` int(10) NOT NULL,
  `targetid` int(10) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `level`
--

CREATE TABLE `level` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(30) NOT NULL,
  `blocked` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `module`
--

CREATE TABLE `module` (
  `id` int(10) NOT NULL,
  `portfolioid` int(10) NOT NULL,
  `moduleid` int(10) NOT NULL,
  `position` int(3) NOT NULL,
  `size` int(4) NOT NULL,
  `input` text NOT NULL,
  `timestamp` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `moduletemplate`
--

CREATE TABLE `moduletemplate` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `function` varchar(30) NOT NULL,
  `field` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `portfolio`
--

CREATE TABLE `portfolio` (
  `userid` int(10) NOT NULL,
  `url` varchar(255) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `colour` int(6) NOT NULL,
  `secondarycolour` int(6) NOT NULL,
  `tertiarycolour` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `levelid` int(1) NOT NULL,
  `slb` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `phone` int(10) NOT NULL,
  `zipcode` varchar(6) NOT NULL,
  `address` varchar(3) NOT NULL,
  `timestamp` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`userid`,`timestamp`,`targetid`);

--
-- Indexen voor tabel `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `portfolioid` (`portfolioid`);

--
-- Indexen voor tabel `moduletemplate`
--
ALTER TABLE `moduletemplate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexen voor tabel `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`userid`),
  ADD KEY `url` (`url`);

--
-- Indexen voor tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `levelid` (`levelid`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `module`
--
ALTER TABLE `module`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT voor een tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
