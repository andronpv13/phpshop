--
-- Структура таблицы `phpshop_modules_panorama360_system`
--

DROP TABLE IF EXISTS `phpshop_modules_panorama360_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_panorama360_system` (
  `id` int(11) NOT NULL auto_increment,
  `frame` int(11),
  `version` varchar(64) default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



INSERT INTO `phpshop_modules_panorama360_system` VALUES (1,'28','1.0');
ALTER TABLE `phpshop_products` ADD `img_panorama360` varchar(255) NOT NULL default '';