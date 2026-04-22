DROP TABLE IF EXISTS `phpshop_modules_branches_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_branches_system` (
  `id` int(11) NOT NULL auto_increment,
  `default_city_id` int(11) default 0,
  `favorite_cities` text,
  `yandex_api_key` varchar(255) default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_branches_system` VALUES (1, 0, '', '', '1.1');

DROP TABLE IF EXISTS `phpshop_modules_branches_branches`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_branches_branches` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default '',
  `address` varchar(255) default '',
  `lon` varchar(255) default '',
  `lat` varchar(255) default '',
  `city_id` int(11) default 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;