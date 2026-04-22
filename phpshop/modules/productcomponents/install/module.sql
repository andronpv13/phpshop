DROP TABLE IF EXISTS `phpshop_modules_productcomponents_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productcomponents_system` (
  `id` int(11) NOT NULL auto_increment,
  `discount` int(11) NOT NULL,
  `product_search` enum('0','1') default '1',
  `logic` enum('0','1','2') default '0',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_productcomponents_system` VALUES (1, 10,'1', '0', '1.3');

ALTER TABLE `phpshop_products` ADD `productcomponents_products` varchar(255) NOT NULL;
ALTER TABLE `phpshop_products` ADD `productcomponents_discount` int(11) NOT NULL;
ALTER TABLE `phpshop_products` ADD `productcomponents_markup` int(11) NOT NULL;
ALTER TABLE `phpshop_products` ADD `productcomponents_sort` enum('0','1') default '0';