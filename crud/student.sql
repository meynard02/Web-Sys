-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 05:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `Lname` varchar(100) NOT NULL,
  `Gname` varchar(100) NOT NULL,
  `MI` varchar(2) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `Lname`, `Gname`, `MI`, `Username`, `Password`, `Created_at`, `is_deleted`) VALUES
(15, '111', '11', '11', '1111', '$2y$10$bX4ICSXJERUYnIPqY6KF3.bDTkc1vauVRsgqEpGMRpbp1apnguq7O', '2025-08-27 12:35:56', 0),
(16, 'ocenar', 'meynard', 'a', 'meynard', '$2y$10$d8CJeInSQZFNdDqNsMUJT.NgLk739fUjKqq176golFp5OTpkctGlu', '2025-09-03 10:41:37', 0),
(17, 'laxa', 'asher', 'l', 'asher', '$2y$10$Mv4cpFfe8d5jejLeMS/NSupl/MvRuDIx8nBZQgBKtphEPNsll5L6u', '2025-09-03 10:45:10', 0),
(18, 'enales', 'flairy', 'g', 'flairy', '$2y$10$SPbJ/H/GbiXcj9txicomKeohtl6XMoEC5VHUz25hgSVyB3jl7Vu.S', '2025-09-03 11:41:35', 0),
(19, 'Enales', 'Flairy ', 'G', 'flairy_', '$2y$10$NBN1JwbdG/kDWQp7sNDyeev/BdOzJdwQNs/w9TJu29teX2RItYfhi', '2025-09-04 05:50:33', 0),
(20, 'enales', 'flairy ann', 'g', 'flairyann_', '$2y$10$NDxdN728Zy7sTTd8.1PgQeK26JFqNkfe9L94x6RBuiix82mVVjyWm', '2025-09-05 01:13:53', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
