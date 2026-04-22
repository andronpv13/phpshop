ALTER TABLE `phpshop_modules_yandexcart_system` ADD `stop` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_products` ADD `yandex_service_life_days` VARCHAR(64) DEFAULT '';
ALTER TABLE `phpshop_products` CHANGE `yandex_condition` `yandex_condition` ENUM('1','2','3','4') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `yandex_quality` enum('1','2','3','4') DEFAULT '1';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `type` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `link` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_products` ADD `yandex_link` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `export` enum('0','1','2') NOT NULL default '0';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `log` enum('0','1') NOT NULL default '0';

# 4.0
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `campaign_id_2` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `model_2` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `campaign_id_3` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `model_3` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `warehouse` int(11) DEFAULT '0';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `warehouse_2` int(11) DEFAULT '0';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `warehouse_3` int(11) DEFAULT '0';
ALTER TABLE `phpshop_modules_yandexcart_system` ADD `businesses_id` varchar(64) DEFAULT '';

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

ALTER TABLE `phpshop_modules_yandexcart_system` ADD `auth_token_2` varchar(64);
ALTER TABLE `phpshop_modules_yandexcart_system` ADD  `create_products` enum('0','1') NOT NULL default '0';

# 4.1
ALTER TABLE `phpshop_categories` ADD `category_yandexcart` int(11) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexcart_categories` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
