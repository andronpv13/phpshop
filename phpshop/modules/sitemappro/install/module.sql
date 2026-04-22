DROP TABLE IF EXISTS `phpshop_modules_sitemappro_system`;
CREATE TABLE `phpshop_modules_sitemappro_system` (
  `id` int(11) NOT NULL auto_increment,
  `limit_products` int(11) NOT NULL default '10000',
  `step` VARCHAR(64) DEFAULT 'content',
  `processed` int(11) NOT NULL default '0',
  `use_filter_combinations` enum('0','1') NOT NULL default '0',
  `version` VARCHAR(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_sitemappro_system` VALUES (1, '10000', 'content', '0', '0', '1.3');