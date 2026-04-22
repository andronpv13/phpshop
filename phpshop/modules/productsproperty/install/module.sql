DROP TABLE IF EXISTS `phpshop_modules_productsproperty_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productsproperty_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_productsproperty_system` VALUES (1, '1.1');

ALTER TABLE `phpshop_products` ADD `productsproperty_array` BLOB NOT NULL;