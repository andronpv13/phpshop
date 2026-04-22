ALTER TABLE `phpshop_orders` ADD `ofd` blob;
ALTER TABLE `phpshop_orders` ADD `ofd_status` enum('0','1','2') default '0';

CREATE TABLE `phpshop_modules_cloudkassir_system` (
  `id` int(11) NOT NULL auto_increment,
  `publicid` varchar(64) default '',
  `inn` varchar(64) default '',
  `apisecret` varchar(64) default '',
  `taxationSystem` int(11) NOT NULL,
  `version` varchar(64) default '1.1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 ;

INSERT INTO `phpshop_modules_cloudkassir_system` VALUES (1,'','','', 0, '1.1');
CREATE TABLE IF NOT EXISTS `phpshop_modules_cloudkassir_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text CHARACTER SET utf8 NOT NULL,
  `order_id` int(11),
  `order_uid` varchar(64) NOT NULL DEFAULT '',
  `status` enum('1','2') NOT NULL DEFAULT '1',
  `path` varchar(64) NOT NULL DEFAULT '',
  `operation` varchar(64) NOT NULL DEFAULT '',
  `fiscal` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
      