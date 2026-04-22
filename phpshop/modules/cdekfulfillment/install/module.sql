DROP TABLE IF EXISTS `phpshop_modules_cdekfulfillment_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_cdekfulfillment_system` (
  `id` int(11) NOT NULL auto_increment,
  `password` varchar(64) default '',
  `account` varchar(64) default '',
  `shop_id`  varchar(64) default '',
  `warehouse_id`  varchar(64) default '',
  `sender`  varchar(64) default '',
  `rate`  varchar(64) default '',
  `price` varchar(64) default '',
  `status` int(11),
  `paid` enum('0','1') DEFAULT '0',
  `log` enum('0','1') DEFAULT '0',
  `warehouse_cdek`  varchar(64) default '',
  `warehouse_main`  varchar(64) default '',
  `fee` int(11) NOT NULL,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_cdekfulfillment_system` VALUES (1, '', '','', '', '', '', '', 0, '1','1','','',0,'1.1');

CREATE TABLE IF NOT EXISTS `phpshop_modules_cdekfulfillment_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_code` varchar(64) default 'success',
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `cdekfulfillment_order_data` text default '';
ALTER TABLE `phpshop_orders` ADD `cdekfulfillment_delivery_price` int(11) NOT NULL;
ALTER TABLE `phpshop_products` ADD `export_cdek` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `export_cdek_id` BIGINT DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `barcode_cdek` varchar(64) default '';