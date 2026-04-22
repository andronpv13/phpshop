DROP TABLE IF EXISTS `phpshop_modules_pochta_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_pochta_system` (
    `id` int(11) NOT NULL auto_increment,
    `token` varchar(255) DEFAULT '',
    `login` varchar(255) DEFAULT '',
    `password` varchar(255) DEFAULT '',
    `status` int(11),
    `declared_percent` float NOT NULL,
    `delivery_id` int(11) NOT NULL DEFAULT 0,
    `delivery_courier_id` int(11) NOT NULL DEFAULT 0,
    `mail_category` varchar(64) default 'ORDINARY',
    `mail_type` varchar(64) default 'PARCEL_CLASS_1',
    `dimension_type` varchar(64) default 'S',
    `index_from` varchar(64) default '',
    `weight` varchar(64) default '',
    `easy_return` enum('0','1') DEFAULT '0',
    `no_return` enum('0','1') DEFAULT '0',
    `fragile` enum('0','1') DEFAULT '0',
    `wo_mail_rank` enum('0','1') DEFAULT '0',
    `completeness_checking` enum('0','1') DEFAULT '0',
    `sms_notice` enum('0','1') DEFAULT '0',
    `electronic_notice` enum('0','1') DEFAULT '0',
    `order_of_notice` enum('0','1') DEFAULT '0',
    `simple_notice` enum('0','1') DEFAULT '0',
    `vsd` enum('0','1') DEFAULT '0',
    `widget_id` int(11) DEFAULT null,
    `courier_widget_id` int(11) DEFAULT null,
    `paid` enum('0','1') DEFAULT '0',
    `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_pochta_system` VALUES (1, '', '', '', 0, '30.0', 0, 0, 'ORDINARY', 'PARCEL_CLASS_1', 'S', '', '100', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', null, null,'0','1.4');

DROP TABLE IF EXISTS `phpshop_modules_pochta_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_pochta_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_uid` varchar(64) NULL,
  `status` varchar(255) NOT NULL,
  `method` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `pochta_order_status` varchar(64) default '';
ALTER TABLE `phpshop_orders` ADD `pochta_settings` text default '';