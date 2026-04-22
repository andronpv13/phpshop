DROP TABLE IF EXISTS `phpshop_modules_saferoutewidget_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_saferoutewidget_system` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(64) default '',
  `shop_id` int(11) default '0',
  `delivery_id` varchar(64) default '',
  `status` int(11),
  `prod_enabled` enum('1','2') default '2',
  `version` FLOAT(2) DEFAULT '1.1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_saferoutewidget_system` VALUES (1,'','','','0', '2', '1.1');
ALTER TABLE `phpshop_orders` ADD `saferoute_token` varchar(64) default '';
ALTER TABLE `phpshop_delivery` ADD `is_mod` enum('1','2') DEFAULT '1';