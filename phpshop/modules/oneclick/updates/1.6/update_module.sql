ALTER TABLE `phpshop_modules_oneclick_system` CHANGE `only_available` `only_available` ENUM('0','1','2') DEFAULT '0';
ALTER TABLE `phpshop_modules_oneclick_jurnal` ADD `mail` varchar(64) default '';