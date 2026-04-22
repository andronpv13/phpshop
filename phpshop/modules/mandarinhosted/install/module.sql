DROP TABLE IF EXISTS `phpshop_modules_mandarinhosted_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_mandarinhosted_system` (
  `id` int(11) NOT NULL auto_increment,
  `merchant_key` varchar(64) NOT NULL default '',
  `merchant_skey` varchar(64) NOT NULL default '',
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_sub` text NOT NULL,
  `version` FLOAT(2) DEFAULT '1.1' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_mandarinhosted_system` VALUES (1,'777','phpshop-test',0,'','','1.1');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10027, 'Visa, Mastercard, МИР (MandarinPay)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/mandarin_logo.png');
