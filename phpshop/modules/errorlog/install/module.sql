DROP TABLE IF EXISTS `phpshop_modules_errorlog_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_errorlog_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') default '0',
  `version` varchar(64) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



INSERT INTO `phpshop_modules_errorlog_system` VALUES (1,'0','1.2');


DROP TABLE IF EXISTS `phpshop_modules_errorlog_log`;
CREATE TABLE `phpshop_modules_errorlog_log` (
  `id` int(11) auto_increment,
  `date` int(11) default '0',
  `ip` varchar(64) default '',
  `error` text ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
