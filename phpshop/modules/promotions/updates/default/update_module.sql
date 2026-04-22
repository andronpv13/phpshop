ALTER TABLE `phpshop_modules_promotions_forms` ADD `label` text;
ALTER TABLE `phpshop_modules_promotions_forms` ADD `header_mail` varchar(255);
ALTER TABLE `phpshop_modules_promotions_forms` ADD `content_mail` text;
ALTER TABLE `phpshop_modules_promotions_forms` ADD `block_old_price` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_modules_promotions_forms` ADD `statuses` text NOT NULL DEFAULT '';
ALTER TABLE `phpshop_modules_promotions_forms` ADD `status_check` enum('0','1') NOT NULL DEFAULT '0';

DROP TABLE IF EXISTS `phpshop_modules_promotions_codes`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_promotions_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL UNIQUE,
  `enabled` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_promotions_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_promotions_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
INSERT INTO `phpshop_modules_promotions_system` VALUES (1,'2.5');