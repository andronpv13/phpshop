DROP TABLE IF EXISTS `phpshop_modules_sliderfilter_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sliderfilter_system` (
  `id` int(11) NOT NULL auto_increment,
  `version` varchar(64) NOT NULL default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_sliderfilter_system` VALUES (1,'1.0');

ALTER TABLE `phpshop_sort_categories` ADD `sliderfilter_enabled` enum('0','1') DEFAULT '0';
