ALTER TABLE `phpshop_orders` ADD `bitrix24_deal_id` varchar(255) DEFAULT '';
ALTER TABLE `phpshop_modules_bitrix24_log` DROP `bitrix24_deal_id`;