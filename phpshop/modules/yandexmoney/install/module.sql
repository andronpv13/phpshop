

DROP TABLE IF EXISTS `phpshop_modules_yandexmoney_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexmoney_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11),
  `title` text,
  `title_end` text,
  `merchant_id` varchar(64) default '',
  `merchant_sig` varchar(64) default '',
  `serial` varchar(64) default '',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_yandexmoney_system` VALUES (1,0,'Платежная система ЮMoney','Оплатите пожалуйста свой заказ','','','','1.2');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10002, 'ЮMoney', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/yandex-money.png');