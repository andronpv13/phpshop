

DROP TABLE IF EXISTS `phpshop_modules_productlastview_system`;
CREATE TABLE `phpshop_modules_productlastview_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') NOT NULL default '1',
  `flag` enum('1','2') NOT NULL default '1',
  `title` varchar(64) NOT NULL default '',
  `pic_width` tinyint(100) NOT NULL default '0',
  `memory` enum('0','1') NOT NULL default '1',
  `num` tinyint(11) NOT NULL default '0',
  `serial` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_productlastview_system` VALUES (1, '0', '1', 'Просмотренные товары', 50, '1', 5, '');

DROP TABLE IF EXISTS phpshop_modules_productlastview_memory;
CREATE TABLE phpshop_modules_productlastview_memory (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memory` varchar(64) NOT NULL DEFAULT '',
  `product` text NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `memory` (`memory`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
