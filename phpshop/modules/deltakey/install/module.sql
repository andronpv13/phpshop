

DROP TABLE IF EXISTS `phpshop_modules_deltakey_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_deltakey_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `merchant_id` varchar(64) NOT NULL default '',
  `merchant_key` varchar(64) NOT NULL default '',
  `merchant_skey` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_deltakey_system` VALUES (1,0,'Платежная система DeltaKey','Оплатите пожалуйста свой заказ','','','','','1.0');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10011, 'Оплата банковской картой (DeltaKey)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/visa.png');