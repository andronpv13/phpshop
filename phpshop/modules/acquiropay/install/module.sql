
DROP TABLE IF EXISTS `phpshop_modules_acquiropay_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_acquiropay_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_sub` text NOT NULL,
  `product_id` varchar(255) NOT NULL default '',
  `merchant_id` varchar(255) NOT NULL default '',
  `merchant_skey` varchar(255) NOT NULL default '',
  `endpoint_url` varchar(255) NOT NULL default '',
  `use_cashbox` TINYINT NOT NULL DEFAULT 0,
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_acquiropay_system`
SET
    `status` = 0,
    `title` = 'Пожалуйста, оплатите свой заказ',
    `title_sub` = 'Заказ находится на ручной проверке';

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10018, 'Visa, Mastercard (AcquiroPay)', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', '/UserFiles/Image/Payments/visa.png');