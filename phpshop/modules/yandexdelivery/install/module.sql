DROP TABLE IF EXISTS `phpshop_modules_yandexdelivery_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexdelivery_system` (
    `id` int(11) NOT NULL auto_increment,
    `token` varchar(255) default '',
    `city` varchar(255) default 'Москва',
    `warehouse_id` varchar(255) default '',
    `status` int(11),
    `delivery_id` varchar(64) default '',
    `length` varchar(64) default '',
    `weight` varchar(64) default '',
    `width` varchar(64) default '',
    `height` varchar(64) default '',
    `declared_percent` float NOT NULL,
    `fee` int(11) default 0,
    `fee_type` enum('1','2') DEFAULT '1',
    `paid` enum('0','1') DEFAULT '0',
    `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_yandexdelivery_system` VALUES (1, '', '', '', 0, '', '', '', '', '', '30.0', 0, 1, 0, '1.2');

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexdelivery_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_code` varchar(64) default 'success',
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `yadelivery_order_data` text default '';
ALTER TABLE `phpshop_delivery` ADD `is_mod` enum('1','2') DEFAULT '1';