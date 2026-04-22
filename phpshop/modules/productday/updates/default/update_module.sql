DROP TABLE IF EXISTS `phpshop_modules_productday_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productday_system` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) default '0',
  `version` varchar(64) DEFAULT '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_productday_system` VALUES (1,'18','1.1');