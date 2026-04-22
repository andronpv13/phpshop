/*659*/
CREATE TABLE IF NOT EXISTS `phpshop_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `date` int(11) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  `description` varchar(255) NOT NULL,
  `date_block` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

/*660*/
ALTER TABLE `phpshop_shopusers_status` ADD `cart_min` INT(11) NOT NULL DEFAULT '0';

/*661*/
ALTER TABLE `phpshop_order_status` ADD `bot_message` TEXT NOT NULL;
ALTER TABLE `phpshop_newsletter` ADD `bot_message` TEXT NOT NULL;

/*662*/
ALTER TABLE `phpshop_delivery` ADD `length_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `height_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `width_max` INT(11) DEFAULT '0';
ALTER TABLE `phpshop_delivery` ADD `sum_side_max` INT(11) DEFAULT '0';