
DROP TABLE IF EXISTS `phpshop_modules_pozvonim_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_pozvonim_system` (
        `id` int(11) NOT NULL auto_increment,
	`appId` INT(11) NOT NULL DEFAULT '0',
	`token` VARCHAR(64) NOT NULL DEFAULT '',
	`email` VARCHAR(64) NOT NULL DEFAULT '',
	`phone` VARCHAR(64) NOT NULL DEFAULT '',
	`host` VARCHAR(64) NOT NULL DEFAULT '',
	`code` TEXT NOT NULL,
	`key` VARCHAR(64) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_pozvonim_system` VALUES (1, 0, '', '', '', '', '', '');

