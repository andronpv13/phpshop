

DROP TABLE IF EXISTS `phpshop_modules_deliverycalc_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_deliverycalc_system` (
  `id` int(11) NOT NULL auto_increment,
  `code` text NOT NULL default '',
  `target` text NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



INSERT INTO `phpshop_modules_deliverycalc_system` VALUES (1,'','','1.0');