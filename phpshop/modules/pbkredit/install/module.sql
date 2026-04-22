DROP TABLE IF EXISTS `phpshop_modules_pbkredit_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_pbkredit_system` (
  `id` int(11) NOT NULL auto_increment,
  `tt_code` varchar(255) default '',
  `tt_name` varchar(255) default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_pbkredit_system` VALUES (1,'', '', '1.0');

ALTER TABLE `phpshop_products` ADD `pbkredit_disabled` enum('0','1') DEFAULT '0';