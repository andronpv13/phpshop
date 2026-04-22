ALTER TABLE `phpshop_modules_productcomponents_system` ADD `logic` enum('0','1','2') default '0';

/* 1.3 */
ALTER TABLE `phpshop_products` ADD `productcomponents_sort` enum('0','1') default '0';