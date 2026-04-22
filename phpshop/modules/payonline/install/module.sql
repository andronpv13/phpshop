DROP TABLE IF EXISTS `phpshop_modules_payonline_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_payonline_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `title_sub` text NOT NULL,
  `title_payment` text NOT NULL,
  `page_id` int(11) NOT NULL,
  `fiskalization` enum('0','1') NOT NULL default '0',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_payonline_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_payonline_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10033, 'Оплата банковской картой (Payonline)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/visa.png');

INSERT INTO `phpshop_modules_payonline_system` VALUES (1, '', '', 0, 'Заказ находится на ручной проверке.', 'Оплатить сейчас', 0, '0', '1.0');