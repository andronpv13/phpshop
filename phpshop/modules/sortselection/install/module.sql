DROP TABLE IF EXISTS `phpshop_modules_sortselection_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sortselection_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('1','2') NOT NULL default '1',
  `flag` enum('1','2') NOT NULL default '1',
  `sort_categories` int(11) NOT NULL default '0',
  `sort` varchar(255) NOT NULL default '',
  `title` varchar(64) NOT NULL default '',
  `version` varchar(64) NOT NULL default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_sortselection_system` VALUES (1,'1','1','0','','Подобрать товар по параметрам','1.0');
