ALTER TABLE `phpshop_categories` ADD `option6` text;
ALTER TABLE `phpshop_categories` ADD `option7` text;
ALTER TABLE `phpshop_categories` ADD `option8` text;
ALTER TABLE `phpshop_categories` ADD `option9` text;
ALTER TABLE `phpshop_categories` ADD `option10` text;
ALTER TABLE `phpshop_products` ADD `option1` text;
ALTER TABLE `phpshop_products` ADD `option2` text;
ALTER TABLE `phpshop_products` ADD `option3` text;
ALTER TABLE `phpshop_products` ADD `option4` text;
ALTER TABLE `phpshop_products` ADD `option5` text;
--
-- Структура таблицы `phpshop_modules_iconcat_system`
--

DROP TABLE IF EXISTS `phpshop_modules_productoption_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productoption_system` (
  `id` int(11) NOT NULL auto_increment,
  `option` blob NOT NULL,
  `version` varchar(64) NOT NULL default '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;



INSERT INTO `phpshop_modules_productoption_system` VALUES (1,'','1.4');