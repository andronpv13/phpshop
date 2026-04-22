DROP TABLE IF EXISTS `phpshop_modules_bitrix24_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_bitrix24_system` (
  `id` int(11) NOT NULL auto_increment,
  `webhook_url` varchar(255) DEFAULT '',
  `update_delivery_token` varchar(255) DEFAULT '',
  `delete_product_token` varchar(255) DEFAULT '',
  `delete_contact_token` varchar(255) DEFAULT '',
  `delete_company_token` varchar(255) DEFAULT '',
  `statuses` text NOT NULL,
  `version` varchar(64) DEFAULT '1.2',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
INSERT INTO `phpshop_modules_bitrix24_system` VALUES ('1', '', '', '', '', '', '', '1.2');

CREATE TABLE IF NOT EXISTS `phpshop_modules_bitrix24_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_categories` ADD `bitrix24_category_id` int(11) default '0';
ALTER TABLE `phpshop_products` ADD `bitrix24_product_id` int(11) default '0';
ALTER TABLE `phpshop_shopusers` ADD `bitrix24_client_id` int(11) default '0';
ALTER TABLE `phpshop_shopusers` ADD `bitrix24_company_id` int(11) default '0';
ALTER TABLE `phpshop_delivery` ADD `bitrix24_delivery_id` int(11) default '0';
ALTER TABLE `phpshop_orders` ADD `bitrix24_deal_id` varchar(255) DEFAULT '';