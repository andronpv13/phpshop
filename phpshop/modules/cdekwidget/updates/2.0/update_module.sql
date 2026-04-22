ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `paid` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `webhook` enum('0','1') DEFAULT '0';
ALTER TABLE `phpshop_modules_cdekwidget_system` ADD `cost` int(11) default 0;
ALTER TABLE `phpshop_delivery` ADD `cdek_paid` enum('0','1') DEFAULT '0';