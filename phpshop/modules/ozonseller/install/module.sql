DROP TABLE IF EXISTS `phpshop_modules_ozonseller_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_system` (
`id` int(11) NOT NULL auto_increment,
`token` varchar(64) default '',
`client_id` varchar(64) default '',
`status` int(11) NOT NULL,
`price` int(11) NOT NULL,
`fee` int(11) NOT NULL,
`password` varchar(64),
`fee_type` enum('1','2') NOT NULL default '1',
`warehouse` TEXT NOT NULL,
`type` enum('1','2') NOT NULL default '1',
`link` enum('0','1') NOT NULL default '0',
`status_import` varchar(64) default '',
`delivery` INT(11) NOT NULL default '0',
`create_products` enum('0','1') NOT NULL default '0',
`log` enum('0','1') NOT NULL default '0',
`export` enum('0','1','2') NOT NULL default '0',
`export_items_limit` int(11) NOT NULL DEFAULT '0',
`version` varchar(64) DEFAULT '1.0',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_ozonseller_system` VALUES (1, '', '', 0,1,0,'','1','','1','0','',0,'0','0','0',0,'2.8');

CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`order_id` int(11) NOT NULL,
`status` varchar(255) NOT NULL,
`status_code` varchar(64) default 'success',
`type` varchar(64) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_categories` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `ozonseller_order_data` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `category_ozonseller` int(11) DEFAULT 0;
ALTER TABLE `phpshop_sort_categories` ADD `attribute_ozonseller` int(11) DEFAULT 0;
ALTER TABLE `phpshop_products` ADD `export_ozon` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `export_ozon_task_id` BIGINT DEFAULT 0;
ALTER TABLE `phpshop_products` ADD `price_ozon` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `export_ozon_task_status` varchar(64) default '';
ALTER TABLE `phpshop_products` ADD `barcode_ozon` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `export_ozon_id` BIGINT DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `sku_ozon` BIGINT DEFAULT 0;

CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_export` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`product_id` int(11) NOT NULL,
`product_name` VARCHAR(255) NOT NULL,
`product_image` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_modules_ozonseller_type` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD INDEX(`ozonseller_order_data`); 