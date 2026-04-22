ALTER TABLE `phpshop_modules_avito_system` ADD `latitude` varchar(255) default '';
ALTER TABLE `phpshop_modules_avito_system` ADD `longitude` varchar(255) default '';

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
ALTER TABLE `phpshop_modules_avito_system` ADD `create_products` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `link` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `log` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `delivery_id` varchar(64) DEFAULT NULL;
ALTER TABLE `phpshop_modules_avito_system` ADD `export` enum('0','1','2') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `type` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_avito_system` ADD `status` int(11) NOT NULL default '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `status_import` varchar(64) default '';
ALTER TABLE `phpshop_modules_avito_system` ADD `fee` int(11) NOT NULL;
ALTER TABLE `phpshop_modules_avito_system` ADD `fee_type` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_avito_system` ADD `price` int(11) NOT NULL;
ALTER TABLE `phpshop_modules_avito_system` ADD `client_id` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_modules_avito_system` ADD `ñlient_secret` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_modules_avito_system` ADD `transition` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_products` ADD `export_avito_id` varchar(64) NOT NULL default '';
ALTER TABLE `phpshop_products` ADD `price_avito` float DEFAULT '0';

/* 2.5 */
ALTER TABLE `phpshop_modules_avito_system` ADD `map_url` varchar(255) DEFAULT '';

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

/* 2.7 */
ALTER TABLE `phpshop_modules_avito_system` ADD `stop_words` text;

/* 2.8 */
ALTER TABLE `phpshop_modules_avito_system` ADD `export_items_enabled` enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `export_items_limit` int(11) NOT NULL DEFAULT '0';