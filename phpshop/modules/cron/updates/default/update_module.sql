ALTER TABLE `phpshop_modules_cron_job` ADD `servers` varchar(255) default '';
ALTER TABLE `phpshop_modules_cron_system` ADD `version` varchar(64) default '1.5';
ALTER TABLE `phpshop_modules_cron_job` CHANGE `execute_day_num` `execute_day_num` INT(11) NOT NULL DEFAULT '0';
