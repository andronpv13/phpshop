DROP TABLE IF EXISTS `phpshop_modules_moysklad_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_moysklad_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255),
  `organization` varchar(64),
  `currency` varchar(64),
  `pricetype` varchar(64),
  `account` varchar(64),
  `status` int(11),
  `webhooks` enum('1','2') default '2',
  `version` varchar(64),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_moysklad_system` VALUES (1, '', '', '','','','','2','1.3');

DROP TABLE IF EXISTS `phpshop_modules_moysklad_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_moysklad_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `moysklad_product_id` varchar(64) default '';
ALTER TABLE `phpshop_shopusers` ADD `moysklad_client_id` varchar(64) default '';
ALTER TABLE `phpshop_delivery` ADD `moysklad_delivery_id` varchar(64) default '';
ALTER TABLE `phpshop_orders` ADD `moysklad_deal_id` varchar(64) default '';
ALTER TABLE `phpshop_parent_name` ADD `moysklad_char_id` varchar(64) default '';
ALTER TABLE `phpshop_parent_name` ADD `moysklad_char2_id` varchar(64) default '';