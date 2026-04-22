DROP TABLE IF EXISTS `phpshop_modules_assistmoney_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_assistmoney_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `merchant_id` varchar(64) NOT NULL default '',
  `merchant_sig` varchar(64) NOT NULL default '',
  `assist_url` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_assistmoney_system` VALUES (1,0,'Платежная система Assist','Оплатите пожалуйста свой заказ','','','','','1.0');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10010, 'Assist', 'modules', '0', 0, '<img src="/UserFiles/Image/Trial/rabbit.png" alt="" align="" border="0"><h3>Благодарим Вас за заказ!</h3><p>Ваш заказ оплачен, в ближайшее время наш менеджер свяжется с Вами.</p>', '', '', 'phpshop/modules/assist/templates/assist-money.png');