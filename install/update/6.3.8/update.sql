/*639*/
UPDATE `phpshop_system` SET `kurs_beznal` = '0';
ALTER TABLE `phpshop_system` CHANGE `kurs_beznal` `shop_type` ENUM('0','1','2') NULL DEFAULT '0';
ALTER TABLE `phpshop_servers` ADD `shop_type` ENUM('0','1','2') NULL DEFAULT '0';

/*643*/
ALTER TABLE `phpshop_payment_systems` ADD `sum_max` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `sum_min` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `discount_max` float DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `discount_min` float DEFAULT '0';

/*646*/
ALTER TABLE `phpshop_products` ADD `type` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_menu` ADD `dop_cat` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_menu` ADD `mobile` enum('0','1') DEFAULT '0';

/*649*/
ALTER TABLE `phpshop_categories` ADD `podcatalog_view` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_system` ADD `ai` BLOB NOT NULL;
ALTER TABLE `phpshop_search_base` ADD `url` VARCHAR(255) NOT NULL;
ALTER TABLE `phpshop_order_status` ADD `external_code` VARCHAR(64) NOT NULL;
ALTER TABLE `phpshop_delivery` ADD `external_code` VARCHAR(64) NOT NULL;
ALTER TABLE `phpshop_dialog` ADD `ai` ENUM('0','1') NOT NULL DEFAULT '0';

/*650*/
ALTER TABLE `phpshop_products` ADD INDEX(`spec`);
ALTER TABLE `phpshop_products` ADD INDEX(`newtip`);
ALTER TABLE `phpshop_products` ADD INDEX(`yml`);
ALTER TABLE `phpshop_products` ADD INDEX(`parent_enabled`);
ALTER TABLE `phpshop_products` ADD INDEX(`sklad`);
ALTER TABLE `phpshop_products` ADD INDEX(`dop_cat`);
ALTER TABLE `phpshop_categories` ADD INDEX(`vid`);
ALTER TABLE `phpshop_categories` ADD INDEX(`skin_enabled`);
ALTER TABLE `phpshop_categories` ADD INDEX(`menu`);
ALTER TABLE `phpshop_categories` ADD INDEX(`tile`);
ALTER TABLE `phpshop_categories` ADD INDEX(`podcatalog_view`);
ALTER TABLE `phpshop_categories` ADD INDEX(`dop_cat`);

/*652*/
ALTER TABLE `phpshop_products` ADD `import_id` VARCHAR(64) NOT NULL DEFAULT '';
ALTER TABLE `phpshop_exchanges_log` ADD `import_id` VARCHAR(64) NOT NULL DEFAULT '';

/*657*/
ALTER TABLE `phpshop_newsletter` ADD `recipients` text NULL;

/*658*/
CREATE TABLE IF NOT EXISTS `phpshop_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

/*659*/
CREATE TABLE IF NOT EXISTS `phpshop_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `date` int(11) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  `description` varchar(255) NOT NULL,
  `date_block` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

/*660*/
ALTER TABLE `phpshop_shopusers_status` ADD `cart_min` INT(11) NOT NULL DEFAULT '0';

/*661*/
ALTER TABLE `phpshop_order_status` ADD `bot_message` TEXT NOT NULL;
ALTER TABLE `phpshop_newsletter` ADD `bot_message` TEXT NOT NULL;

/*662*/
ALTER TABLE `phpshop_delivery` ADD `length_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `height_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `width_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `sum_side_max` INT(11) DEFAULT '0';