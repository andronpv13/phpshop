ALTER TABLE `phpshop_modules_returncall_system` ADD `captcha_enabled` enum('1','2') NOT NULL default '1';
ALTER TABLE `phpshop_modules_returncall_jurnal` CHANGE `status` `status` INT(11) DEFAULT '1';
ALTER TABLE `phpshop_modules_returncall_system` ADD `status` int(11) default '0';