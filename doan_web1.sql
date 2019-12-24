-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th12 24, 2019 lúc 05:45 AM
-- Phiên bản máy phục vụ: 5.7.26
-- Phiên bản PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `doan_web1`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment_status_id` int(11) DEFAULT NULL,
  `comment_user_id` int(11) DEFAULT NULL,
  `comment_content` text CHARACTER SET utf8,
  `comment_created` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `fk_comments_users` (`comment_user_id`),
  KEY `fk_comments_status` (`comment_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forgot_password`
--

DROP TABLE IF EXISTS `forgot_password`;
CREATE TABLE IF NOT EXISTS `forgot_password` (
  `forgot_password_id` int(11) NOT NULL AUTO_INCREMENT,
  `forgot_password_email` varchar(50) DEFAULT NULL,
  `forgot_password_token` varchar(50) DEFAULT NULL,
  `forgot_password_experied` datetime DEFAULT NULL,
  PRIMARY KEY (`forgot_password_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `message_user_id` int(11) NOT NULL,
  `message_from_user_id` int(11) NOT NULL,
  `message_content` varchar(255) NOT NULL,
  `message_created` datetime NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `register`
--

DROP TABLE IF EXISTS `register`;
CREATE TABLE IF NOT EXISTS `register` (
  `register_id` int(11) NOT NULL AUTO_INCREMENT,
  `register_email` varchar(100) NOT NULL,
  `register_password` varchar(200) NOT NULL,
  `register_token` varchar(200) NOT NULL,
  `register_displayname` varchar(50) NOT NULL,
  PRIMARY KEY (`register_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_user_id` int(11) NOT NULL,
  `status_content` text NOT NULL,
  `status_created` datetime DEFAULT NULL,
  `status_role` text,
  `status_image` varchar(100) DEFAULT NULL,
  `status_wholiked` text,
  PRIMARY KEY (`status_id`),
  KEY `fk_status_users` (`status_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_displayname` varchar(50) DEFAULT NULL,
  `user_phone` varchar(12) DEFAULT NULL,
  `user_avatar` longblob,
  `user_birthday` int(11) NOT NULL DEFAULT '1970',
  `user_followed` text,
  `user_follows` text,
  `user_following` text,
  `user_created` datetime DEFAULT NULL,
  `user_lastlogin` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_status` FOREIGN KEY (`comment_status_id`) REFERENCES `status` (`status_id`),
  ADD CONSTRAINT `fk_comments_users` FOREIGN KEY (`comment_user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `fk_status_users` FOREIGN KEY (`status_user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
