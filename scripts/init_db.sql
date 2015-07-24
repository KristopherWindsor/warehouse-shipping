
CREATE DATABASE IF NOT EXISTS `warehouse_shipping`;

USE `warehouse_shipping`;

CREATE TABLE IF NOT EXISTS `warehouse` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `address` TEXT NOT NULL,
  `lat` FLOAT NOT NULL,
  `lon` FLOAT NOT NULL
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `product` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `dimensions` TEXT NOT NULL,
  `weight` TEXT NOT NULL
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `warehouse_products` (
  `warehouse_id` INT NOT NULL UNIQUE,
  `product_id` INT NOT NULL UNIQUE,
  `quantity` INT NOT NULL,
  PRIMARY KEY (`warehouse_id`, `product_id`)
) DEFAULT CHARACTER SET utf8;