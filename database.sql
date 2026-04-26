-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost: 
-- Generation Time: Jun 06, 2025 at 01:11 PM
-- Server version: 10.11.11-MariaDB-cll-lve
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myblog`
--

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `visited_at` datetime DEFAULT NULL,
  `referrer` text DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `screen_res` varchar(20) DEFAULT NULL,
  `page_url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_bot` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `blog_ID` int(14) NOT NULL,
  `blog_title` varchar(100) NOT NULL,
  `blog_slug` varchar(40) NOT NULL,
  `blog_image` varchar(60) DEFAULT NULL,
  `blog_category_fk` int(11) DEFAULT NULL,
  `blog_content` text DEFAULT NULL,
  `blog_views` int(11) DEFAULT 0,
  `blog_date` varchar(100) DEFAULT NULL,
  `blog_is_published` enum('Yes','Pending') DEFAULT 'Yes',
  `blog_user_fk` int(11) NOT NULL,
  `blog_created_at` datetime DEFAULT current_timestamp(),
  `blog_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`blog_ID`, `blog_title`, `blog_slug`, `blog_image`, `blog_category_fk`, `blog_content`, `blog_views`, `blog_date`, `blog_is_published`, `blog_user_fk`, `blog_created_at`, `blog_updated_at`) VALUES
(3, 'Samp Tits for b', 'ICfKs', '1749053162880673986jpeg', 4, NULL, 0, '1749053162', 'Yes', 6, '2025-06-04 19:06:02', '2025-06-04 19:06:02'),
(5, 'Second thing', 'rZd4S', '1749053353318414821jpeg', 4, NULL, 0, '1749053353', 'Yes', 6, '2025-06-04 19:09:13', '2025-06-04 19:09:13'),
(6, 'CKEDITOR.instances.ckeditor.getData()', 'MxAYb', '17490539931582924195jpeg', 4, '<p>dsdsd sdsdsds&nbsp;&nbsp;ensuite facilities in Nairobis</p>\r\n', 0, '1749053993', 'Yes', 6, '2025-06-04 19:19:53', '2025-06-04 19:19:53');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `bc_ID` int(11) NOT NULL,
  `bc_name` varchar(100) NOT NULL,
  `bc_url` varchar(40) NOT NULL,
  `bc_desc` text DEFAULT NULL,
  `bc_views` int(11) DEFAULT 0,
  `bc_image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`bc_ID`, `bc_name`, `bc_url`, `bc_desc`, `bc_views`, `bc_image`) VALUES
(4, 'Fun Tv', 'K62RX', 'Just some random fun moments', 0, '1749051692306139038.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `comment_created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `c_ID` int(11) NOT NULL,
  `c_name` varchar(40) DEFAULT NULL,
  `c_short_desc` varchar(300) DEFAULT NULL,
  `c_tel` varchar(20) DEFAULT NULL,
  `c_email` varchar(100) DEFAULT NULL,
  `c_address` varchar(100) DEFAULT NULL,
  `c_icon` varchar(100) DEFAULT NULL,
  `c_logo` varchar(100) DEFAULT NULL,
  `enable_maintenance_mode` INT(4) DEFAULT 0,
  `c_facebook` varchar(100) DEFAULT NULL,
  `c_youtube` varchar(100) DEFAULT NULL,
  `c_instagram` varchar(100) DEFAULT NULL,
  `c_twitter` varchar(100) DEFAULT NULL,
  `c_linkedin` varchar(100) DEFAULT NULL,
  `c_tiktok` varchar(100) DEFAULT NULL,
  `c_send_from` varchar(70) DEFAULT NULL,
  `c_send_from_password` varchar(70) DEFAULT NULL,
  `c_primary_color` varchar(20) DEFAULT '#d1cf1f',
  `c_verify_mail` enum('True','False') NOT NULL DEFAULT 'False',
  `c_verify_phone` enum('True','False') DEFAULT 'False',
  `c_strong_password` enum('True','False') DEFAULT 'False',
  `c_smtp_server` varchar(40) DEFAULT 'mail.examle.com',
  `c_user_can_signup` enum('True','False') DEFAULT 'True',
  `c_user_can_blog` enum('True','False') DEFAULT 'True',
  `c_user_can_login` enum('True','False') DEFAULT 'True',
  `user_loop_sequence` int(11) DEFAULT 12,
  `c_sessions_last` enum('True','False') DEFAULT 'True'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`c_ID`, `c_name`, `c_short_desc`, `c_tel`, `c_email`, `c_address`, `c_icon`, `c_logo`, `c_facebook`, `c_youtube`, `c_instagram`, `c_twitter`, `c_linkedin`, `c_tiktok`, `c_send_from`, `c_send_from_password`, `c_primary_color`, `c_verify_mail`, `c_verify_phone`, `c_strong_password`, `c_smtp_server`, `c_user_can_signup`, `c_user_can_blog`, `c_user_can_login`, `user_loop_sequence`, `c_sessions_last`) VALUES
