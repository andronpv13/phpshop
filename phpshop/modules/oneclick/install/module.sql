DROP TABLE IF EXISTS `phpshop_modules_oneclick_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_oneclick_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1','2') default '1',
  `title` text,
  `title_end` text,
  `serial` varchar(64) default '',
  `windows` enum('0','1') default '0',
  `display` enum('0','1') default '0',
  `write_order` enum('0','1') default '0',
  `captcha` enum('0','1') default '0',
  `only_available` enum('0','1','2') default '0',
  `status` int(11) default '0',
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_oneclick_system` VALUES (1,'0','Спасибо, Ваш заказ принят!','Наши менеджеры свяжутся с Вами для уточнения деталей.','','1','0','0','1', '0', '0','1.9');

DROP TABLE IF EXISTS `phpshop_modules_oneclick_jurnal`;
CREATE TABLE `phpshop_modules_oneclick_jurnal` (
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