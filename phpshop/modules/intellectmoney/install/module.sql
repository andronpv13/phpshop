DROP TABLE IF EXISTS `phpshop_modules_intellectmoney_settings`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_intellectmoney_settings` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(64) NOT NULL default '', 
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_modules_intellectmoney_logs` (
  `id` int(11) NOT NULL auto_increment,
  `timestamp` int(11) NOT NULL default 0, 
  `message` varchar(255) NOT NULL default '',
  `kind` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('module_id','20115');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('eshopId','467253');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('secretKey','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('expireDate','4319');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('testMode','off');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('holdMode','off');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('holdTime','119');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('tax','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusCreated','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusCancelled','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusPaid','101');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusHolded','101');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusPartiallyPaid','101');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('statusRefunded','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('merchantReceiptType','1');
INSERT INTO `phpshop_modules_intellectmoney_settings`(`key`, `value`) VALUES ('integrationMethod','Default');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(20115, 'Visa, Mastercard, Ã»–, ﬂPay (IntellectMoney)', 'modules', '1', 0, '', '', '', '/UserFiles/Image/Payments/intellectmoney.png');