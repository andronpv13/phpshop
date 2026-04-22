ALTER TABLE `phpshop_modules_oneclick_system` ADD `write_order` enum('0','1') default '0';
ALTER TABLE `phpshop_modules_oneclick_system` ADD `captcha` enum('0','1') default '0';
ALTER TABLE `phpshop_modules_oneclick_system` ADD `status` int(11) default '0';
ALTER TABLE `phpshop_modules_oneclick_jurnal` ADD `mail` varchar(64) default '';