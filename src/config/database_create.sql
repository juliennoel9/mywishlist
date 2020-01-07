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
  `account_id` INT(11) DEFAULT NULL,
  `nomReservation` VARCHAR(30) DEFAULT NULL,
  `messageReservation` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `liste`;
CREATE TABLE `liste` (
  `num` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `expiration` TIMESTAMP NOT NULL,
  `token` VARCHAR(255) COLLATE utf8_bin DEFAULT NULL,
  `public` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `hash` VARCHAR(256) NOT NULL,
  `nom` VARCHAR(30) NOT NULL,
  `prenom` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`liste_id` INT(11) NOT NULL,
`account_id` INT(11) DEFAULT NULL,
`nomMessage` VARCHAR(30),
`message` TEXT,
`date` datetime NOT NULL,
PRIMARY KEY (`id`),
FOREIGN KEY (`liste_id`) REFERENCES `liste`(num),
FOREIGN KEY (`account_id`) REFERENCES `account`(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*
ALTER TABLE `item`
ADD FOREIGN KEY (`liste_id`) REFERENCES `liste`(num) ON DELETE CASCADE,
ADD FOREIGN KEY (`account_id`) REFERENCES `account`(id) ON DELETE CASCADE;
  
ALTER TABLE `liste`
ADD FOREIGN KEY (`user_id`) REFERENCES `account`(id);
*/