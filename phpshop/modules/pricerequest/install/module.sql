DROP TABLE IF EXISTS `phpshop_modules_pricerequest_system`;
CREATE TABLE `phpshop_modules_pricerequest_system` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `mail` varchar(64) NOT NULL default '',
  `message` varchar(255) NOT NULL default '',
  `display` enum('0','1') default '0',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_pricerequest_system` VALUES (1,'Запрос цены','','Наши менеджеры свяжутся с Вами для уточнения деталей.','0','1.0');

DROP TABLE IF EXISTS `phpshop_modules_pricerequest_jurnal`;
CREATE TABLE `phpshop_modules_pricerequest_jurnal` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `name` varchar(64) default '',
  `tel` varchar(64) default '',
  `message` text,
  `product_name` varchar(64) default '',
  `product_id` int(11),
  `product_price` varchar(64) default '',
  `product_image` varchar(255) default '',
  `ip` varchar(64) default '',
  `status` enum('1','2','3','4') default '1',
  `mail` varchar(64) default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
