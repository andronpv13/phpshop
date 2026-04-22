/* 2.7 */
ALTER TABLE `phpshop_modules_avito_system` ADD `stop_words` text;

/* 2.8 */
ALTER TABLE `phpshop_modules_avito_system` ADD `export_items_enabled` enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_modules_avito_system` ADD `export_items_limit` int(11) NOT NULL DEFAULT '0';