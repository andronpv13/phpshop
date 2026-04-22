DROP TABLE IF EXISTS `phpshop_modules_cdekwidget_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_cdekwidget_system` (
  `id` int(11) NOT NULL auto_increment,
  `password` varchar(255) default '',
  `account` varchar(255) default '',
  `status` int(11),
  `delivery_id` varchar(64) default '',
  `city_from` varchar(64) default '',
  `city_from_code` varchar(64) default '',
  `index_from` varchar(64) default '',
  `default_city` varchar(64) default '',
  `length` varchar(64) default '',
  `weight` varchar(64) default '',
  `width` varchar(64) default '',
  `height` varchar(64) default '',
  `russia_only` enum('0','1') NOT NULL default '0',
  `fee` int(11) default 0,
  `fee_type` enum('1','2') DEFAULT '1',
  `test` enum('0','1')  DEFAULT '0',
  `paid` enum('0','1') DEFAULT '0',
  `webhook` enum('0','1') DEFAULT '0',
 `cost` int(11) default 0,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_cdekwidget_system` VALUES (1, '', '', '0', '', '', '', '', '', '','', '', '', 0,  0, 1, 0, 0, 0,0,'2.7');

CREATE TABLE IF NOT EXISTS `phpshop_modules_cdekwidget_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_code` varchar(64) default 'success',
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `cdek_order_data` text default '';
ALTER TABLE `phpshop_delivery` ADD `cdek_paid` enum('0','1') DEFAULT '0';