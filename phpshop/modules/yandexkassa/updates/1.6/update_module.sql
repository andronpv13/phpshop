ALTER TABLE `phpshop_modules_yandexkassa_system` ADD `payment_mode` ENUM('1','2') NOT NULL DEFAULT '1';
ALTER TABLE `phpshop_modules_yandexkassa_system` ADD `receipt_status`int(11) NOT NULL;