DROP TABLE IF EXISTS `phpshop_modules_productservices_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productservices_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_productservices_system` VALUES (1, '1.1');

ALTER TABLE `phpshop_products` ADD `productservices_products` varchar(255) DEFAULT null;
ALTER TABLE `phpshop_products` ADD `productservices_discount` int(11)  DEFAULT 0;