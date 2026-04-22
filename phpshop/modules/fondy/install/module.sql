DROP TABLE IF EXISTS `phpshop_modules_fondy_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_fondy_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `payment_type` varchar(64) NOT NULL,
  `mode_work` varchar(20) NOT NULL,
  `title_sub` text NOT NULL,
  `title_payment` text NOT NULL,
  `version` varchar(64) DEFAULT '1.0',
  `transaction_type` varchar(64) NOT NULL,
  `status_checkout` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_fondy_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_fondy_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10034, 'Fondy (Fondy)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Fondy', '', '/UserFiles/Image/Payments/visa.png');

INSERT INTO `phpshop_modules_fondy_system` VALUES (1, '', '', 'test', '', 'Fondy', 'Fondy', '1.0', '', '');

INSERT INTO `phpshop_order_status` SET `id` = 101,  `name` = 'Оплачено платежными системами', `color` = '#ccff00', `sklad_action` = '0', `cumulative_action` = '0',  `mail_action` = '0',  `mail_message` = '' ON DUPLICATE KEY UPDATE `id` = 101,  `name` = 'Оплачено платежными системами', `color` = '#ccff00', `sklad_action` = '0', `cumulative_action` = '0',  `mail_action` = '0',  `mail_message` = '';
INSERT INTO `phpshop_order_status` SET `id` = 102,  `name` = 'Ожидает оплаты', `color` = '#ccff00', `sklad_action` = '0', `cumulative_action` = '0',  `mail_action` = '0',  `mail_message` = '' ON DUPLICATE KEY UPDATE `id` = 102,  `name` = 'Ожидает оплаты', `color` = '#ccff00', `sklad_action` = '0', `cumulative_action` = '0',  `mail_action` = '0',  `mail_message` = '';