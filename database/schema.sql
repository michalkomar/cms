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

INSERT INTO `compose_article` (`id`, `title`, `keywords`, `description`, `content`, `notes`, `status`) VALUES
(1,	'My First Page',	'',	'',	NULL,	NULL,	'ok');

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


DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `published` int(1) NOT NULL DEFAULT '0',
  `status` enum('ok','del') NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `menu` (`id`, `name`, `published`, `status`) VALUES
(1,	'MainMenu',	1,	'ok');

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

INSERT INTO `menu_item` (`id`, `name`, `presenter`, `action`, `params`, `options`, `url`, `homepage`, `lft`, `rgt`, `depth`, `parent_id`, `published`, `secured`, `target`, `menu_id`, `status`) VALUES
(1,	'My First Page',	'Public:Compose:Compose',	'default',	'{\"id\":1}',	NULL,	'my-first-page',	1,	1,	2,	0,	NULL,	1,	0,	'_self',	1,	'ok');

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
(1,	'KHOHOHOIHIOhiohoHOHIUOIhoHIIOHOHOHOhoHOH',	'root',	'$2a$07$2kkmkh73splfeh79fh8obedQxrK8FFqyIvWUEz2BrVfTgMXW.ObSa',	'Admin',	'Istrator',	'',	'email@example.com',	NULL,	'admin',	'ok');

-- 2016-06-16 07:13:31