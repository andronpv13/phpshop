ALTER TABLE `phpshop_modules_sitemappro_system` CHANGE `is_products_step` `step` VARCHAR(64) DEFAULT 'content';
ALTER TABLE `phpshop_modules_sitemappro_system` CHANGE `processed_products` `processed` int(11) NOT NULL default '0';
ALTER TABLE `phpshop_modules_sitemappro_system` ADD `use_filter_combinations` enum('0','1') NOT NULL default '0';

UPDATE `phpshop_modules_sitemappro_system` SET `step`='content' WHERE `id`='1';
UPDATE `phpshop_modules_sitemappro_system` SET `processed`='0' WHERE `id`='1';