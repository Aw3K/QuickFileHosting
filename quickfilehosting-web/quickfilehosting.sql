-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Wersja serwera: 8.4.0
-- Wersja PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quickfilehosting`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `hash` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fname` text NOT NULL,
  `size` int NOT NULL,
  `created` text NOT NULL,
  `expired` text NOT NULL,
  `userip` text NOT NULL,
  `dcount` int NOT NULL DEFAULT '0',
  `remove` varchar(5) NOT NULL,
  `owner` int DEFAULT NULL,
  `pass` text NOT NULL,
  `locked` varchar(5) NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(33) CHARACTER SET latin2 COLLATE latin2_general_ci NOT NULL,
  `password` text CHARACTER SET latin2 COLLATE latin2_general_ci NOT NULL,
  `email` varchar(64) CHARACTER SET latin2 COLLATE latin2_general_ci NOT NULL,
  `hash` varchar(128) CHARACTER SET latin2 COLLATE latin2_general_ci DEFAULT NULL,
  `hash_valid_time` int DEFAULT NULL,
  `createdate` bigint NOT NULL,
  `active` tinyint(1) DEFAULT '0',
  `passres` varchar(128) CHARACTER SET latin2 COLLATE latin2_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

--
-- Wyzwalacze `users`
--
DELIMITER $$
CREATE TRIGGER `hash_valid_time` BEFORE UPDATE ON `users` FOR EACH ROW IF NEW.hash != OLD.hash THEN
	SET NEW.hash_valid_time = UNIX_TIMESTAMP()+86400;
END IF
$$
DELIMITER ;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`,`hash`),
  ADD KEY `owner` (`owner`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`,`email`,`hash`,`passres`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`owner`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
