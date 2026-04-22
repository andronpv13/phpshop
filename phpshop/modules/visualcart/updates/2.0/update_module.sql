ALTER TABLE `phpshop_modules_visualcart_system` ADD `referal` enum('0','1') default '0';

DROP TABLE IF EXISTS `phpshop_modules_visualcart_log`;
CREATE TABLE `phpshop_modules_visualcart_log` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `user` varchar(255) default '',
  `ip` varchar(64) default '',
  `status` enum('1','2') DEFAULT '1',
  `content` varchar(64) default '',
  `num` TINYINT(11) default '0',
  `product_id` INT(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_modules_visualcart_memory` ADD `tel` VARCHAR(64), ADD `mail` VARCHAR(64), ADD `name` VARCHAR(64), `sum` FLOAT ;
ALTER TABLE `phpshop_modules_visualcart_memory` ADD `sendmail` ENUM('0','1') DEFAULT '0', ADD `server` INT(11);
ALTER TABLE `phpshop_modules_visualcart_system` ADD `sendmail` INT(11) DEFAULT '10';
ALTER TABLE `phpshop_modules_visualcart_system` ADD `day` INT(11) DEFAULT 10;
