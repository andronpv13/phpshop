DROP TABLE IF EXISTS `phpshop_modules_showcaseselect_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_showcaseselect_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_showcaseselect_system` VALUES (1,'1.1');

ALTER TABLE `phpshop_servers` ADD `selector_name` varchar(64) NOT NULL default '';
ALTER TABLE `phpshop_page` ADD `selector_enabled` enum('0','1') NOT NULL default '0';