-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 09, 2024 at 12:34 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u800275806_easedocument`
--

-- --------------------------------------------------------

--
-- Table structure for table `centralize_request`
--

CREATE TABLE `centralize_request` (
  `cr_id` int(11) NOT NULL,
  `cr_code` varchar(60) NOT NULL,
  `cr_1X1_pic` varchar(255) DEFAULT NULL,
  `cr_Signature` varchar(255) DEFAULT NULL,
  `cr_purpose` text NOT NULL,
  `cr_price` decimal(10,2) NOT NULL,
  `cr_shipping_fee` decimal(10,3) NOT NULL,
  `cr_total` decimal(10,3) NOT NULL,
  `cr_address` varchar(255) NOT NULL,
  `cr_payment` varchar(60) NOT NULL,
  `cr_validId` varchar(255) NOT NULL,
  `cr_proofResidency` varchar(255) DEFAULT NULL,
  `cr_r_id` int(11) NOT NULL,
  `cr_formtype` varchar(60) NOT NULL,
  `cr_request_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cr_status` varchar(60) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `centralize_request`
--

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `r_id` int(11) NOT NULL,
  `r_fname` varchar(60) NOT NULL,
  `r_mname` varchar(60) DEFAULT NULL,
  `r_lname` varchar(60) NOT NULL,
  `r_profile` varchar(255) NOT NULL,
  `r_valid_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `r_suffix` varchar(20) DEFAULT NULL,
  `r_gender` varchar(20) NOT NULL,
  `r_civil_status` varchar(20) NOT NULL,
  `r_citizenship` varchar(20) NOT NULL,
  `r_bday` date NOT NULL,
  `r_street` varchar(60) NOT NULL,
  `r_region` varchar(60) NOT NULL,
  `r_province` varchar(60) NOT NULL,
  `r_municipality` varchar(60) NOT NULL,
  `r_barangay` varchar(60) NOT NULL,
  `r_contact_number` varchar(60) NOT NULL,
  `r_email` varchar(60) NOT NULL,
  `r_password` varchar(255) NOT NULL,
  `r_status` int(10) NOT NULL DEFAULT 1 COMMENT '0=archive,1=Verified,2=NotVerified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_fname` varchar(60) NOT NULL,
  `user_mname` varchar(60) DEFAULT NULL,
  `user_lname` varchar(60) NOT NULL,
  `user_email` varchar(60) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` varchar(60) NOT NULL,
  `user_status` varchar(60) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

--
-- Indexes for dumped tables
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT 'General',
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Published' COMMENT 'Draft, Published, Archived',
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=No, 1=Pinned',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `fk_announcements_user` (`user_id`);

--
-- Indexes for table `centralize_request`
--
ALTER TABLE `centralize_request`
  ADD PRIMARY KEY (`cr_id`),
  ADD KEY `rcl_r_id` (`cr_r_id`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`r_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `centralize_request`
--
ALTER TABLE `centralize_request`
  MODIFY `cr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `resident`
--
ALTER TABLE `resident`
  MODIFY `r_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `centralize_request`
--
ALTER TABLE `centralize_request`
  ADD CONSTRAINT `centralize_request_ibfk_1` FOREIGN KEY (`cr_r_id`) REFERENCES `resident` (`r_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
