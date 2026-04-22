DROP TABLE IF EXISTS `phpshop_modules_retailcrm_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_retailcrm_system` (
  `code` varchar(64) NOT NULL default '',
  `value` text NOT NULL,
  `version` varchar(64) DEFAULT '1.0'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_retailcrm_system` VALUES ('options', '{"status":"0","email":"integration@retailcrm.ru"}', '3.5');

CREATE TABLE IF NOT EXISTS `phpshop_modules_retailcrm_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `retail_status` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `retail_product_id` varchar(64) default '';
ALTER TABLE `phpshop_shopusers` ADD `retail_user_id` varchar(64) default '';