DROP TABLE IF EXISTS `phpshop_modules_novaposhta_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novaposhta_system` (
  `id` int(11) NOT NULL auto_increment,
  `api_key` varchar(64) default '',
  `delivery_id` int(11) NOT NULL default 0,
  `status` int(11),
  `weight` varchar(64) default '',
  `google_api` varchar(255) default '',
  `default_city` varchar(255) default '',
  `city_sender` varchar(255) default '',
  `sender` varchar(255) default '',
  `sender_address` varchar(255) default '',
  `sender_contact` varchar(255) default '',
  `phone` varchar(255) default '',
  `last_cities_update` int(11) NOT NULL default '0',
  `last_whtypes_update` int(11) NOT NULL default '0',
  `last_warehouses_update` int(11) NOT NULL default '0',
  `pvz_ref` varchar(255) default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_novaposhta_system` VALUES (1,'', 0, 0, '', '', '', '', '', '', '', '', 0, 0, 0, '', '1.0');

DROP TABLE IF EXISTS `phpshop_modules_novaposhta_cities`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novaposhta_cities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `area_description` varchar(255) NOT NULL,
  `area_description_ru` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_novaposhta_wh_types`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novaposhta_wh_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `ref` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_novaposhta_warehouses`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novaposhta_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `ref` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(64) NULL,
  `type` varchar(255) NOT NULL,
  `number` varchar(64) NOT NULL,
  `city` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `latitude` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_novaposhta_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novaposhta_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NULL,
  `status` varchar(255) NOT NULL,
  `model` varchar(64) NOT NULL,
  `method` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `np_order_data` varchar(255) default '';
ALTER TABLE `phpshop_delivery` ADD `is_mod` enum('1','2') DEFAULT '1';