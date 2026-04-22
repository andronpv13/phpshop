ALTER TABLE `phpshop_modules_retailcrm_system` ADD `version` varchar (64) DEFAULT '3.2';

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
