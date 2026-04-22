DROP TABLE IF EXISTS `phpshop_modules_productsgroup_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productsgroup_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_productsgroup_system` VALUES (1, '1.1');

ALTER TABLE `phpshop_products` ADD `productsgroup_check` enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `productsgroup_products` BLOB NOT NULL;
ALTER TABLE `phpshop_products` ADD `productsgroup_products_keys` text NOT NULL;