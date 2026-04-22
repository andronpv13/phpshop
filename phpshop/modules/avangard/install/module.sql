DROP TABLE IF EXISTS `phpshop_modules_avangard_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_avangard_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shop_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `shop_sign` varchar(255) NOT NULL,
  `av_sign` varchar(255) NOT NULL,
  `qr` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `title_sub` text NOT NULL,
  `title_payment` text NOT NULL,
  `version` varchar(64) default '1.1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_avangard_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_avangard_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_avangard_order_status`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_avangard_order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `order` varchar(64) NOT NULL,
  `status_code` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10013, 'Оплата банковской картой (Банк Авангард)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/visa.png');

INSERT INTO `phpshop_modules_avangard_system` VALUES (1, 0, '', '', '', 0, 0, 0, 'Заказ находится на ручной проверке.', 'Оплатить сейчас', '1.1');