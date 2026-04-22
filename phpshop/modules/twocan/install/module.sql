DROP TABLE IF EXISTS `phpshop_modules_twocan_system`;
CREATE TABLE `phpshop_modules_twocan_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(64) NOT NULL,
  `password` varchar(64) NOT NULL,
  `terminal` varchar(64) NOT NULL,
  `url` varchar(64) NOT NULL,
  `test_url` varchar(64) NOT NULL,
  `dev_mode` enum('0','1') NOT NULL DEFAULT '0',
  `autocharge` enum('0','1') NOT NULL DEFAULT '0',
  `template` varchar(64) DEFAULT NULL,
  `exptimeout` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `status_auth` int(11) NOT NULL,
  `version` varchar(64) DEFAULT '1.0',
  `title_sub` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


CREATE TABLE `phpshop_modules_twocan_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  `twocan_id` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE `phpshop_modules_twocan_orders` (
  `id` varchar(20) NOT NULL,
  `amount` float NOT NULL,
  `charged` float NOT NULL,
  `refunded` float NOT NULL,
  `status` varchar(20) NOT NULL,
  `twocanid` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10028, 'Онлайн оплата Visa, Mastercard, МИР (2can&ibox)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/visa.png');

INSERT INTO `phpshop_modules_twocan_system` VALUES (1, '', '', '', '','', 0, 0,'', NULL, 0, 0, '1.0', 'Заказ находится на ручной проверке.');