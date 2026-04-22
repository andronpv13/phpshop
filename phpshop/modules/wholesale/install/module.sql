DROP TABLE IF EXISTS `phpshop_modules_wholesale_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_wholesale_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
INSERT INTO `phpshop_modules_wholesale_system` VALUES (1,'1.0');

DROP TABLE IF EXISTS `phpshop_modules_wholesale_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_wholesale_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') DEFAULT '0',
  `description` text NOT NULL,
  `label` text NOT NULL,
  `active_check` enum('0','1') NOT NULL,
  `active_date_ot` varchar(255) NOT NULL,
  `active_date_do` varchar(255) NOT NULL,
  `discount_check` enum('0','1') NOT NULL,
  `discount_tip` enum('0','1','2') NOT NULL,
  `discount` int(11) NOT NULL,
  `categories_check` enum('0','1') NOT NULL,
  `categories` text NOT NULL,
  `status_check` enum('0','1') NOT NULL DEFAULT '0',
  `statuses` text NOT NULL DEFAULT '',
  `products_check` enum('0','1') NOT NULL,
  `products` text NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `block_old_price` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `wholesale_check` int(11) DEFAULT '5';
ALTER TABLE `phpshop_products` ADD `wholesale_discount` int(11) DEFAULT '10';
ALTER TABLE `phpshop_products` ADD `wholesale_price` int(11) DEFAULT '2';