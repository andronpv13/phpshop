ALTER TABLE `phpshop_modules_vkseller_system` CHANGE `token` `token` text NULL;
ALTER TABLE `phpshop_modules_vkseller_system` ADD `device_id` varchar(255) default '';
ALTER TABLE `phpshop_modules_vkseller_system` ADD `refresh_token` text NULL;
ALTER TABLE `phpshop_modules_vkseller_system` ADD `token_time` int(11) NOT NULL,