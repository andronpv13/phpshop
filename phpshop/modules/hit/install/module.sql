DROP TABLE IF EXISTS `phpshop_modules_hit_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_hit_system` (
  `id` int(11) NOT NULL auto_increment,
  `hit_main` int(11) NOT NULL default 20,
  `hit_page` int(11) NOT NULL default 3,
  `hit_cat` INT(11) NOT NULL default 0,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_hit_system` VALUES (1, 20, 3, 0,'1.1');

ALTER TABLE `phpshop_products` ADD `hit` enum('0','1') DEFAULT '0', ADD INDEX(`hit`);