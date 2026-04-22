ALTER TABLE `phpshop_modules_wbseller_system` ADD `link` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_wbseller_system` ADD `status_import` varchar(64) default '';
ALTER TABLE `phpshop_modules_wbseller_system` ADD `delivery` INT(11) NOT NULL default '0';
ALTER TABLE `phpshop_modules_wbseller_system` ADD `create_products` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_modules_wbseller_system` CHANGE `token` `token` TEXT default '';
ALTER TABLE `phpshop_modules_wbseller_system` ADD `log` enum('0','1') NOT NULL default '0';
ALTER TABLE `phpshop_categories` ADD `category_wbseller_id` int(11) DEFAULT 0;
ALTER TABLE `phpshop_modules_wbseller_system` ADD `discount` enum('0','1') NOT NULL default '0';