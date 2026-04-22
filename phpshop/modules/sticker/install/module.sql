

DROP TABLE IF EXISTS `phpshop_modules_sticker_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sticker_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) default '1.2',
  `editor` enum('0','1') default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_sticker_system` VALUES (1,'1.3','0');


DROP TABLE IF EXISTS `phpshop_modules_sticker_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sticker_forms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default '',
  `path` varchar(64) default '',
  `content` text,
  `mail` varchar(64) default '',
  `enabled` enum('0','1') default '1',
  `dir` text,
  `skin` varchar(64) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;