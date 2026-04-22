DROP TABLE IF EXISTS `phpshop_modules_wbseller_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_wbseller_system` (
`id` int(11) NOT NULL auto_increment,
`token` text NOT NULL,
`status` int(11) NOT NULL default '0',
`price` int(11) NOT NULL default '0',
`fee` int(11) NOT NULL default '0',
`fee_type` enum('1','2') NOT NULL default '1',
`warehouse_id`  varchar(255) NOT NULL,
`type` enum('1','2') NOT NULL default '1',
`link` enum('0','1') NOT NULL default '0',
`status_import` varchar(64) default '',
`delivery` INT(11) NOT NULL default '0',
`create_products` enum('0','1') NOT NULL default '0',
`log` enum('0','1') NOT NULL default '0',
`discount` enum('0','1') NOT NULL default '0',
`version` varchar(64) DEFAULT '1.0',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_wbseller_system` VALUES (1,'','0','0','0','2','1','1','1','0','0','0','0','0','1.9');

CREATE TABLE IF NOT EXISTS `phpshop_modules_wbseller_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`order_id` int(11) NOT NULL,
`status` varchar(255) NOT NULL,
`status_code` varchar(64) default 'success',
`type` varchar(64) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `wbseller_order_data` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `category_wbseller` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_sort_categories` ADD `attribute_wbseller` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `export_wb` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_wb` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `export_wb_task_status` int(11) DEFAULT 0;
ALTER TABLE `phpshop_products` ADD `barcode_wb` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `export_wb_id` int(11) DEFAULT 0;
ALTER TABLE `phpshop_categories` ADD `category_wbseller_id` int(11) DEFAULT 0;

ALTER TABLE `phpshop_orders` ADD INDEX(`wbseller_order_data`); 