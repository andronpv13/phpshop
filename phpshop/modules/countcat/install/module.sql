ALTER TABLE `phpshop_categories` ADD `count` INT(11) NOT NULL;

DROP TABLE IF EXISTS `phpshop_modules_countcat_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_countcat_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_countcat_system` VALUES (1,'0');