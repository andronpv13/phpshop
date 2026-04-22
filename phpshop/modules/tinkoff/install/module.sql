DROP TABLE IF EXISTS `phpshop_modules_tinkoff_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_tinkoff_system` (
    `id` int(11) NOT NULL auto_increment,
    `title` text NOT NULL,
    `terminal` varchar(64) NOT NULL default '',
    `secret_key` varchar(64) NOT NULL default '',
    `gateway` varchar(64) NOT NULL default '',
    `force_payment` enum('0','1') NOT NULL default '0',
    `version` varchar(64) DEFAULT '1.0',
    `enabled_taxation` int DEFAULT 0,
    `status` int(11) NOT NULL,
    `title_end` text NOT NULL,
    `taxation` varchar(64) NOT NULL,
    `status_confirmed` int(11) NOT NULL,
    `status_receipt` int(11) NOT NULL,
`payment_mode` ENUM('1','2') NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_tinkoff_system` (`id`, `title`, `terminal`, `secret_key`, `gateway`, `force_payment`, `version`, `enabled_taxation`, `status`, `title_end`, `taxation`) VALUES
(1, 'Платежная система Т-Банк', 'TinkoffBankTest', 'TinkoffBankTest', 'https://securepay.tinkoff.ru/v2', '0', '2.6', 0, 0, '', 'osn');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10032, 'Мир, Visa, Mastercard (Т-Банк)', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/tbank.svg');

CREATE TABLE IF NOT EXISTS `phpshop_modules_tinkoff_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
