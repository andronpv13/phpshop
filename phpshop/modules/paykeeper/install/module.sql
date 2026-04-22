

DROP TABLE IF EXISTS `phpshop_modules_paykeeper_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_paykeeper_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `form_url` varchar(128) NOT NULL default '',
  `secret` varchar(64) NOT NULL default '',
  `serial` varchar(64) NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  `forced_discount_check` INT(1) ,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


INSERT INTO `phpshop_modules_paykeeper_system` VALUES (1,0,'Платежная система PayKeeper','Оплата банковскими картами Visa и MasterCard','','','','1.1',0);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10115, 'Оплата пластиковыми картами Visa и MasterCard', 'paykeeper', '1', 99, '', '', '', '/UserFiles/Image/Payments/paykeeper_logo.png');
