ALTER TABLE `phpshop_products` ADD `google_merchant` enum('0','1') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `aliexpress` enum('0','1') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `sbermarket` enum('0','1') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `cdek` enum('0','1') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `price_google` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_cdek` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_aliexpress` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_sbermarket` float DEFAULT '0';

CREATE TABLE `phpshop_modules_marketplaces_system` (
  `id` int(11) NOT NULL auto_increment,
  `password` varchar(64),
  `use_params` enum('0','1') DEFAULT '0',
  `description_template` varchar(255),
  `version` varchar(64) default '1.0',
  `options` BLOB,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_marketplaces_system` VALUES (1,'', 0, '', '1.3', '');
