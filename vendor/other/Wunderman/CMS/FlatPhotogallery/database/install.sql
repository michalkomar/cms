CREATE TABLE `flat_photo_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL,
  `status` enum('ok','del') CHARACTER SET utf8 COLLATE utf8_czech_ci NOT NULL DEFAULT 'ok',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;