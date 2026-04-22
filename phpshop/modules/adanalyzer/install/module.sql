
DROP TABLE IF EXISTS `phpshop_modules_adanalyzer_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_adanalyzer_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1') default '1',
   `status` enum('0','1')  default '1',
  `version` varchar(64) default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_adanalyzer_system` VALUES (1,'1','1','1.1');

DROP TABLE IF EXISTS `phpshop_modules_adanalyzer_campaign`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_adanalyzer_campaign` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) default '',
  `content` text,
  `enabled` enum('0','1') default '1',
  `num` TINYINT(11),
  `utm` VARCHAR(64) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `utm` VARCHAR(64);