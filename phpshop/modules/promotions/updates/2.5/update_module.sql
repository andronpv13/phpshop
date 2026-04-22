ALTER TABLE `phpshop_modules_promotions_forms` ADD `statuses` text NOT NULL DEFAULT '';
ALTER TABLE `phpshop_modules_promotions_forms` ADD `status_check` enum('0','1') NOT NULL DEFAULT '0';
ALTER TABLE `phpshop_modules_promotions_forms` ADD `hide_old_price` enum('0','1') DEFAULT '0';