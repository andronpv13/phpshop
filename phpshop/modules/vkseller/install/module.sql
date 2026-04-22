DROP TABLE IF EXISTS `phpshop_modules_vkseller_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_vkseller_system` (
`id` int(11) NOT NULL auto_increment,
`token` text NULL,
`status` int(11) NOT NULL,
`price` int(11) NOT NULL,
`fee` int(11) NOT NULL,
`fee_type` enum('1','2') NOT NULL default '1',
`client_id` varchar(255) NOT NULL default '',
`client_secret` varchar(255) NOT NULL default '',
`owner_id` varchar(255) NOT NULL default '',
`type` enum('1','2') NOT NULL default '1',
`password` varchar(64) default '',
`model` varchar(64),
`link` enum('0','1') NOT NULL default '0',
`status_import` varchar(64) default '',
`delivery` INT(11) NOT NULL default '0',
`device_id` varchar(255) default '',
`refresh_token` text NULL,
`token_time` int(11) NOT NULL,
`version` varchar(64) DEFAULT '1.0',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_vkseller_system` VALUES (1, '', '',1,0,'1','','','','1','','YML','0','',0,'','',0, '1.4');

CREATE TABLE IF NOT EXISTS `phpshop_modules_vkseller_log` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`order_id` int(11) NOT NULL,
`status` varchar(255) NOT NULL,
`status_code` varchar(64) default 'success',
`type` varchar(64) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `vkseller_order_data` text default '';
ALTER TABLE `phpshop_products` ADD `export_vk` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_vk` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `export_vk_task_status` int(11) DEFAULT 0;
ALTER TABLE `phpshop_products` ADD `export_vk_id` int(11) DEFAULT 0;
ALTER TABLE `phpshop_categories` ADD `category_vkseller` int(11) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `phpshop_modules_vkseller_export` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`date` int(11) NOT NULL,
`message` text NOT NULL,
`product_id` int(11) NOT NULL,
`product_name` VARCHAR(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
