

DROP TABLE IF EXISTS `phpshop_modules_sort_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sort_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') NOT NULL default '1',
  `flag` enum('1','2') NOT NULL default '1',
  `sort` int(11) NOT NULL default '0',
  `title` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_sort_system` VALUES (1,'1','1','','','');
