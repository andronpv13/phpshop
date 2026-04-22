

DROP TABLE IF EXISTS `phpshop_modules_button_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_button_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2','3') default '1',
  `editor` enum('0','1') default '0',
  `version` varchar(64) default '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_button_system` VALUES (1,'0','0','1.1');

DROP TABLE IF EXISTS `phpshop_modules_button_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_button_forms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `content` text NOT NULL,
  `enabled` enum('0','1') NOT NULL default '1',
  `num` TINYINT(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;