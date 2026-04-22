DROP TABLE IF EXISTS `phpshop_modules_productday_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productday_system` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) default '0',
  `version` varchar(64) DEFAULT '1.3',
  `status` enum('1','2','3') default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_productday_system` VALUES (1,'24','1.3','1');
ALTER TABLE `phpshop_products` ADD `productday` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `productday_time` int(11) DEFAULT null;