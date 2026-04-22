DROP TABLE IF EXISTS `phpshop_modules_deliverywidget_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_deliverywidget_system` (
  `id` int(11) NOT NULL auto_increment,
  `index_from` varchar(64) default '',
  `cache` enum('0','1','2') DEFAULT '0',
  `weight` varchar(64) default '',
  `time` varchar(64) default '',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_deliverywidget_system` VALUES (1,'','0','500','1', '1.1');

DROP TABLE IF EXISTS `phpshop_modules_deliverywidget_cache`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_deliverywidget_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;