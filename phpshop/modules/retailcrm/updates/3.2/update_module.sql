ALTER TABLE `phpshop_orders` ADD `retail_status` enum('1','2') DEFAULT '1';
ALTER TABLE `phpshop_products` ADD `retail_product_id` varchar(64) default '';