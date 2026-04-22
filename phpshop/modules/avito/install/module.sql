DROP TABLE IF EXISTS `phpshop_modules_avito_system`;
CREATE TABLE `phpshop_modules_avito_system` (
  `id` int(11) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT '',
  `phone` varchar(64) DEFAULT NULL,
  `preview_description_template` text,
  `image_url` varchar(255) DEFAULT '',
  `latitude` varchar(255) DEFAULT '',
  `longitude` varchar(255) DEFAULT '',
  `client_id` varchar(255) DEFAULT '',
  `ñlient_secret` varchar(255) DEFAULT '',
  `create_products` enum('0','1') NOT NULL DEFAULT '0',
  `link` enum('0','1') NOT NULL DEFAULT '0',
  `log` enum('0','1') NOT NULL DEFAULT '0',
  `delivery_id` varchar(64) DEFAULT NULL,
  `export` enum('0','1','2') NOT NULL DEFAULT '0',
  `type` enum('1','2') NOT NULL DEFAULT '1',
  `status` int(11) NOT NULL DEFAULT '0',
  `status_import` varchar(64) DEFAULT '',
  `fee` int(11) NOT NULL,
  `fee_type` enum('1','2') NOT NULL DEFAULT '1',
  `price` int(11) NOT NULL,
  `transition` enum('0','1') NOT NULL DEFAULT '0',
  `map_url` varchar(255) DEFAULT '',
  `stop_words` text,
  `export_items_enabled` enum('0','1') NOT NULL DEFAULT '0',
  `export_items_limit` int(11) NOT NULL DEFAULT '0',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_avito_system` (`id`, `password`, `manager`, `address`, `phone`, `preview_description_template`, `image_url`, `latitude`, `longitude`, `client_id`, `ñlient_secret`, `version`, `create_products`, `link`, `log`, `delivery_id`, `export`, `type`, `status`, `status_import`, `fee`, `fee_type`, `price`,`transition`) VALUES
(1, '', '', '', '', '', '', '', '', '', '', '2.8', '1', '0', '1', '1', '1', '2', 2, 'confirming', 0, '2', 1,'0');

ALTER TABLE `phpshop_products` ADD `export_avito` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `name_avito` varchar(255) DEFAULT '';

/* 2.3 */
CREATE TABLE IF NOT EXISTS `phpshop_modules_avito_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` int(11) NOT NULL,
    `message` text CHARACTER SET utf8 NOT NULL,
    `order_id` varchar(64) NOT NULL DEFAULT '',
    `status` enum('1','2') NOT NULL DEFAULT '1',
    `path` varchar(64) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `avito_order_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_products` ADD `export_avito_id` varchar(64) NOT NULL default '';
ALTER TABLE `phpshop_products` ADD `price_avito` float DEFAULT '0';

/* 2.6 */
ALTER TABLE `phpshop_categories` ADD `category_avitoapi` varchar(255) NOT NULL;

CREATE TABLE IF NOT EXISTS `phpshop_modules_avitoapi_categories` (
`id` varchar(255) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` varchar(255) NOT NULL,
`slug` varchar(255) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_sort_categories` ADD `attribute_avitoapi` varchar(255) NOT NULL;