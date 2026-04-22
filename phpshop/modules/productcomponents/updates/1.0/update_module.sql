ALTER TABLE `phpshop_modules_productcomponents_system` ADD `product_search` enum('0','1') default '1';
ALTER TABLE `phpshop_products` ADD `productcomponents_markup` int(11) NOT NULL;

/* 1.3 */
ALTER TABLE `phpshop_products` ADD `productcomponents_sort` enum('0','1') default '0';