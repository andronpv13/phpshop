DROP TABLE IF EXISTS `phpshop_modules_dolyame_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_dolyame_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `status` int(11) NOT NULL,
  `status_payment` int(11) NOT NULL,
  `max_sum` int(11) NOT NULL,
  `site_id` varchar(64) NOT NULL,
  `version` varchar(64) default '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_dolyame_system` VALUES (1, '', '',0,101,30000,'','1.0');

CREATE TABLE IF NOT EXISTS `phpshop_modules_dolyame_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10025, 'Долями', 'modules', '0', 0, 'Ваш заказ оплачен', 'Спасибо', '', '/UserFiles/Image/Payments/dolyame.png');

ALTER TABLE `phpshop_products` ADD `dolyame_enabled` enum('0','1') DEFAULT '0';