
ALTER TABLE  `phpshop_news` ADD  `meta_title` VARCHAR( 255 ) NOT NULL ,
ADD  `meta_keywords` TEXT NOT NULL ,
ADD  `meta_description` TEXT NOT NULL ;

DROP TABLE IF EXISTS `phpshop_modules_seometanews_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_seometanews_system` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `description` varchar(255)  NOT NULL default '',
  `keywords` varchar(255) NOT NULL default '',
  `version` FLOAT(2) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_seometanews_system` VALUES (1,'Новости','Новости','Новости','1.1');
