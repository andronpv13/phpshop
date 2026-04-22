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