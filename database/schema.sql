-- Adminer 4.2.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staropramen_id` int(11) DEFAULT NULL,
  `customer` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(16) NOT NULL DEFAULT '',
  `phone` varchar(128) NOT NULL DEFAULT '',
  `segment` varchar(32) NOT NULL,
  `status` varchar(32) NOT NULL,
  `rop_id` int(11) NOT NULL,
  `district` varchar(32) NOT NULL,
  `city` varchar(64) NOT NULL,
  `zip` char(5) NOT NULL,
  `street` varchar(64) NOT NULL,
  `street_number_o` varchar(8) DEFAULT NULL,
  `street_number_p` varchar(8) DEFAULT NULL,
  `street_number_is` varchar(8) DEFAULT NULL,
  `map_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `description` text,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  `tv` tinyint(1) NOT NULL DEFAULT '0',
  `xdsl` tinyint(1) NOT NULL DEFAULT '0',
  `fv` tinyint(1) NOT NULL DEFAULT '0',
  `pp` tinyint(4) NOT NULL DEFAULT '0',
  `blacklist` tinyint(1) NOT NULL,
  `o2_tv_accessibility` tinyint(1) NOT NULL DEFAULT '0',
  `o2_tv_installed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL,
  `recipient` varchar(150) CHARACTER SET utf8 NOT NULL,
  `template` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `subject` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `params` text COLLATE utf8_czech_ci NOT NULL,
  `created` datetime NOT NULL,
  `sended` datetime DEFAULT NULL,
  `error` varchar(200) COLLATE utf8_czech_ci DEFAULT NULL,
  `status` enum('new','sended','postponed') CHARACTER SET ucs2 NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  CONSTRAINT `email_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `district` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `village` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `okres` (`district`),
  KEY `kraj` (`region`,`district`,`village`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `location_district`;
CREATE TABLE `location_district` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `region_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `region_id` (`region_id`),
  CONSTRAINT `location_district_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `location_region` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `location_region`;
CREATE TABLE `location_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16;


DROP TABLE IF EXISTS `location_street`;
CREATE TABLE `location_street` (
  `id` mediumint(6) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `street` varchar(60) COLLATE utf8_czech_ci NOT NULL,
  `lat` float DEFAULT NULL,
  `lng` float DEFAULT NULL,
  `export_user_id` int(11) DEFAULT NULL,
  `export_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`location_id`,`street`),
  KEY `lat` (`lat`),
  KEY `lng` (`lng`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `organization`;
CREATE TABLE `organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `type` enum('o2','brewery') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT 'o2',
  `status` enum('ok','del') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
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


DROP TABLE IF EXISTS `user_activity`;
CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(15) NOT NULL,
  `datetime` datetime NOT NULL,
  `get` text NOT NULL,
  `post` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user_district`;
CREATE TABLE `user_district` (
  `user_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`location_id`),
  KEY `okres` (`location_id`),
  CONSTRAINT `user_district_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_district_ibfk_4` FOREIGN KEY (`location_id`) REFERENCES `location_district` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2015-07-27 08:53:12