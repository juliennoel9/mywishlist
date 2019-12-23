SET NAMES utf8;
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `liste_id` INT(11) NOT NULL,
  `nom` TEXT NOT NULL,
  `descr` TEXT,
  `img` TEXT,
  `url` TEXT,
  `tarif` DECIMAL(10,2) DEFAULT NULL,
  `reserve` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`liste_id`) REFERENCES `liste`(num) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `liste`;
CREATE TABLE `liste` (
  `num` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `titre` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` TEXT COLLATE utf8_unicode_ci,
  `expiration` TIMESTAMP NOT NULL,
  `token` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `public` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`num`),
  FOREIGN KEY (`user_id`) REFERENCES `account`(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL UNIQUE,
  `email` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `hash` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;