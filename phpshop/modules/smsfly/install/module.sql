
DROP TABLE IF EXISTS `phpshop_modules_smsfly_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_smsfly_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merchant_user` varchar(64) NOT NULL DEFAULT '',
  `merchant_pwd` varchar(64) NOT NULL DEFAULT '',
  `phone` varchar(64) NOT NULL DEFAULT '',
  `sandbox` enum('1','2') NOT NULL DEFAULT '2',
  `alfaname` varchar(64) NOT NULL DEFAULT '',
  `version` float(2) NOT NULL DEFAULT '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_smsfly_system` (`id`, `merchant_user`, `merchant_pwd`, `phone`, `sandbox`,`alfaname`,`version`) VALUES
(1, '', '', '','2','ShopName','1.4');
