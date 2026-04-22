DROP TABLE IF EXISTS `phpshop_modules_yandexkassa_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexkassa_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `shop_id` varchar(64) NOT NULL default '',
  `api_key` varchar(255) NOT NULL default '',
  `payment_mode` ENUM('1','2') NOT NULL DEFAULT '1',
  `receipt_status`int(11) NOT NULL,
  `version` varchar(64) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_yandexkassa_system` (`id`, `status`, `title`, `title_end`, `shop_id`, `api_key`,`payment_mode`, `version`) VALUES
(1, 0, 'Оплатить сейчас', 'Оплатите пожалуйста свой заказ', '665601', 'test_IBkYJDzgL1-gaz04YTHNxQekxtaGz6z-7_40u0rRlYs', '1', 1.9);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10004, 'ЮKassa', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/yookassa.png');

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexkassa_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `yandex_id` varchar(255) NULL,
  `status_code` varchar(255) NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_products` ADD `yandex_vat_code` int(11) default 0;
