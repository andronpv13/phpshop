

DROP TABLE IF EXISTS `phpshop_modules_formgenerator_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_formgenerator_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) NOT NULL default '1.9',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_formgenerator_system` VALUES (1,'1.9');


DROP TABLE IF EXISTS `phpshop_modules_formgenerator_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_formgenerator_forms` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `path` varchar(64) NOT NULL default '',
  `content` text NOT NULL,
  `mail` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  `user_mail_copy` enum('0','1') NOT NULL default '1',
  `success_message` text NOT NULL,
  `error_message` text NOT NULL,
  `dir` text NOT NULL,
  `captcha` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;