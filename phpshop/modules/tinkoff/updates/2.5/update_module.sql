ALTER TABLE `phpshop_modules_tinkoff_system` ADD `status_receipt` int(11) NOT NULL;
ALTER TABLE `phpshop_modules_tinkoff_system` ADD `payment_mode` ENUM('1','2') NOT NULL DEFAULT '1';