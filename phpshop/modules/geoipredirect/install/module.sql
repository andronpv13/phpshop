

DROP TABLE IF EXISTS `phpshop_modules_geoipredirect_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_geoipredirect_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_geoipredirect_system` VALUES (1,'1.0');

DROP TABLE IF EXISTS `phpshop_modules_geoipredirect_city`;
CREATE TABLE `phpshop_modules_geoipredirect_city` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `host` varchar(64) NOT NULL default '',
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;