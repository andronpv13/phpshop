

DROP TABLE IF EXISTS `phpshop_modules_sortproduct_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sortproduct_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') NOT NULL default '1',
  `title` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_sortproduct_system` VALUES (1,'1','','');

DROP TABLE IF EXISTS `phpshop_modules_sortproduct_forms`;
CREATE TABLE `phpshop_modules_sortproduct_forms` (
  `id` int(11) NOT NULL auto_increment,
  `sort` int(11) NOT NULL default '0',
  `value_id` int(11) NOT NULL default '0',
  `value_name` varchar(64) NOT NULL default '',
  `items` text NOT NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `num` tinyint(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;