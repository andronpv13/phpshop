

DROP TABLE IF EXISTS `phpshop_modules_mobile_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_mobile_system` (
  `id` int(11) auto_increment,
  `message` varchar(255) default '',
  `logo` varchar(255) default '',
  `returncall` enum('1','2') default '1',
  `skin` varchar(64) DEFAULT 'mobile',
  `version` varchar(64) DEFAULT '1.4',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



INSERT INTO `phpshop_modules_mobile_system` VALUES (1,'ƒоступна мобильна€ верси€ сайта, перейти?','','1','mobile','1.');