-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE `attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5` varchar(32) NOT NULL,
  `size` int(10) NOT NULL,
  `type` varchar(30) NOT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `box`;
CREATE TABLE `box` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `status` enum('ok','del') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT 'ok',
  `show_filters` int(1) NOT NULL DEFAULT '1',
  `show_navigation` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `box_item`;
CREATE TABLE `box_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `box_id` int(11) NOT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  `detail_color` varchar(6) DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `secondtitle` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `category` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `position` int(3) DEFAULT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  KEY `attachment_id` (`attachment_id`),
  KEY `box_id` (`box_id`),
  CONSTRAINT `box_item_ibfk_3` FOREIGN KEY (`attachment_id`) REFERENCES `attachment` (`id`),
  CONSTRAINT `box_item_ibfk_4` FOREIGN KEY (`box_id`) REFERENCES `box` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `carousel`;
CREATE TABLE `carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `status` enum('ok','del') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT 'ok',
  `show_navigation` int(1) NOT NULL DEFAULT '1',
  `show_header` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `carousel_item`;
CREATE TABLE `carousel_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carousel_id` int(11) NOT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `text2` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `link_text` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `position` int(3) DEFAULT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  KEY `attachment_id` (`attachment_id`),
  KEY `carousel_id` (`carousel_id`),
  CONSTRAINT `carousel_item_ibfk_3` FOREIGN KEY (`attachment_id`) REFERENCES `attachment` (`id`),
  CONSTRAINT `carousel_item_ibfk_4` FOREIGN KEY (`carousel_id`) REFERENCES `carousel` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `compose_article`;
CREATE TABLE `compose_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `keywords` varchar(150) NOT NULL,
  `description` varchar(150) NOT NULL,
  `content` text,
  `notes` text,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `compose_article_item`;
CREATE TABLE `compose_article_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `compose_article_id` int(11) NOT NULL,
  `position` int(3) NOT NULL,
  `status` enum('ok','del','hide') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  KEY `compose_article_id` (`compose_article_id`),
  CONSTRAINT `compose_article_item_ibfk_1` FOREIGN KEY (`compose_article_id`) REFERENCES `compose_article` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `compose_article_item_param`;
CREATE TABLE `compose_article_item_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` varchar(1000) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `compose_article_item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compose_article_item_id` (`compose_article_item_id`),
  CONSTRAINT `compose_article_item_param_ibfk_2` FOREIGN KEY (`compose_article_item_id`) REFERENCES `compose_article_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `feed_item`;
CREATE TABLE `feed_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `datetime` datetime NOT NULL,
  `header` varchar(250) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `perex` varchar(500) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `detail` varchar(500) NOT NULL,
  `feed_item_id` varchar(32) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_feed_item_id` (`type`,`feed_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `flat_photo_gallery`;
CREATE TABLE `flat_photo_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `status` enum('ok','del') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `flat_photo_gallery_item`;
CREATE TABLE `flat_photo_gallery_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flat_photo_gallery_id` int(11) NOT NULL,
  `attachment_id` int(11) NOT NULL,
  `text` text CHARACTER SET utf8 COLLATE utf8_czech_ci,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `position` int(3) DEFAULT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  KEY `attachment_id` (`attachment_id`),
  KEY `flat_photo_gallery_id` (`flat_photo_gallery_id`),
  CONSTRAINT `flat_photo_gallery_item_ibfk_3` FOREIGN KEY (`attachment_id`) REFERENCES `attachment` (`id`),
  CONSTRAINT `flat_photo_gallery_item_ibfk_4` FOREIGN KEY (`flat_photo_gallery_id`) REFERENCES `flat_photo_gallery` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `full_page_text`;
CREATE TABLE `full_page_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_text` varchar(150) NOT NULL,
  `link` varchar(250) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `published` int(1) NOT NULL DEFAULT '0',
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `presenter` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `params` tinytext,
  `options` tinytext,
  `url` varchar(255) DEFAULT NULL,
  `homepage` tinyint(1) NOT NULL DEFAULT '0',
  `lft` int(5) NOT NULL,
  `rgt` int(5) NOT NULL,
  `depth` int(5) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `published` int(1) NOT NULL DEFAULT '0',
  `secured` int(1) NOT NULL,
  `target` varchar(10) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `lft` (`lft`),
  KEY `menu_id` (`menu_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_item_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `text_article`;
CREATE TABLE `text_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `keywords` varchar(150) DEFAULT NULL,
  `description` varchar(150) DEFAULT NULL,
  `perex` varchar(255) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `notes` text,
  `category` int(11) DEFAULT NULL,
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  `homepage` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  CONSTRAINT `text_article_ibfk_1` FOREIGN KEY (`category`) REFERENCES `text_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `text_article_content`;
CREATE TABLE `text_article_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `create_date` datetime NOT NULL,
  `status` enum('ok','del') NOT NULL,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `author` (`author`),
  CONSTRAINT `text_article_content_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `text_article` (`id`),
  CONSTRAINT `text_article_content_ibfk_2` FOREIGN KEY (`author`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `text_category`;
CREATE TABLE `text_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `description` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `parent_category` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_category` (`parent_category`),
  CONSTRAINT `text_category_ibfk_1` FOREIGN KEY (`parent_category`) REFERENCES `text_category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `identity_no` varchar(40) CHARACTER SET utf8 COLLATE utf8_czech_ci DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `surname` varchar(150) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `role` varchar(150) NOT NULL,
  `status` set('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `organization_id` (`organization_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`),
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `identity_no`, `username`, `password`, `name`, `surname`, `phone`, `email`, `organization_id`, `role`, `status`) VALUES
(1,	'KHOHOHOIHIOhiohoHOHIUOIhoHIIOHOHOHOhoHOH',	'root',	'$2a$07$2kkmkh73splfeh79fh8obedQxrK8FFqyIvWUEz2BrVfTgMXW.ObSa',	'Petr',	'Horáček',	'728514123',	'petr.horacek@wunderman.cz',	NULL,	'admin',	'ok');

-- 2016-06-10 00:06:39