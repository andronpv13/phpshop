ALTER TABLE `phpshop_delivery` ADD `yandex_enabled` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `yandex_day` int(11) DEFAULT '2';
ALTER TABLE `phpshop_delivery` ADD `yandex_type` enum('1','2','3') DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `yandex_payment` enum('1','2','3') DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `yandex_outlet` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_delivery` ADD `yandex_check` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `manufacturer_warranty` enum('1','2') DEFAULT '2';
ALTER TABLE `phpshop_products` ADD `country_of_origin` varchar(50) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `adult` enum('1','2') DEFAULT '2';
ALTER TABLE `phpshop_products` ADD `delivery` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `pickup` enum('1','2') DEFAULT '2';
ALTER TABLE `phpshop_sort_categories` ADD `yandex_param` enum('1','2') DEFAULT '2';
ALTER TABLE `phpshop_sort_categories` ADD `yandex_param_unit` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_delivery` ADD `yandex_delivery_points` text;
ALTER TABLE `phpshop_delivery` ADD `yandex_region_id` int(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `yandex_day_min` int(11) DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `yandex_order_before` int(11) DEFAULT '16';
ALTER TABLE `phpshop_products` ADD `yandex_min_quantity` int(11) DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `yandex_step_quantity` int(11) DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `vendor_code` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `vendor_name` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `manufacturer` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `yandex_condition` enum('1','2','3','4') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `yandex_condition_reason` text;
ALTER TABLE `phpshop_products` ADD `yandex_quality` enum('1','2','3','4') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `barcode` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `market_sku` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `yandex_link` varchar(255) DEFAULT '';

CREATE TABLE `phpshop_modules_yandexcart_system` (
  `id` int(11) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `auth_token` varchar(64) DEFAULT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_token` varchar(255) DEFAULT NULL,
  `campaign_id` varchar(64) DEFAULT NULL,
  `description_template` varchar(255) DEFAULT NULL,
  `delivery_id` varchar(64) DEFAULT NULL,
  `model` varchar(64) DEFAULT NULL,
  `import_from` int(11) DEFAULT '0',
  `use_params` enum('0','1') DEFAULT '0',
  `version` varchar(64) DEFAULT '1.0',
  `options` blob,
  `stop` enum('0','1') DEFAULT '0',
  `type` enum('1','2') NOT NULL DEFAULT '1',
  `link` enum('0','1') NOT NULL DEFAULT '0',
  `export` enum('0','1','2') NOT NULL DEFAULT '0',
  `log` enum('0','1') NOT NULL DEFAULT '1',
  `campaign_id_2` varchar(64) DEFAULT '',
  `model_2` varchar(64) DEFAULT '',
  `campaign_id_3` varchar(64) DEFAULT '',
  `model_3` varchar(64) DEFAULT '',
  `warehouse` int(11) DEFAULT '0',
  `warehouse_2` int(11) DEFAULT '0',
  `warehouse_3` int(11) DEFAULT '0',
  `businesses_id` varchar(64) DEFAULT '',
  `auth_token_2` varchar(64) DEFAULT NULL,
  `create_products` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_yandexcart_system` VALUES(1, '', '', '', '', '', '', '0', '', 0, '', '4.2', '', '0', '1', '0', '0', '0', '', '', '', '', 0, 0, 0, '', NULL, '0');


CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexcart_log` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` int(11) NOT NULL,
    `message` text CHARACTER SET utf8 NOT NULL,
    `order_id` varchar(64) NOT NULL DEFAULT '',
    `status` enum('1','2') NOT NULL DEFAULT '1',
    `path` varchar(64) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_orders` ADD `yandex_order_id` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_products` ADD `yandex_service_life_days` VARCHAR(64) DEFAULT '';

ALTER TABLE `phpshop_products` ADD `yml_2` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `yml_3` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_yandex` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_yandex_2` float DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `price_yandex_3` float DEFAULT '0';

ALTER TABLE `phpshop_orders` ADD `yandex_order_id_2` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_orders` ADD `yandex_order_id_3` varchar(255) NOT NULL DEFAULT '';

ALTER TABLE `phpshop_delivery` ADD `yandex_delivery_points_2` text;
ALTER TABLE `phpshop_delivery` ADD `yandex_region_id_2` int(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `yandex_delivery_points_3` text;
ALTER TABLE `phpshop_delivery` ADD `yandex_region_id_3` int(11) DEFAULT '0';

ALTER TABLE `phpshop_orders` ADD INDEX(`yandex_order_id`); 

ALTER TABLE `phpshop_categories` ADD `category_yandexcart` int(11) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexcart_categories` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
