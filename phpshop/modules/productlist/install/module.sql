DROP TABLE IF EXISTS `phpshop_modules_productlist_system`;
CREATE TABLE `phpshop_modules_productlist_system` (
  `id` int(11) NOT NULL auto_increment,
  `num` tinyint(11) NOT NULL default '0',
  `title` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  `enabled` enum('0','1','2') NOT NULL default '1',
  `version` varchar(64) DEFAULT '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_productlist_system` VALUES (1, 10,'Похожие товары', '','0','1.3');
