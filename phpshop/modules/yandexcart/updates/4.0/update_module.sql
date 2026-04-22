# 4.1
ALTER TABLE `phpshop_categories` ADD `category_yandexcart` int(11) DEFAULT 0;

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexcart_categories` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`parent_to` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;
