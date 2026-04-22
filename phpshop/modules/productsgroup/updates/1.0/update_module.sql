ALTER TABLE `phpshop_products` CHANGE `productsgroup_products` `productsgroup_products` BLOB NOT NULL;
ALTER TABLE `phpshop_products` ADD `productsgroup_products_keys` text NOT NULL;