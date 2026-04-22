

DROP TABLE IF EXISTS `phpshop_modules_returncall_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_returncall_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') default '1',
  `title` varchar(64) default '',
  `title_end` text,
  `windows` enum('0','1') default '1',
  `captcha_enabled` enum('1','2') default '1',
  `status` int(11) default '0',
  `version` varchar(64) DEFAULT '' ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_returncall_system` VALUES (1,'0','Обратный звонок','Спасибо! Мы скоро свяжемся с Вами.','1','1','0','1.7');

DROP TABLE IF EXISTS `phpshop_modules_returncall_jurnal`;
CREATE TABLE `phpshop_modules_returncall_jurnal` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `time_start` varchar(64) default '',
  `time_end` varchar(64) default '',
  `name` varchar(64) default '',
  `tel` varchar(64) default '',
  `message` text ,
  `status` int(11) default '0',
  `ip` varchar(64) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;