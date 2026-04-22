
DROP TABLE IF EXISTS `phpshop_modules_cloudpayment_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_cloudpayment_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `publicId` varchar(64) NOT NULL default '',
  `api` varchar(64) NOT NULL default '',
  `taxationSystem` int(11) NOT NULL,
  `description` varchar(64) NOT NULL default '',
  `version` varchar(64) default '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_cloudpayment_system` (`id`, `status`, `title`, `title_end`, `publicId`, `api`, `description`,`version`) VALUES
(1, 0, 'Оплатить сейчас', 'Оплатите пожалуйста свой заказ', '', '','','1.1');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10014, 'CloudPayments', 'modules', '0', 0, '<p>Ваш заказ оплачен!</p>', 'Спасибо', '', 'phpshop/modules/cloudpayments/templates/cloudpayments.png');

CREATE TABLE IF NOT EXISTS `phpshop_modules_cloudpayment_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;