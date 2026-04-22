DROP TABLE IF EXISTS `phpshop_modules_megamarket_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_megamarket_system` (
`id` int(11) NOT NULL auto_increment,
`token` varchar(64) NOT NULL,
`status` int(11) NOT NULL default '0',
`price` int(11) NOT NULL default '0',
`fee` int(11) NOT NULL default '0',
`fee_type` enum('1','2') NOT NULL default '1',
`password`  varchar(255) NOT NULL,
`type` enum('1','2') NOT NULL default '1',
`export` enum('0','1','2') NOT NULL default '0',
`delivery` INT(11) NOT NULL default '0',
`log` enum('0','1') NOT NULL default '0',
`version` varchar(64) DEFAULT '1.0',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_megamarket_system` VALUES (1,'','0','0','','1','','1','0','0','1','1.1');

CREATE TABLE IF NOT EXISTS `phpshop_modules_megamarket_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`order_id` text NOT NULL,
`status` varchar(255) NOT NULL,
`status_code` varchar(64) default 'success',
`type` varchar(64) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `megamarket_order_id` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `export_megamarket` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_megamarket` float DEFAULT '0';

ALTER TABLE `phpshop_orders` ADD INDEX(`megamarket_order_id`); 