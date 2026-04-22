ALTER TABLE `phpshop_categories` ADD `sort_cache` blob;
ALTER TABLE `phpshop_categories` ADD `sort_cache_created_at` int(11);
ALTER TABLE  `phpshop_categories` ADD  `dop_cat` varchar(255) DEFAULT '';
ALTER TABLE  `phpshop_delivery` ADD  `payment` varchar(255) DEFAULT '';
CREATE TABLE `phpshop_modules_key` (
  `path` varchar(64) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `key` text NOT NULL,
  `verification` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE  `phpshop_users` ADD  `hash` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_delivery` ADD  `icon` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_payment_systems` ADD  `icon` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_comment` ADD  `rate`  SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_sort_categories` ADD  `brand` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_sort` ADD  `icon` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `phpshop_shopusers` ADD  `wishlist` BLOB NOT NULL;
ALTER TABLE  `phpshop_shopusers` ADD  `data_adres` BLOB NOT NULL;
ALTER TABLE  `phpshop_delivery` ADD  `city_select` ENUM(  '0',  '1',  '2' ) NOT NULL DEFAULT  '0', ADD  `data_fields` BLOB NOT NULL;
ALTER TABLE  `phpshop_payment_systems` ADD  `yur_data_flag` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_orders` 
ADD  `country` VARCHAR( 255 ) NOT NULL ,
ADD  `state` VARCHAR( 255 ) NOT NULL ,
ADD  `city` VARCHAR( 255 ) NOT NULL ,
ADD  `index` VARCHAR( 255 ) NOT NULL ,
ADD  `fio` VARCHAR( 255 ) NOT NULL ,
ADD  `tel` VARCHAR( 255 ) NOT NULL ,
ADD  `street` VARCHAR( 255 ) NOT NULL ,
ADD  `house` VARCHAR( 255 ) NOT NULL ,
ADD  `porch` VARCHAR( 255 ) NOT NULL ,
ADD  `door_phone` VARCHAR( 255 ) NOT NULL ,
ADD  `flat` VARCHAR( 255 ) NOT NULL ,
ADD  `org_name` VARCHAR( 255 ) NOT NULL ,
ADD  `org_inn` VARCHAR( 255 ) NOT NULL ,
ADD  `org_kpp` VARCHAR( 255 ) NOT NULL ,
ADD  `org_yur_adres` VARCHAR( 255 ) NOT NULL ,
ADD  `org_fakt_adres` VARCHAR( 255 ) NOT NULL ,
ADD  `org_ras` VARCHAR( 255 ) NOT NULL ,
ADD  `org_bank` VARCHAR( 255 ) NOT NULL ,
ADD  `org_kor` VARCHAR( 255 ) NOT NULL ,
ADD  `org_bik` VARCHAR( 255 ) NOT NULL ,
ADD  `org_city` VARCHAR( 255 ) NOT NULL;
ALTER TABLE  `phpshop_delivery` ADD  `num` SMALLINT( 3 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_delivery` CHANGE  `city_select`  `city_select` ENUM(  '0',  '1',  '2',  '3' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_delivery` CHANGE  `city_select`  `city_select` ENUM(  '0',  '1',  '2' ) CHARACTER SET cp1251 COLLATE cp1251_general_ci NOT NULL DEFAULT  '0';
ALTER TABLE  `phpshop_orders` ADD  `dop_info` TEXT NOT NULL;
ALTER TABLE  `phpshop_orders` ADD  `delivtime` VARCHAR( 255 ) NOT NULL AFTER  `flat`;
ALTER TABLE  `phpshop_sort_categories` DROP  `flag`;
CREATE TABLE `phpshop_slider` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '0',
  `num` smallint(6) NOT NULL,
  `link` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;


ALTER TABLE  `phpshop_products` ADD  `rate`  FLOAT( 1.1 ) UNSIGNED NOT NULL DEFAULT  '0.0';
ALTER TABLE  `phpshop_products` ADD  `rate_count` INT UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `phpshop_categories` ADD  `icon` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE  `phpshop_categories` ADD  `icon_description` VARCHAR( 255 ) NOT NULL ;

CREATE TABLE `phpshop_citylist_city` (
  `city_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(11) unsigned NOT NULL DEFAULT '0',
  `region_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`city_id`),
  KEY `country_id` (`country_id`),
  KEY `region_id` (`region_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=15789521 ;


CREATE TABLE `phpshop_citylist_country` (
  `country_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=7716094 ;


CREATE TABLE `phpshop_citylist_region` (
  `region_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL DEFAULT '0',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`region_id`),
  KEY `country_id` (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


ALTER TABLE  `phpshop_shopusers_status` ADD  `cumulative_discount_check` INT NOT NULL AFTER  `enabled` ,
ADD  `cumulative_discount` BLOB NOT NULL AFTER  `cumulative_discount_check` ;

ALTER TABLE  `phpshop_shopusers` ADD  `cumulative_discount` INT NOT NULL AFTER  `data_adres` ;
ALTER TABLE  `phpshop_order_status` ADD  `cumulative_action` ENUM(  '0',  '1' ) NOT NULL AFTER  `sklad_action` ;
ALTER TABLE  `phpshop_orders` ADD  `sum` FLOAT ;

ALTER TABLE  `phpshop_sort_categories` ADD  `product` enum('0','1') NOT NULL DEFAULT '0' ;

CREATE TABLE IF NOT EXISTS `phpshop_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `template` int(11) DEFAULT '0',
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


CREATE TABLE `phpshop_photo_categories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `parent_to` int(11) default '0',
  `link` varchar(64) default '',
  `name` varchar(64) default '',
  `num` tinyint(11) default '0',
  `content` text,
  `enabled` enum('0','1') default '0',
  `page` varchar(255)  default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



CREATE TABLE `phpshop_photo` (
  `id` int(11) NOT NULL auto_increment,
  `category` int(11) default '0',
  `enabled` enum('0','1') default '0',
  `name` varchar(64) default '',
  `num` tinyint(11)  default '0',
  `info` varchar(255) default '',
  PRIMARY KEY  (`id`),
  KEY `parent` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_templates_key` (
  `path` varchar(64) NOT NULL DEFAULT '',
  `date` int(11) DEFAULT '0',
  `key` text,
  `verification` varchar(32) DEFAULT '',
  PRIMARY KEY (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_parent_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_categories` ADD `parent_title` int(11) DEFAULT '0';
ALTER TABLE `phpshop_order_status` ADD `mail_action` ENUM( "0", "1" ) DEFAULT '1';
ALTER TABLE `phpshop_servers` ADD `tel` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `company` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `adres` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `logo` varchar(255) default '';
ALTER TABLE `phpshop_servers` ADD `adminmail` varchar(255) default '';
ALTER TABLE `phpshop_order_status` ADD `mail_message` text default '';
ALTER TABLE `phpshop_news` ADD `odnotip` text;
ALTER TABLE `phpshop_servers` ADD `admoption` blob;
ALTER TABLE `phpshop_news` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_page_categories` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_delivery` ADD `sum_max` float DEFAULT '0';
ALTER TABLE `phpshop_modules` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_delivery` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_orders` ADD `tracking` varchar(64) default '';
ALTER TABLE `phpshop_rssgraber` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_baners` ADD `dop_cat` varchar(255) default '';
ALTER TABLE `phpshop_baners` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_payment_systems` ADD `color` varchar(64) default '#000000';
ALTER TABLE `phpshop_system` CHANGE `width_icon` `icon` varchar(255) default '';
ALTER TABLE `phpshop_sort_categories` ADD `virtual` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_categories` ADD `menu` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_page_categories` ADD `menu` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_order_status` ADD `sms_action` ENUM('0', '1') DEFAULT '0';
ALTER TABLE `phpshop_search_base` ADD `link` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_sort` ADD `description` text;
ALTER TABLE `phpshop_sort` ADD `title` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_news` ADD `icon`  varchar(255) DEFAULT '';

CREATE TABLE IF NOT EXISTS `phpshop_warehouses` (
  `id` int(11) AUTO_INCREMENT,
  `name` varchar(64) ,
  `description` varchar(255) ,
  `uid` varchar(64),
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11) ,
  `servers` varchar(64) default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_servers` ADD `warehouse` int(11) DEFAULT '0';
ALTER TABLE `phpshop_page` ADD `icon`  varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page` ADD `preview` text;
ALTER TABLE `phpshop_servers` ADD `price` enum('1','2','3','4','5') DEFAULT '1';
ALTER TABLE `phpshop_delivery` ADD `warehouse` int(11) DEFAULT '0';
ALTER TABLE `phpshop_sort_categories` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_baners` ADD `skin` varchar(64) default '';
ALTER TABLE `phpshop_page` ADD `footer` enum('0','1') DEFAULT '1';

CREATE TABLE IF NOT EXISTS `phpshop_promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') DEFAULT '0',
  `description` text NOT NULL,
  `label` text NOT NULL,
  `active_check` enum('0','1') NOT NULL,
  `active_date_ot` varchar(255) NOT NULL,
  `active_date_do` varchar(255) NOT NULL,
  `discount_check` enum('0','1') NOT NULL,
  `discount_tip` enum('0','1') NOT NULL,
  `discount` int(11) NOT NULL,
  `free_delivery` enum('0','1') NOT NULL,
  `categories_check` enum('0','1') NOT NULL,
  `categories` text NOT NULL,
  `status_check` enum('0','1') NOT NULL DEFAULT '0',
  `statuses` text NOT NULL DEFAULT '',
  `products_check` enum('0','1') NOT NULL,
  `products` text NOT NULL,
  `sum_order_check` enum('0','1') NOT NULL,
  `sum_order` int(11) NOT NULL,
  `delivery_method_check` enum('0','1') NOT NULL,
  `delivery_method` int(11) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `block_old_price` enum('0','1') DEFAULT '0',
  `hide_old_price` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_gbook` ADD `servers` varchar(64) default '';

CREATE TABLE `phpshop_push` (
  `token` text,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_discount` ADD `action` ENUM('1', '2') DEFAULT '1';
ALTER TABLE `phpshop_orders` ADD `admin` int(11) default 0;
ALTER TABLE `phpshop_parent_name` ADD `color` VARCHAR(255);
ALTER TABLE `phpshop_orders` ADD `servers` int(11) default 0;
ALTER TABLE `phpshop_servers` ADD `admin` int(11) default 0;

ALTER TABLE `phpshop_promotions` CHANGE `free_delivery` `num_check` int(11) DEFAULT '0';
ALTER TABLE `phpshop_payment_systems` ADD `servers` varchar(64) default '';
ALTER TABLE `phpshop_shopusers` ADD `servers` int(11) default 0;
ALTER TABLE `phpshop_shopusers` DROP INDEX `login`;
ALTER TABLE `phpshop_orders` ADD `paid` TINYINT(1) DEFAULT NULL;

CREATE TABLE `phpshop_bonus` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `comment` varchar(255) default '',
  `user_id` int(11),
  `order_id` int(11),
  `bonus_operation` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_shopusers` ADD `bonus` int(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `bonus_minus` int(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `bonus_plus` int(11) DEFAULT '0';

ALTER TABLE `phpshop_categories`  ADD `tile` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_sort_categories`  ADD `show_preview` ENUM('0','1') DEFAULT '0';
update `phpshop_categories`  set `tile`='1' where parent_to='0';

ALTER TABLE `phpshop_delivery` ADD `comment` TEXT;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `option` blob,
  `type` varchar(64),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `length` varchar(64) default '';
ALTER TABLE `phpshop_products` ADD `width` varchar(64) default '';
ALTER TABLE `phpshop_products` ADD `height` varchar(64) default '';

ALTER TABLE `phpshop_page_categories` ADD `icon` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `title` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `description` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `keywords` text;
ALTER TABLE `phpshop_photo_categories` ADD `count` tinyint(11) default 2;

ALTER TABLE `phpshop_users` ADD `token` VARCHAR(64);
ALTER TABLE `phpshop_slider` ADD `mobile` enum('0','1') default '0';
ALTER TABLE `phpshop_search_jurnal` ADD `ip` VARCHAR(64);

ALTER TABLE `phpshop_order_status` ADD `num` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `date` INT(11) DEFAULT '0';

CREATE TABLE `phpshop_notes` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `message` text ,
  `status` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_promotions` ADD `action` ENUM('1', '2') DEFAULT '1';
ALTER TABLE `phpshop_shopusers` ADD `token` INT(11);
ALTER TABLE `phpshop_shopusers` ADD `token_time` INT(11);
ALTER TABLE `phpshop_servers` ADD `icon` VARCHAR(255);
ALTER TABLE `phpshop_notes` ADD `name` VARCHAR(64), ADD `tel` VARCHAR(64), ADD `mail` VARCHAR(64), ADD `content` TEXT;

ALTER TABLE `phpshop_payment_systems` ADD `company` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_orders` ADD `company` INT(11) DEFAULT '0';
CREATE TABLE `phpshop_company` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bank` blob,
  `enabled` enum('0','1') DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
ALTER TABLE `phpshop_servers` ADD `company_id` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_discount` ADD `block_old_price` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_discount` ADD `block_categories` text DEFAULT '';
ALTER TABLE `phpshop_sort` ADD `meta_description` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_baners` CHANGE `count_all` `type` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_baners` CHANGE `count_today` `display` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_baners` CHANGE `limit_all` `size` ENUM('0','1','2') DEFAULT '0';
ALTER TABLE `phpshop_shopusers` ADD `bot` VARCHAR(64) DEFAULT '';
UPDATE `phpshop_shopusers` SET `bot` = MD5(CONCAT(`id`,`login`));
ALTER TABLE `phpshop_order_status` ADD `bot_action` ENUM('0','1') DEFAULT '0';

CREATE TABLE `phpshop_dialog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `chat_id` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `bot` varchar(64) NOT NULL,
  `staffid` enum('0','1') DEFAULT '1',
  `isview` enum('0','1') DEFAULT '1',
  `order_id` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_system` ADD `sort_title_shablon` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_system` ADD `sort_description_shablon` varchar(255) DEFAULT '';

ALTER TABLE `phpshop_dialog` ADD `attachments` VARCHAR(255);
ALTER TABLE `phpshop_delivery` ADD `weight_max` int(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `weight_min` int(11) DEFAULT '0';
ALTER TABLE `phpshop_dialog` ADD `isview_user` enum('0','1') DEFAULT '1';

CREATE TABLE `phpshop_dialog_answer` (
  `id` int(11) NOT NULL,
  `name` varchar(64),
  `message` text,
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11),
  `servers` varchar(255),
  `view` enum('1','2') DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `price_purch` FLOAT DEFAULT '0';
ALTER TABLE `phpshop_shopusers` ADD `dialog_ban` ENUM('0','1') DEFAULT '0';

/*620*/
ALTER TABLE `phpshop_promotions` ADD `disable_categories` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_baners` ADD `image` VARCHAR(255), ADD `description` TEXT;
ALTER TABLE `phpshop_baners` CHANGE `type` `type` ENUM('0','1','2') DEFAULT '0';
ALTER TABLE `phpshop_baners` ADD `link` VARCHAR(255);
ALTER TABLE `phpshop_baners` ADD `mobile` ENUM('0','1') DEFAULT '0';
ALTER TABLE `phpshop_slider` ADD `name` VARCHAR(255);
ALTER TABLE `phpshop_slider` ADD `link_text` VARCHAR(255);
ALTER TABLE `phpshop_shopusers_status` ADD `warehouse` enum('0','1') DEFAULT '1';

/*621*/
ALTER TABLE `phpshop_delivery` ADD `categories_check` ENUM('0','1') DEFAULT '0', ADD `categories` VARCHAR(255);

/*622*/
DROP TABLE IF EXISTS `phpshop_exchanges_log`;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `option` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

/*623*/
ALTER TABLE `phpshop_newsletter` ADD `servers` INT(11) DEFAULT '0';

/*625*/
ALTER TABLE `phpshop_baners` CHANGE `type` `type` ENUM('0','1','2','3') DEFAULT '0';

/*627*/
ALTER TABLE `phpshop_categories` ADD `length` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `width` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `height` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `weight` float DEFAULT '0';
ALTER TABLE `phpshop_categories` ADD `ed_izm` varchar(64) DEFAULT '';

ALTER TABLE `phpshop_slider` ADD `color` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_baners` ADD `color` varchar(64) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `color` varchar(64) DEFAULT '';

ALTER TABLE `phpshop_jurnal` CHANGE `ip` `ip` VARCHAR(64) DEFAULT '';

ALTER TABLE `phpshop_system` CHANGE `num_vitrina` `num_vitrina` ENUM('1','2','3','4','5','6') DEFAULT '3';
ALTER TABLE `phpshop_categories` CHANGE `num_row` `num_row` ENUM('1','2','3','4','5','6') DEFAULT '3';

/*634*/
ALTER TABLE `phpshop_payment_systems` ADD `status` INT(11) DEFAULT '0';

/*636*/
ALTER TABLE `phpshop_products` ADD `external_code` varchar(64) DEFAULT '';

/*637*/
ALTER TABLE `phpshop_products` ADD INDEX(`external_code`);

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