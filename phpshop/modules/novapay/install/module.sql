DROP TABLE IF EXISTS `phpshop_modules_novapay_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_novapay_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `merchant_id` varchar(255) DEFAULT NULL,
  `private_key` text DEFAULT NULL,
  `public_key` text DEFAULT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `dev_mode` enum('0','1') NOT NULL default '0',
  `version` varchar(64) DEFAULT '1.5' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_novapay_system` (`id`, `status`, `merchant_id`, `private_key`, `public_key`, `title`, `title_end`, `dev_mode`, `version`) VALUES
(1, 0, '', '', '', 'Оплатить сейчас', 'Оплатите пожалуйста свой заказ', '0', 1.0);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10023, 'NovaPay', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/visa.png');

CREATE TABLE IF NOT EXISTS `phpshop_modules_novapay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `status_code` varchar(255) NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;