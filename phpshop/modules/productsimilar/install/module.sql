DROP TABLE IF EXISTS `phpshop_modules_productsimilar_system`;
CREATE TABLE `phpshop_modules_productsimilar_system` (
  `id` int(11) NOT NULL auto_increment,
  `num` tinyint(11) NOT NULL default '0',
  `title` varchar(64) NOT NULL default '',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_productsimilar_system` VALUES (1, 10,'Похожие товары','1.0');
ALTER TABLE `phpshop_sort_categories` ADD `productsimilar_enabled` enum('0','1') DEFAULT '0';