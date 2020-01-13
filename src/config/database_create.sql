SET NAMES utf8mb4;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `cagnotte`;
CREATE TABLE `cagnotte` (
`item_id` INT(11) NOT NULL,
`account_id` INT(11) NOT NULL,
`montant` DECIMAL(10,2) NOT NULL,
PRIMARY KEY (`item_id`, `account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `liste_id` INT(11) NOT NULL,
  `nom` TEXT NOT NULL,
  `descr` TEXT,
  `img` TEXT,
  `url` TEXT,
  `tarif` DECIMAL(10,2),
  `account_id_reserv` INT(11),
  `messageReservation` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
`id` INT(11) NOT NULL AUTO_INCREMENT,
`liste_id` INT(11) NOT NULL,
`account_id` INT(11),
`nomMessage` VARCHAR(61) NOT NULL,
`message` TEXT NOT NULL,
`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `liste`;
CREATE TABLE `liste` (
  `num` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `expiration` DATE NOT NULL,
  `token` VARCHAR(255) COLLATE utf8mb4_bin NOT NULL,
  `public` BOOLEAN DEFAULT FALSE,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(20) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `hash` VARCHAR(256) NOT NULL,
  `nom` VARCHAR(30) NOT NULL,
  `prenom` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE `cagnotte`
ADD FOREIGN KEY (`item_id`) REFERENCES `item`(id) ON DELETE CASCADE,
ADD FOREIGN KEY (`account_id`) REFERENCES `account`(id) ON DELETE CASCADE;

ALTER TABLE `item`
ADD FOREIGN KEY (`liste_id`) REFERENCES `liste`(num) ON DELETE CASCADE,
ADD FOREIGN KEY (`account_id_reserv`) REFERENCES `account`(id) ON DELETE SET NULL;

ALTER TABLE `message`
ADD FOREIGN KEY (`liste_id`) REFERENCES `liste`(num) ON DELETE CASCADE,
ADD FOREIGN KEY (`account_id`) REFERENCES `account`(id) ON DELETE SET NULL;

ALTER TABLE `liste`
ADD FOREIGN KEY (`user_id`) REFERENCES `account`(id) ON DELETE CASCADE;
