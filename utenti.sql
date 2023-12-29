-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 24, 2023 alle 18:53
-- Versione del server: 10.4.25-MariaDB
-- Versione PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `utenti`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `auth`
--

CREATE TABLE `auth` (
  `id` int(5) NOT NULL,
  `auth` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `auth`
--

INSERT INTO `auth` (`id`, `auth`) VALUES
(1, 'ldap'),
(2, 'manual');

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppi`
--

CREATE TABLE `gruppi` (
  `id` int(11) NOT NULL,
  `gruppi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `gruppi`
--

INSERT INTO `gruppi` (`id`, `gruppi`) VALUES
(1, 'Amministratori'),
(2, 'Studenti'),
(3, 'Coordinatori'),
(4, 'Docenti');

-- --------------------------------------------------------

--
-- Struttura della tabella `useraccountcontrol`
--

CREATE TABLE `useraccountcontrol` (
  `id` int(11) NOT NULL,
  `codice` int(30) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `useraccountcontrol`
--

INSERT INTO `useraccountcontrol` (`id`, `codice`, `nome`) VALUES
(1, 65536, 'DONT_EXPIRE_PASSWORD'),
(2, 512, 'NORMAL_ACCOUNT');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `idnumber` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `group` varchar(50) DEFAULT NULL,
  `auth` varchar(50) NOT NULL,
  `gruppi` varchar(50) NOT NULL,
  `userAccountControl` int(20) DEFAULT NULL,
  `memberOf` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `nome`, `cognome`, `email`, `idnumber`, `password`, `course`, `group`, `auth`, `gruppi`, `userAccountControl`, `memberOf`) VALUES
(1, 'Luca', 'TEST', 'lucaDASCANIO@mail.com', 'A8013297', NULL, '', '', 'ldap', 'Amministratori', 0, ''),
(2, 'Massimo', 'CESARETTI', 'MASSIMO', '', NULL, '', '', 'ldap', 'Amministratori', 0, ''),
(3, 'Renzo', 'TANCREDI', 'tancredi', '', NULL, '', '', 'ldap', 'Amministratori', 0, '');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `auth`
--
ALTER TABLE `auth`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `gruppi`
--
ALTER TABLE `gruppi`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `useraccountcontrol`
--
ALTER TABLE `useraccountcontrol`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `auth`
--
ALTER TABLE `auth`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `gruppi`
--
ALTER TABLE `gruppi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT per la tabella `useraccountcontrol`
--
ALTER TABLE `useraccountcontrol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
