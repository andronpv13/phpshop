--ALTER TABLE `phpshop_products` ADD `kvk_promo` varchar(20) NOT NULL default '';
--ALTER TABLE `phpshop_products` ADD `kvk_enabled` enum('0','1') NOT NULL default '0';

DROP TABLE IF EXISTS `phpshop_modules_alfacredit_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_alfacredit_system` (
  `id` int(11) NOT NULL auto_increment,
  `inn` varchar(50) NOT NULL default '',
  `category_name` varchar(50) NOT NULL default '',
  `action_name` varchar(50) NOT NULL default '',
  `min_sum_cre` int(15),
  `cre_name` varchar(50) NOT NULL default '',
  `min_sum_ras` int(15),
  `ras_name` varchar(50) NOT NULL default '',
  `prod_mode` enum('0','1') NOT NULL default '0',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_alfacredit_system` (`id`, `inn`, `category_name`, `action_name`, `min_sum_cre`, `cre_name`, `min_sum_ras`, `ras_name`, `prod_mode`, `version`) VALUES
(1, '', '', '', 5000, 'Купить в кредит', 10000, 'Купить в рассрочку', '1', 1.0);

CREATE TABLE IF NOT EXISTS `phpshop_modules_alfacredit_log` (
  `id` int(11) NOT NULL auto_increment,
  `reference` varchar(16) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `status` text NOT NULL,
  `cart` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE `phpshop_modules_alfacredit_log` ADD INDEX(`reference`);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10045, 'AlfaCredit', 'modules', '0', 0, '', '', '', 'phpshop/modules/alfacredit/templates/alfabank.png');