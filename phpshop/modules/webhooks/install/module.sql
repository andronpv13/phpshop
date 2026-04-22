DROP TABLE IF EXISTS `phpshop_modules_webhooks_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_webhooks_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(64),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_modules_webhooks_system` VALUES (1,'1.0');

DROP TABLE IF EXISTS `phpshop_modules_webhooks_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_webhooks_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `date` int(11),
  `message` text,
  `form_id` int(11),
  `status` varchar(255),
  `type` varchar(64) ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_webhooks_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_webhooks_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  `url` varchar(255),
  `status` int(11),
  `type` int(11),
  `enabled` enum('0','1') DEFAULT '0',
  `send` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

