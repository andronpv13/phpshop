DROP TABLE IF EXISTS `phpshop_modules_idram_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_idram_system` (
    `id` int(11) NOT NULL auto_increment,
    `idram_id` varchar(255) DEFAULT NULL,
    `secret_key` varchar(255) DEFAULT NULL,
    `language` varchar(64) DEFAULT 'RU' NOT NULL,
    `title` text NOT NULL,
    `status` int(11) NOT NULL,
    `payment_description` text NOT NULL,
    `payment_status` text NOT NULL,
    `version` varchar(64) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_idram_system` (`id`, `idram_id`, `secret_key`, `language`, `title`, `status`, `payment_description`, `payment_status`, `version`) VALUES
(1, '', '', 'RU', 'Оплатить сейчас', 0, 'Оплатите пожалуйста свой заказ.', 'Ваш заказ принят в обработку.', 1.0);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10046, 'Idram', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/visa.png');

CREATE TABLE IF NOT EXISTS `phpshop_modules_idram_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;