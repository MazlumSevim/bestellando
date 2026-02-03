-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 03. Feb 2026 um 14:38
-- Server-Version: 10.4.28-MariaDB
-- PHP-Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `bestellando`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bestellung`
--

CREATE TABLE `bestellung` (
  `bid` int(11) NOT NULL,
  `tischid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `speisekarteid` int(11) NOT NULL,
  `storniert` tinyint(1) NOT NULL DEFAULT 0,
  `menge` int(11) NOT NULL DEFAULT 1,
  `gesamtpreis` decimal(10,2) NOT NULL,
  `fertig` tinyint(1) NOT NULL DEFAULT 0,
  `fertig_am` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `bestellung`
--

INSERT INTO `bestellung` (`bid`, `tischid`, `user_id`, `speisekarteid`, `storniert`, `menge`, `gesamtpreis`, `fertig`, `fertig_am`) VALUES
(11, 1, 1, 1, 0, 1, 10.90, 0, NULL),
(12, 1, 1, 3, 0, 1, 6.50, 0, NULL),
(13, 1, 1, 6, 0, 1, 3.00, 0, NULL),
(14, 1, 1, 7, 0, 1, 3.00, 0, NULL),
(15, 4, 1, 6, 0, 1, 3.00, 0, NULL),
(16, 4, 1, 7, 0, 1, 3.00, 0, NULL),
(17, 4, 1, 2, 0, 1, 12.90, 0, NULL),
(18, 4, 1, 3, 0, 1, 6.50, 0, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `speisekarte`
--

CREATE TABLE `speisekarte` (
  `speiseid` int(11) NOT NULL,
  `speisename` varchar(100) NOT NULL,
  `preis` decimal(10,2) NOT NULL,
  `getraenk` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `speisekarte`
--

INSERT INTO `speisekarte` (`speiseid`, `speisename`, `preis`, `getraenk`) VALUES
(1, 'Burger', 10.90, 0),
(2, 'Schnitzel', 12.90, 0),
(3, 'Salat', 6.50, 0),
(4, 'Pommes', 4.50, 0),
(5, 'Wasser', 2.50, 1),
(6, 'Cola', 3.00, 1),
(7, 'Fanta', 3.00, 1),
(8, 'Kaffee', 2.80, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tisch`
--

CREATE TABLE `tisch` (
  `tischid` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `tisch`
--

INSERT INTO `tisch` (`tischid`, `name`) VALUES
(1, 'Tisch 1'),
(2, 'Tisch 2'),
(3, 'Tisch 3'),
(4, 'Tisch 4'),
(5, 'Tisch 5');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `rolle` varchar(20) NOT NULL,
  `erstellt_am` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`uid`, `username`, `passwort`, `rolle`, `erstellt_am`) VALUES
(1, 'ali', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'kellner', '2026-01-23 09:20:59'),
(2, 'sophie', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'kellner', '2026-01-23 09:20:59'),
(3, 'mehmet', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'kellner', '2026-01-23 09:20:59'),
(4, 'koch', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'kueche', '2026-01-23 09:20:59');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `bestellung`
--
ALTER TABLE `bestellung`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `idx_bestellung_tisch` (`tischid`),
  ADD KEY `idx_bestellung_user` (`user_id`),
  ADD KEY `idx_bestellung_speise` (`speisekarteid`);

--
-- Indizes für die Tabelle `speisekarte`
--
ALTER TABLE `speisekarte`
  ADD PRIMARY KEY (`speiseid`);

--
-- Indizes für die Tabelle `tisch`
--
ALTER TABLE `tisch`
  ADD PRIMARY KEY (`tischid`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uq_users_username` (`username`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `bestellung`
--
ALTER TABLE `bestellung`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT für Tabelle `speisekarte`
--
ALTER TABLE `speisekarte`
  MODIFY `speiseid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT für Tabelle `tisch`
--
ALTER TABLE `tisch`
  MODIFY `tischid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bestellung`
--
ALTER TABLE `bestellung`
  ADD CONSTRAINT `fk_bestellung_speise` FOREIGN KEY (`speisekarteid`) REFERENCES `speisekarte` (`speiseid`),
  ADD CONSTRAINT `fk_bestellung_tisch` FOREIGN KEY (`tischid`) REFERENCES `tisch` (`tischid`),
  ADD CONSTRAINT `fk_bestellung_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
