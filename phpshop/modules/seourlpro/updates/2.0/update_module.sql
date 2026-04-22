ALTER TABLE `phpshop_products` ADD `prod_seo_name_old` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_categories` ADD `cat_seo_name_old` VARCHAR(255) DEFAULT '';
ALTER TABLE `phpshop_modules_seourlpro_system` ADD  `redirect_enabled` enum('1','2') default '1';
ALTER TABLE `phpshop_modules_seourlpro_system` ADD  `html_enabled` enum('1','2') default '1';