(1, 'My New Company ', 'A good description', '070000000', 'info@company.com', 'New State 4th Street', '1749158590317539845.jpeg', '17491586211731829117.jpeg', 'https://facebook.com/#', 'https://youtube.com/#', 'https://instragram.com/#', 'https://twitter.com/#', 'https://linkedin.com/#', 'https://tiktok.com/#', 'info@homekazi.com', 'acfr5ex60@', '#d1cf1f', 'False', 'False', 'False', 'mail.brodjet.com', 'True', 'True', 'True', 12, 'True');

-- --------------------------------------------------------

--
-- Table structure for table `contactus`
--

CREATE TABLE `contactus` (
  `id` int(11) NOT NULL,
  `email` varchar(70) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `date` varchar(100) DEFAULT NULL,
  `status` enum('current','completed') DEFAULT 'current',
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contactus`
--

INSERT INTO `contactus` (`id`, `email`, `phone`, `subject`, `message`, `date`, `status`, `name`) VALUES
(25, 'cc@gmail.com', '070000000', 'Happy New Year', 'xxxxxxxxxxxxxxxxxxxxxxxxx', '2025-06-04', 'current', 'cc');

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `cont_ID` int(11) NOT NULL,
  `cont_body` text DEFAULT NULL,
  `cont_given_id` varchar(100) DEFAULT NULL,
  `cont_title` varchar(300) DEFAULT NULL,
  `cont_img` varchar(100) DEFAULT NULL,
  `cont_date` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`cont_ID`, `cont_body`, `cont_given_id`, `cont_title`, `cont_img`, `cont_date`) VALUES
(22, '<p>some random body aboutusdccdcdcdcdc</p>\r\n', 'aboutus', 'About Usxx', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `recipient` varchar(255) DEFAULT NULL,
  `status` enum('success','fail') DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `l_id` int(11) NOT NULL,
  `l_user_id` int(11) NOT NULL,
  `l_post_id` int(11) NOT NULL,
  `l_created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `l_ID` int(20) NOT NULL,
  `l_message` text DEFAULT NULL,
  `l_by` int(11) DEFAULT NULL,
  `l_type` varchar(50) DEFAULT NULL,
  `l_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

CREATE TABLE `password_reset` (
  `reset_ID` int(11) NOT NULL,
  `reset_email` varchar(50) DEFAULT NULL,
  `reset_selector` text DEFAULT NULL,
  `reset_token` longtext DEFAULT NULL,
  `reset_expiry` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`reset_ID`, `reset_email`, `reset_selector`, `reset_token`, `reset_expiry`) VALUES
(5, 'calvinsoneeeeegele@gmail.com', 'a13c4ad150f2b2a5', '$2y$10$qIp/T5V/vzwfbjl.8S02mO1AnQAvXxmjzxVdxMbLRART/mBmPorxm', 1590876776),
(56, 'calvinsongele@gmail.com', 'bd85b0e589beba7b', '$2y$10$v2ROUIAN7RQCNxsODeBWQeFaxdL/8IHGY/wW49Qu5rNNDG4yhyrw.', 1749028581);

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `post_tags`
--

INSERT INTO `post_tags` (`post_id`, `tag_id`) VALUES
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `s_ID` int(11) NOT NULL,
  `s_email` varchar(100) DEFAULT NULL,
  `s_date` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `name`) VALUES
(7, ' 203243 MILES'),
(8, ' 2500 CC'),
(10, ' Diesel'),
(9, ' Manual'),
(6, 'Local');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `user_full_name` varchar(100) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `user_phone` varchar(30) DEFAULT NULL,
  `user_role` varchar(20) DEFAULT 'Customer',
  `user_reg_date` varchar(30) DEFAULT NULL,
  `user_created_at` datetime DEFAULT current_timestamp(),
  `user_update_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_email_verified` enum('True','False') DEFAULT 'False',
  `user_phone_verified` enum('True','False') DEFAULT 'False'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_full_name`, `user_email`, `user_pass`, `user_phone`, `user_role`, `user_reg_date`, `user_created_at`, `user_update_at`, `user_email_verified`, `user_phone_verified`) VALUES
