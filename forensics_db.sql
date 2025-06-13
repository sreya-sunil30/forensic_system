-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 08:02 AM
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
-- Database: `forensics_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `calendar_notes`
--

CREATE TABLE `calendar_notes` (
  `id` int(11) NOT NULL,
  `investigator_id` int(11) NOT NULL,
  `custom_case_id` varchar(11) NOT NULL,
  `note` text NOT NULL,
  `confidential` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `calendar_notes`
--

INSERT INTO `calendar_notes` (`id`, `investigator_id`, `custom_case_id`, `note`, `confidential`, `created_at`, `updated_at`) VALUES
(1, 3, 'CASE-FU63KY', 'dtdrttrtttttttttttyyewrwewww', 1, '2025-06-09 15:01:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('open','in progress','closed') DEFAULT 'open',
  `assigned_to` int(11) DEFAULT NULL,
  `custom_case_id` varchar(20) NOT NULL,
  `deadline` date DEFAULT NULL,
  `investigator_response` enum('pending','accepted','reassign_requested') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cases`
--

INSERT INTO `cases` (`title`, `description`, `created_by`, `created_at`, `status`, `assigned_to`, `custom_case_id`, `deadline`, `investigator_response`) VALUES
('cyber theft', 'sdkfjiouowuriowehrjkhejkrbhejkrhwejkrq', 2, '2025-06-07 11:22:37', 'open', 3, 'CASE-FU63KY', NULL, 'accepted'),
('forgery', 'gsfshfjjljl', NULL, '2025-06-03 12:04:55', 'closed', 3, 'CASE-RT5XNF', NULL, 'reassign_requested'),
('cyber data theft', 'rtpieprtu0eiutjituorutihtjhgwu', NULL, '2025-06-07 12:36:09', 'open', NULL, 'CASE-SXUJ8R', NULL, 'pending'),
('drug case', 'oifjiapwjtwr9pitp9r', NULL, '2025-06-02 14:59:56', 'in progress', 3, 'CASE-XPBRHL', '2025-07-07', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Submitted',
  `response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_converted` tinyint(1) DEFAULT 0,
  `custom_case_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `subject`, `message`, `attachment`, `status`, `response`, `created_at`, `is_converted`, `custom_case_id`) VALUES
(12, 4, 'forgery', 'jkopipoik;lkl/k', NULL, 'Rejected', NULL, '2025-06-02 14:56:08', 1, NULL),
(14, 2, 'murder 2', 'jojs;js;gpjspiptie0r9tiptois\'fk', NULL, 'Rejected', NULL, '2025-06-03 12:21:14', 1, NULL),
(15, 2, 'robbery', 'toitioiowt09wtojklkd;jkdjkljafjhajfhjkhgl.ss', '../uploads/complaints/1749021308_fileupload.jpg', 'Pending', NULL, '2025-06-04 12:45:08', 0, NULL),
(17, 2, 'chase\'s murder', 'teiotuiutituhujtjhiuiwu4io3wu4io3u4o3i4hhgerhgetrfykerewuw', '../uploads/complaints/1749272515_connie.jpg', 'Rejected', NULL, '2025-06-07 10:31:55', 1, NULL),
(18, 2, 'cyber theft', 'sdkfjiouowuriowehrjkhejkrbhejkrhwejkrq', '../uploads/complaints/1749274837_dorae.jpg', 'Pending', NULL, '2025-06-07 11:10:37', 1, 'CASE-FU63KY');

-- --------------------------------------------------------

--
-- Table structure for table `evidence`
--

CREATE TABLE `evidence` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `sha256_hash` varchar(64) DEFAULT NULL,
  `custom_case_id` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evidence`
--

INSERT INTO `evidence` (`id`, `file_name`, `file_path`, `uploaded_by`, `uploaded_at`, `sha256_hash`, `custom_case_id`, `description`) VALUES
(3, '1748858304_dorae.jpg', '../uploads/evidence/1748858304_dorae.jpg', 1, '2025-06-02 15:28:24', 'a9eed34fd3254e03bdd6a9adb39dc1276f43f2a90bfa902b249e9426e6cc6fc5', 'CASE-XPBRHL', NULL),
(6, '1748858940_dorae.jpg', '../uploads/evidence/1748858940_dorae.jpg', 1, '2025-06-02 15:39:00', 'a9eed34fd3254e03bdd6a9adb39dc1276f43f2a90bfa902b249e9426e6cc6fc5', 'CASE-XPBRHL', NULL),
(8, '1748943830_dorae.jpg', '../uploads/evidence/1748943830_dorae.jpg', 1, '2025-06-03 15:13:50', 'a9eed34fd3254e03bdd6a9adb39dc1276f43f2a90bfa902b249e9426e6cc6fc5', 'CASE-RT5XNF', NULL),
(22, '1748950571_connie.jpg', '../uploads/evidence/1748950571_connie.jpg', 1, '2025-06-03 17:06:11', '1936476967e3c1bce27df7ab234ee2356d718d7722b5a26a2f8199f359452238', 'CASE-XPBRHL', NULL),
(27, '1749017350_zeb.jpg', 'uploads/evidence/1749017350_zeb.jpg', 4, '2025-06-04 11:39:10', 'bb2438749ed9deaf7bc84749562d69867869b0cbe4fed6ce6f4f85ac1c177677', 'CASE-RT5XNF', ''),
(30, '1749635785_report_6843d8614ae564.20572115.jpg', '../uploads/evidence/1749635785_report_6843d8614ae564.20572115.jpg', 1, '2025-06-11 15:26:25', 'bb2438749ed9deaf7bc84749562d69867869b0cbe4fed6ce6f4f85ac1c177677', 'CASE-SXUJ8R', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `investigator_daily_reports`
--

CREATE TABLE `investigator_daily_reports` (
  `id` int(11) NOT NULL,
  `investigator_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `custom_case_id` varchar(20) NOT NULL,
  `activity_summary` text NOT NULL,
  `num_suspects` int(11) DEFAULT 0,
  `suspects` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`suspects`)),
  `evidence_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`evidence_files`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investigator_daily_reports`
--

INSERT INTO `investigator_daily_reports` (`id`, `investigator_id`, `report_date`, `custom_case_id`, `activity_summary`, `num_suspects`, `suspects`, `evidence_files`, `created_at`) VALUES
(1, 3, '2025-06-12', 'CASE-FU63KY', 'jfughrt98rtether ierierrihtr', 0, '[{\"name\":\"subanu\",\"age\":\"30\",\"remarks\":\"rtrtrtrtr\"}]', '[\"uploads\\/684a753f6d336_fileupload.jpg\"]', '2025-06-12 06:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `custom_case_id` varchar(20) NOT NULL,
  `investigator_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `report_date` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `conclusion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `custom_case_id`, `investigator_id`, `description`, `report_date`, `file_name`, `file_path`, `conclusion`) VALUES
(1, 'CASE-RT5XNF', 3, 'kl;ksooptipoti', '2025-06-03', NULL, NULL, 'dsjkkewheuwiheuwgw'),
(2, 'CASE-FU63KY', 3, 'ioutoiurtoiuroituoeriutoretjikrtfnmdnfkdjrhe   tiortuuierthklrtjnklretiortoiwjetpowejtklwengtjuhretuwoekwlkel;ekr hjeuiryhegiruygrtftgrfwejkreiwruyiytuerhtrjknjklgdfghkladjks', '2025-06-07', 'zeb.jpg', 'uploads/reports/report_6843d8614ae564.20572115.jpg', 'jkfkdhfkdhfiud');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','investigator','user') DEFAULT 'investigator',
  `status` varchar(10) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$YdoskKweziKskOP/jeqa5.zRM8ZcFPG3lHV4Tu43hVIPngjRR71ia', 'admin', 'active'),
(2, 'Sreya', 'sreya@gmail.com', '$2y$10$OHCFnqGdHdNjUyzaHHYOpenMVxLmiTeaPJCmojEWZd/dNbjljVR2O', 'user', 'active'),
(3, 'Ares', 'areswindsor@gmail.com', '$2y$10$cAMKHDYb9F00SEOQcuwSlONgvWej9EEDOn41JRh5oSdEwN6.DAIqG', 'investigator', 'active'),
(4, 'thamarai', 'thamarai@gmail.com', '$2y$10$twZi56DpxSU1RpxzMzlo8e3LUr/uJ4lirB7E.GINW9grpcjKLvZbu', 'user', 'active'),
(5, 'jude', 'jude@gmail.com', '$2y$10$NVSfwNh5bITBL31DpDc.AuW.N8GCbhQxsR5eHxkSHnP/COYMRn7de', 'investigator', 'active'),
(6, 'nisha', 'nisha12@gmail.com', '$2y$10$m64Vm.4WHwn/xifja/CUEOsQtAuYrfkZFFukwjeh0uOc9CnhGarrm', 'user', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendar_notes`
--
ALTER TABLE `calendar_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investigator_id` (`investigator_id`),
  ADD KEY `custom_case_id` (`custom_case_id`);

--
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`custom_case_id`),
  ADD UNIQUE KEY `custom_case_id` (`custom_case_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_case_id` (`custom_case_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `evidence`
--
ALTER TABLE `evidence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `fk_evidence_case` (`custom_case_id`);

--
-- Indexes for table `investigator_daily_reports`
--
ALTER TABLE `investigator_daily_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `investigator_id` (`investigator_id`),
  ADD KEY `custom_case_id` (`custom_case_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_case_id` (`custom_case_id`),
  ADD KEY `investigator_id` (`investigator_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendar_notes`
--
ALTER TABLE `calendar_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `evidence`
--
ALTER TABLE `evidence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `investigator_daily_reports`
--
ALTER TABLE `investigator_daily_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calendar_notes`
--
ALTER TABLE `calendar_notes`
  ADD CONSTRAINT `calendar_notes_ibfk_1` FOREIGN KEY (`investigator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calendar_notes_ibfk_2` FOREIGN KEY (`custom_case_id`) REFERENCES `cases` (`custom_case_id`) ON DELETE CASCADE;

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `cases_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cases_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_complaints_case` FOREIGN KEY (`custom_case_id`) REFERENCES `cases` (`custom_case_id`) ON DELETE CASCADE;

--
-- Constraints for table `evidence`
--
ALTER TABLE `evidence`
  ADD CONSTRAINT `evidence_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_evidence_case` FOREIGN KEY (`custom_case_id`) REFERENCES `cases` (`custom_case_id`) ON DELETE CASCADE;

--
-- Constraints for table `investigator_daily_reports`
--
ALTER TABLE `investigator_daily_reports`
  ADD CONSTRAINT `investigator_daily_reports_ibfk_1` FOREIGN KEY (`investigator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `investigator_daily_reports_ibfk_2` FOREIGN KEY (`custom_case_id`) REFERENCES `cases` (`custom_case_id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`custom_case_id`) REFERENCES `cases` (`custom_case_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`investigator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
