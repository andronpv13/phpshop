ALTER TABLE `phpshop_modules_lock_system` ADD `version` varchar(64) default '1.1';
ALTER TABLE `phpshop_modules_lock_system` ADD `flag_admin` enum('1','2') default '1';