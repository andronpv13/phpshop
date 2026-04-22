DROP TABLE IF EXISTS `phpshop_modules_status_history`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_status_history` (
  `id` int(11) NOT NULL auto_increment,
  `unix_data` varchar(64) NOT NULL DEFAULT '',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT '',
  `ouid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
ALTER TABLE  `phpshop_modules_status_history` ADD INDEX (  `ouid` ) ;