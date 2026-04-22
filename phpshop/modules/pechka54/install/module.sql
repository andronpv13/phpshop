ALTER TABLE `phpshop_orders` ADD `ofd` blob;
ALTER TABLE `phpshop_orders` ADD `ofd_status` enum('0','1','2','3') NOT NULL default '0';
ALTER TABLE `phpshop_orders` ADD `ofd_type` varchar(64) NOT NULL DEFAULT '';

CREATE TABLE `phpshop_modules_pechka54_system` (
  `id` int(11) NOT NULL auto_increment,
  `password` varchar(64) default '',
  `kkm` varchar(64) default '',
  `tax_product` int(11) default 0,
  `tax_delivery` int(11) default 0,
  `version` varchar(64) default '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 ;


INSERT INTO `phpshop_modules_pechka54_system` VALUES (1,'','',0,0,'1.2');

CREATE TABLE IF NOT EXISTS `phpshop_modules_pechka54_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `order_id` int(11),
  `order_uid` varchar(64) NOT NULL DEFAULT '',
  `status` enum('1','2') NOT NULL DEFAULT '1',
  `sum` float,
  `operation` varchar(64) NOT NULL DEFAULT '',
  `fiscal` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
     

CREATE TABLE IF NOT EXISTS `phpshop_modules_pechka54_taxes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_name` varchar(64) NOT NULL DEFAULT '',
  `tax_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;