(1, 'rd', 'cc@gmail.com', '$2y$10$h57QSlp6Z.TUse2VMcHS7OTLljWygHstWrkA/b.THPJQ/j2v1qhHq', '09999', 'Patient', '1723188263', '2025-06-01 18:23:14', '2025-06-06 13:02:42', 'False', 'False'),
(6, 'cd', 'c@gmail.com', '$2y$10$zlz1YIOXO0OeluXUrK1qIeyq5BmLa4ipfaaKyCU63pA0dT7uWFOPW', '07000000', 'Admin', '1748792374', '2025-06-01 18:39:34', '2025-06-06 13:02:32', 'False', 'False'),
(7, 'tt', 'cx@gmail.com', '$2y$10$K3iX/jvqw1i.vy1cbi5.FOsOnWf2K3Hy00RIbVfajZA.zqskus9yG', '07000000', 'Customer', '1748793322', '2025-06-01 18:55:22', '2025-06-06 13:02:37', 'False', 'False');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`blog_ID`),
  ADD UNIQUE KEY `blog_slug` (`blog_slug`),
  ADD KEY `blog_user_fk_fk` (`blog_user_fk`),
  ADD KEY `blog_category_fk_fk` (`blog_category_fk`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`bc_ID`),
  ADD UNIQUE KEY `bc_name` (`bc_name`),
  ADD UNIQUE KEY `bc_url` (`bc_url`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id_fk` (`post_id`),
  ADD KEY `user_id_fk` (`user_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`c_ID`);

--
-- Indexes for table `contactus`
--
ALTER TABLE `contactus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`cont_ID`),
  ADD UNIQUE KEY `unique_given_id` (`cont_given_id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`l_id`),
  ADD UNIQUE KEY `l_user_id` (`l_user_id`,`l_post_id`),
  ADD KEY `l_post_id_fk` (`l_post_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`l_ID`);

--
-- Indexes for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`reset_ID`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`post_id`,`tag_id`),
  ADD KEY `tag_id_fk_1` (`tag_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`s_ID`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `uniqueuser` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `blog_ID` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `bc_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `c_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contactus`
--
ALTER TABLE `contactus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `cont_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `l_ID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2900;

--
-- AUTO_INCREMENT for table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `reset_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `subscribers`
--
ALTER TABLE `subscribers`
  MODIFY `s_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_category_fk_fk` FOREIGN KEY (`blog_category_fk`) REFERENCES `blog_categories` (`bc_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `blog_user_fk_fk` FOREIGN KEY (`blog_user_fk`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `post_id_fk` FOREIGN KEY (`post_id`) REFERENCES `blog` (`blog_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_ID`) ON DELETE SET NULL;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `l_post_id_fk` FOREIGN KEY (`l_post_id`) REFERENCES `blog` (`blog_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `l_user_id_fk` FOREIGN KEY (`l_user_id`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_id_fk_1` FOREIGN KEY (`post_id`) REFERENCES `blog` (`blog_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `tag_id_fk_1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
