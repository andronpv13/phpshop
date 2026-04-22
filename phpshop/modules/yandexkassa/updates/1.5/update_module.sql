ALTER TABLE `phpshop_products` ADD `yandex_vat_code` int(11) default 0;
ALTER TABLE `phpshop_modules_yandexkassa_system` ADD `payment_mode` ENUM('1','2') NOT NULL DEFAULT '1';
ALTER TABLE `phpshop_modules_yandexkassa_system` ADD `receipt_status`int(11) NOT NULL;