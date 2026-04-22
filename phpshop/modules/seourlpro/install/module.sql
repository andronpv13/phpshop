ALTER TABLE `phpshop_categories` ADD `cat_seo_name` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `prod_seo_name` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_sort` ADD `sort_seo_name` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_news` ADD `news_seo_name` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_page_categories` ADD `page_cat_seo_name` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_products` ADD `prod_seo_name_old` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `cat_seo_name_old` VARCHAR(255) DEFAULT '';

DROP TABLE IF EXISTS `phpshop_modules_seourlpro_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_seourlpro_system` (
  `id` int(11)  auto_increment,
  `paginator` enum('1','2') default '1',
  `seo_brands_enabled` enum('1','2') default '1',
  `cat_content_enabled` enum('1','2') default '1',
  `seo_news_enabled` enum('1','2') default '1',
  `seo_page_enabled` enum('1','2') default '1',
  `redirect_enabled` enum('1','2') default '1',
  `html_enabled` enum('1','2') default '1',
  `version` VARCHAR(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_seourlpro_system` VALUES (1,'2','2','1','2','2','1','1','2.3');