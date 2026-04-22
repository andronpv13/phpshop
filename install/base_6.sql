DROP TABLE IF EXISTS `phpshop_1c_docs`;
CREATE TABLE IF NOT EXISTS `phpshop_1c_docs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT '0',
  `cid` varchar(64) DEFAULT '',
  `datas` int(11) DEFAULT '0',
  `datas_f` int(11) DEFAULT '0',
  `year` int(11) DEFAULT '2018',
  PRIMARY KEY (`id`),
  KEY `cid` (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_1c_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_1c_jurnal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datas` varchar(64) DEFAULT '0',
  `p_name` varchar(64) DEFAULT '',
  `f_name` varchar(64) DEFAULT '',
  `time` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_baners`;
CREATE TABLE IF NOT EXISTS `phpshop_baners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '',
  `content` text,
  `type` enum('0','1','2','3') DEFAULT '0',
  `display` enum('0','1') DEFAULT '0',
  `flag` enum('0','1') DEFAULT '0',
  `datas` varchar(32) DEFAULT '',
  `size` enum('0','1','2') DEFAULT '0',
  `dir` varchar(255) DEFAULT '',
  `dop_cat` varchar(255) DEFAULT '',
  `servers` varchar(1000) DEFAULT '',
  `skin` varchar(64) DEFAULT '',
  `image` varchar(255) DEFAULT NULL,
  `description` text,
  `link` varchar(255) DEFAULT NULL,
  `mobile` enum('0','1') DEFAULT '0',
  `color` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_baners` (`id`, `name`, `content`, `type`, `display`, `flag`, `datas`, `size`, `dir`, `dop_cat`, `servers`, `skin`, `image`, `description`, `link`, `mobile`) VALUES
(1, 'Скидка -20%', '<p>Напишите подробное описание акции в меню Маркетинг - Баннеры. Добавьте картинку. Используйте темный фон для этого баннера.</p>', '0', '0', '1', '', '0', '', '', '', '', '/UserFiles/Image/trial/banner990x900.jpg', 'Купить сейчас!', '/podkatalog1-pod-podkatalog1.html', '0'),
(2, 'Распродажа', '<p>Опишите подробности акции, сроки проведения в меню Маркетинг - Баннеры. Установите скидку в Маркетинг - Промоакции.</p>\r\n<p>Создайте купон в Модули - Продажи - Промокоды.</p>', '2', '0', '1', '', '0', '', '', '', '', '/UserFiles/Image/trial/slider1700x600.jpg', 'Купить', '/', '0'),
(3, 'Акция', '<p>Описание условий акции.</p>', '1', '1', '0', '', '0', '', '', '', '', '/UserFiles/Image/trial/slider-mobile800x400.jpg', 'Подробнее', '/news/', '0'),
(4, 'Баннер в меню', '<p style="color: #ffffff;">Этот баннер можно скрыть. Вставьте свой текст и картинку в меню Маркетинг - Баннеры, тип вывода "В меню каталога".</p>', '3', '0', '1', '', '0', '', '', '', '', '/UserFiles/Image/trial/popup740x410.jpg', 'Далее', '', '0');


DROP TABLE IF EXISTS `phpshop_black_list`;
CREATE TABLE IF NOT EXISTS `phpshop_black_list` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(32) DEFAULT '',
  `datas` varchar(32) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_bonus`;
CREATE TABLE IF NOT EXISTS `phpshop_bonus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `comment` varchar(255) DEFAULT '',
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `bonus_operation` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_categories`;
CREATE TABLE IF NOT EXISTS `phpshop_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `num` int(11) DEFAULT '0',
  `parent_to` int(11) NOT NULL DEFAULT '0',
  `yml` enum('0','1') DEFAULT '1',
  `num_row` enum('1','2','3','4','5','6') DEFAULT '3',
  `num_cow` tinyint(11) DEFAULT '0',
  `sort` blob,
  `content` text,
  `vid` enum('0','1') DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `title_enabled` enum('0','1','2') DEFAULT '0',
  `title_shablon` varchar(255) DEFAULT '',
  `descrip` varchar(255) DEFAULT '',
  `descrip_enabled` enum('0','1','2') DEFAULT '0',
  `descrip_shablon` varchar(255) DEFAULT '',
  `keywords` varchar(255) DEFAULT '',
  `keywords_enabled` enum('0','1','2') DEFAULT '0',
  `keywords_shablon` varchar(255) DEFAULT '',
  `skin` varchar(64) DEFAULT '',
  `skin_enabled` enum('0','1') DEFAULT '0',
  `order_by` enum('1','2','3') DEFAULT '3',
  `order_to` enum('1','2') DEFAULT '1',
  `secure_groups` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `icon_description` varchar(255) DEFAULT '',
  `dop_cat` varchar(255) DEFAULT '',
  `parent_title` int(11) DEFAULT '0',
  `sort_cache` blob,
  `sort_cache_created_at` int(11) DEFAULT NULL,
  `menu` enum('0','1') DEFAULT '0',
  `cat_seo_name` varchar(255) DEFAULT '',
  `cat_seo_name_old` varchar(255) DEFAULT '',
  `tile` enum('0','1') DEFAULT '0',
  `length` varchar(64) DEFAULT '',
  `width` varchar(64) DEFAULT '',
  `height` varchar(64) DEFAULT '',
  `weight` float DEFAULT '0',
  `ed_izm` varchar(64) DEFAULT '',
  `color` varchar(64) DEFAULT '',
  `podcatalog_view` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_to` (`parent_to`),
  KEY `servers` (`servers`),
  KEY `vid` (`vid`),
  KEY `skin_enabled` (`skin_enabled`),
  KEY `menu` (`menu`),
  KEY `tile` (`tile`),
  KEY `podcatalog_view` (`podcatalog_view`),
  KEY `dop_cat` (`dop_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_categories` (`id`, `name`, `num`, `parent_to`, `yml`, `num_row`, `num_cow`, `sort`, `content`, `vid`, `servers`, `title`, `title_enabled`, `title_shablon`, `descrip`, `descrip_enabled`, `descrip_shablon`, `keywords`, `keywords_enabled`, `keywords_shablon`, `skin`, `skin_enabled`, `order_by`, `order_to`, `secure_groups`, `icon`, `icon_description`, `dop_cat`, `parent_title`, `sort_cache`, `sort_cache_created_at`, `menu`, `cat_seo_name`, `cat_seo_name_old`, `tile`, `length`, `width`, `height`, `weight`, `ed_izm`, `color`) VALUES
(1, 'Каталог1', 1, 0, '1', '4', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '<p>Описание каталога</p>', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-01.png', '', '', 0, '', 0, '0', 'katalog1', '', '0', '', '', '', 0, '', ''),
(2, 'Подкаталог1', 1, 1, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '<p>Описание каталога</p>', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-02.png', '', '', 0, '', 0, '1', 'katalog1-podkatalog1', '', '1', '', '', '', 0, '', ''),
(3, 'Подкаталог2', 1, 1, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-05.png', '', '', 0, '', 0, '0', 'katalog1-podkatalog2', '', '1', '', '', '', 0, '', ''),
(4, 'Подкаталог3', 1, 1, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-06.png', '', '', 0, '', 0, '0', 'katalog1-podkatalog3', '', '1', '', '', '', 0, '', ''),
(5, 'Под-подкаталог1', 1, 2, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-03.png', '', '', 0, '', 0, '0', 'podkatalog1-pod-podkatalog1', '', '0', '', '', '', 0, '', ''),
(6, 'Под-подкаталог2', 1, 2, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-04.png', '', '', 0, '', 0, '0', 'podkatalog1-pod-podkatalog2', '', '0', '', '', '', 0, '', ''),
(7, 'Каталог2', 1, 0, '1', '1', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-07.png', '', '', 0, '', 0, '0', 'katalog2', '', '1', '', '', '', 0, '', ''),
(8, 'Подкаталог1', 1, 7, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-08.png', '', '', 0, '', 0, '0', 'katalog2-podkatalog1', '', '0', '', '', '', 0, '', ''),
(9, 'Подкаталог2', 1, 7, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-01.png', '', '', 0, '', 0, '0', 'katalog2-podkatalog2', '', '1', '', '', '', 0, '', ''),
(10, 'Каталог3', 1, 0, '1', '3', 0, 0x613a323a7b693a303b733a313a2237223b693a313b733a313a2234223b7d, '', '0', '', '', '0', '', '', '0', '', '', '0', '', '', '0', '1', '1', '', '/UserFiles/Image/trial/catalog410x200-09.png', '', '', 0, '', 0, '0', 'katalog3', '', '1', '', '', '', 0, '', '');

--

DROP TABLE IF EXISTS `phpshop_citylist_city`;
CREATE TABLE IF NOT EXISTS `phpshop_citylist_city` (
  `city_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` int(11) UNSIGNED DEFAULT '0',
  `region_id` int(10) UNSIGNED DEFAULT '0',
  `name` varchar(128) DEFAULT '',
  PRIMARY KEY (`city_id`),
  KEY `country_id` (`country_id`),
  KEY `region_id` (`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_citylist_country`;
CREATE TABLE IF NOT EXISTS `phpshop_citylist_country` (
  `country_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` int(11) DEFAULT '0',
  `name` varchar(128) DEFAULT '',
  PRIMARY KEY (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_citylist_region`;
CREATE TABLE IF NOT EXISTS `phpshop_citylist_region` (
  `region_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `country_id` int(10) UNSIGNED DEFAULT '0',
  `city_id` int(10) UNSIGNED DEFAULT '0',
  `name` varchar(64) DEFAULT '',
  PRIMARY KEY (`region_id`),
  KEY `country_id` (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_comment`;
CREATE TABLE IF NOT EXISTS `phpshop_comment` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datas` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `content` text,
  `user_id` int(11) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '0',
  `rate` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_company`;
CREATE TABLE IF NOT EXISTS `phpshop_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `bank` blob,
  `enabled` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_delivery`;
CREATE TABLE IF NOT EXISTS `phpshop_delivery` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city` varchar(255) DEFAULT '',
  `price` float DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '1',
  `flag` enum('0','1') DEFAULT '0',
  `price_null` float DEFAULT '0',
  `price_null_enabled` enum('0','1') DEFAULT '0',
  `PID` int(11) DEFAULT '0',
  `taxa` int(11) DEFAULT '0',
  `is_folder` enum('0','1') DEFAULT '0',
  `city_select` enum('0','1','2') DEFAULT '0',
  `data_fields` blob,
  `num` smallint(3) DEFAULT '0',
  `icon` varchar(255) DEFAULT '',
  `payment` varchar(255) DEFAULT '',
  `ofd_nds` varchar(64) DEFAULT '',
  `sum_max` float DEFAULT '0',
  `sum_min` float DEFAULT '0',
  `weight_max` int(11) DEFAULT '0',
  `weight_min` int(11) DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  `is_mod` enum('1','2') DEFAULT '1',
  `warehouse` int(11) DEFAULT '0',
  `comment` text,
  `categories_check` ENUM('0','1') DEFAULT '0', 
  `categories` VARCHAR(255),
  `external_code` VARCHAR(64) NOT NULL,
  `length_max` INT(11) DEFAULT '0',
  `height_max` INT(11) DEFAULT '0',
  `width_max` INT(11) DEFAULT '0',
  `sum_side_max` INT(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_delivery` (`id`, `city`, `price`, `enabled`, `flag`, `price_null`, `price_null_enabled`, `PID`, `taxa`, `is_folder`, `city_select`, `data_fields`, `num`, `icon`, `payment`, `ofd_nds`, `sum_max`, `sum_min`, `weight_max`, `weight_min`, `servers`, `is_mod`, `warehouse`, `comment`) VALUES
(1, 'Доставка курьером', 0, '1', '1', 0, '0', 0, 0, '', '0', 0x613a323a7b733a373a22656e61626c6564223b613a31323a7b733a373a22636f756e747279223b613a313a7b733a343a226e616d65223b733a363a22d1f2f0e0ede0223b7d733a353a227374617465223b613a313a7b733a343a226e616d65223b733a31313a22d0e5e3e8eeed2ff8f2e0f2223b7d733a343a2263697479223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a353a22c3eef0eee4223b7d733a353a22696e646578223b613a313a7b733a343a226e616d65223b733a363a22c8ede4e5eaf1223b7d733a333a2266696f223b613a313a7b733a343a226e616d65223b733a333a22d4c8ce223b7d733a333a2274656c223b613a313a7b733a343a226e616d65223b733a373a22d2e5ebe5f4eeed223b7d733a363a22737472656574223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a353a22d3ebe8f6e0223b7d733a353a22686f757365223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a333a22c4eeec223b7d733a353a22706f726368223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a373a22cfeee4fae5e7e4223b7d733a31303a22646f6f725f70686f6e65223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a31323a22caeee420e4eeeceef4eeede0223b7d733a343a22666c6174223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a383a22cae2e0f0f2e8f0e0223b7d733a393a2264656c697674696d65223b613a323a7b733a373a22656e61626c6564223b733a313a2231223b733a343a226e616d65223b733a31343a22c2f0e5ecff20e4eef1f2e0e2eae8223b7d7d733a333a226e756d223b613a31323a7b733a373a22636f756e747279223b733a313a2231223b733a353a227374617465223b733a313a2232223b733a343a2263697479223b733a313a2233223b733a353a22696e646578223b733a313a2234223b733a333a2266696f223b733a313a2235223b733a333a2274656c223b733a313a2236223b733a363a22737472656574223b733a313a2237223b733a353a22686f757365223b733a313a2238223b733a353a22706f726368223b733a313a2239223b733a31303a22646f6f725f70686f6e65223b733a323a223130223b733a343a22666c6174223b733a323a223131223b733a393a2264656c697674696d65223b733a323a223132223b7d7d, 1, '/UserFiles/Image/trial/002-delivery-man.png', 'null', '20', 0, 0, 0, 0, '', '1', 0, ''),
(2, 'Самовывоз', 0, '1', '0', 0, '0', 0, 0, '', '0', 0x613a323a7b733a373a22656e61626c6564223b613a31323a7b733a373a22636f756e747279223b613a313a7b733a343a226e616d65223b733a363a22d1f2f0e0ede0223b7d733a353a227374617465223b613a313a7b733a343a226e616d65223b733a31313a22d0e5e3e8eeed2ff8f2e0f2223b7d733a343a2263697479223b613a313a7b733a343a226e616d65223b733a353a22c3eef0eee4223b7d733a353a22696e646578223b613a313a7b733a343a226e616d65223b733a363a22c8ede4e5eaf1223b7d733a333a2266696f223b613a313a7b733a343a226e616d65223b733a333a22d4c8ce223b7d733a333a2274656c223b613a313a7b733a343a226e616d65223b733a373a22d2e5ebe5f4eeed223b7d733a363a22737472656574223b613a313a7b733a343a226e616d65223b733a353a22d3ebe8f6e0223b7d733a353a22686f757365223b613a313a7b733a343a226e616d65223b733a333a22c4eeec223b7d733a353a22706f726368223b613a313a7b733a343a226e616d65223b733a373a22cfeee4fae5e7e4223b7d733a31303a22646f6f725f70686f6e65223b613a313a7b733a343a226e616d65223b733a31323a22caeee420e4eeeceef4eeede0223b7d733a343a22666c6174223b613a313a7b733a343a226e616d65223b733a383a22cae2e0f0f2e8f0e0223b7d733a393a2264656c697674696d65223b613a313a7b733a343a226e616d65223b733a31343a22c2f0e5ecff20e4eef1f2e0e2eae8223b7d7d733a333a226e756d223b613a31323a7b733a373a22636f756e747279223b733a313a2231223b733a353a227374617465223b733a313a2232223b733a343a2263697479223b733a313a2233223b733a353a22696e646578223b733a313a2234223b733a333a2266696f223b733a313a2235223b733a333a2274656c223b733a313a2236223b733a363a22737472656574223b733a313a2237223b733a353a22686f757365223b733a313a2238223b733a353a22706f726368223b733a313a2239223b733a31303a22646f6f725f70686f6e65223b733a323a223130223b733a343a22666c6174223b733a323a223131223b733a393a2264656c697674696d65223b733a323a223132223b7d7d, 2, '/UserFiles/Image/trial/001-map.png', 'null', '20', 0, 0, 0, 0, '', '1', 0, 'Выберите модуль агрегатора доставки');

DROP TABLE IF EXISTS `phpshop_dialog`;
CREATE TABLE IF NOT EXISTS `phpshop_dialog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `message` text,
  `chat_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `bot` varchar(64) DEFAULT NULL,
  `staffid` enum('0','1') DEFAULT '1',
  `isview` enum('0','1') DEFAULT '1',
  `order_id` int(11) DEFAULT '0',
  `attachments` varchar(255) DEFAULT NULL,
  `isview_user` enum('0','1') DEFAULT '1',
  `ai` ENUM('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_dialog_answer`;
CREATE TABLE IF NOT EXISTS `phpshop_dialog_answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `message` text,
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11) DEFAULT NULL,
  `servers` varchar(1000) DEFAULT '',
  `view` enum('1','2') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_discount`;
CREATE TABLE IF NOT EXISTS `phpshop_discount` (
  `id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sum` int(255) DEFAULT '0',
  `discount` float DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '0',
  `action` enum('1','2') DEFAULT '1',
  `block_old_price` enum('0','1') DEFAULT '0',
  `block_categories` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_exchanges`;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `option` blob,
  `type` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_foto`;
CREATE TABLE IF NOT EXISTS `phpshop_foto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `num` tinyint(11) DEFAULT '0',
  `info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_foto` (`id`, `parent`, `name`, `num`, `info`) VALUES
(1, 15, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(2, 15, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(3, 15, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(4, 14, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(5, 14, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(6, 14, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(7, 13, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(8, 13, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(9, 13, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(10, 12, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(11, 12, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(12, 12, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(13, 11, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(14, 11, '/UserFiles/Image/trial/primer-foto2.jpg', 3, ''),
(15, 11, '/UserFiles/Image/trial/primer-foto3.jpg', 5, ''),
(16, 10, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(17, 10, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(18, 10, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(19, 9, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(20, 9, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(21, 9, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(22, 8, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(23, 8, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(24, 8, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(25, 7, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(26, 7, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(27, 7, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(28, 6, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(29, 6, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(30, 6, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(31, 5, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(32, 5, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(33, 5, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(34, 4, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(35, 4, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(36, 4, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(37, 3, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(38, 3, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(39, 3, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(40, 2, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(41, 2, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(42, 2, '/UserFiles/Image/trial/primer-foto3.jpg', 2, ''),
(43, 1, '/UserFiles/Image/trial/primer-foto.jpg', 0, ''),
(44, 1, '/UserFiles/Image/trial/primer-foto2.jpg', 1, ''),
(45, 1, '/UserFiles/Image/trial/primer-foto3.jpg', 2, '');

DROP TABLE IF EXISTS `phpshop_gbook`;
CREATE TABLE IF NOT EXISTS `phpshop_gbook` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datas` int(11) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `mail` varchar(32) DEFAULT NULL,
  `tema` text,
  `otsiv` text,
  `otvet` text,
  `flag` enum('0','1') DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_jurnal` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user` varchar(64) NOT NULL DEFAULT '',
  `datas` varchar(32) NOT NULL DEFAULT '',
  `flag` enum('0','1') NOT NULL DEFAULT '0',
  `ip` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_menu`;
CREATE TABLE IF NOT EXISTS `phpshop_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '',
  `content` text,
  `flag` enum('0','1') DEFAULT '1',
  `num` int(11) DEFAULT '0',
  `dir` varchar(64) DEFAULT NULL,
  `element` enum('0','1') DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  `dop_cat` VARCHAR(255) DEFAULT '',
  `mobile` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `flag` (`flag`),
  KEY `element` (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_menu` (`id`, `name`, `content`, `flag`, `num`, `dir`, `element`, `servers`) VALUES
(1, 'Текстовый блок', '<p>Это текстовый блок. Меняется в меню Веб-сайт - Текстовые блоки. </p>', '1', 1, '', '0', '');

DROP TABLE IF EXISTS `phpshop_modules`;
CREATE TABLE IF NOT EXISTS `phpshop_modules` (
  `path` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `date` int(11) DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  PRIMARY KEY (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules` (`path`, `name`, `date`, `servers`) VALUES
('returncall', 'Return Call', 1723119415, ''),
('visualcart', 'Visual Cart', 1723119415, ''),
('productday', 'Товар дня', 1723119415, ''),
('sticker', 'Sticker', 1723119415, ''),
('hit', 'Хиты', 1723119415, ''),
('oneclick', 'One Click', 1723119415, ''),
('seourlpro', 'SeoUrl', 1723119415, ''),
('yandexkassa', 'ЮKassa', 1723119415, ''),
('tinkoff', 'Т-Банк', 1723119415, '');

DROP TABLE IF EXISTS `phpshop_modules_hit_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_hit_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hit_main` int(11) NOT NULL DEFAULT '20',
  `hit_page` int(11) NOT NULL DEFAULT '3',
  `hit_cat` INT(11) NOT NULL default 0,
  `version` varchar(64) DEFAULT '1.0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_hit_system` (`id`, `hit_main`, `hit_page`, `version`) VALUES
(1, 20, 3, '1.1');

DROP TABLE IF EXISTS `phpshop_modules_key`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_key` (
  `path` varchar(64) NOT NULL DEFAULT '',
  `date` int(11) DEFAULT '0',
  `key` text,
  `verification` varchar(32) DEFAULT '',
  PRIMARY KEY (`path`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_oneclick_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_oneclick_jurnal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `name` varchar(64) DEFAULT '',
  `tel` varchar(64) DEFAULT '',
  `message` text,
  `product_name` varchar(64) DEFAULT '',
  `product_id` int(11) DEFAULT NULL,
  `product_price` varchar(64) DEFAULT '',
  `product_image` varchar(64) DEFAULT '',
  `ip` varchar(64) DEFAULT '',
  `status` enum('1','2','3','4') DEFAULT '1',
  `mail` varchar(64) default '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_oneclick_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_oneclick_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` enum('0','1','2') DEFAULT '1',
  `title` text,
  `title_end` text,
  `serial` varchar(64) DEFAULT '',
  `windows` enum('0','1') DEFAULT '0',
  `display` enum('0','1') DEFAULT '0',
  `write_order` enum('0','1') DEFAULT '0',
  `captcha` enum('0','1') DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `version` varchar(64) DEFAULT '1.0',
  `only_available` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_oneclick_system` (`id`, `enabled`, `title`, `title_end`, `serial`, `windows`, `display`, `write_order`, `captcha`, `status`, `version`, `only_available`) VALUES
(1, '0', 'Спасибо, Ваш заказ принят!', 'Наши менеджеры свяжутся с Вами для уточнения деталей.', '', '1', '0', '1', '1', 0, '1.9', '0');

DROP TABLE IF EXISTS `phpshop_modules_productday_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_productday_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(11) DEFAULT '0',
  `version` varchar(64) DEFAULT '1.1',
  `status` enum('1','2','3') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_productday_system` (`id`, `time`, `version`, `status`) VALUES
(1, 24, '1.3', '3');

DROP TABLE IF EXISTS `phpshop_modules_returncall_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_returncall_jurnal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `time_start` varchar(64) DEFAULT '',
  `time_end` varchar(64) DEFAULT '',
  `name` varchar(64) DEFAULT '',
  `tel` varchar(64) DEFAULT '',
  `message` text,
  `status` int(11) DEFAULT '1',
  `ip` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_returncall_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_returncall_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` enum('0','1','2') DEFAULT '1',
  `title` varchar(64) DEFAULT '',
  `title_end` text,
  `windows` enum('0','1') DEFAULT '1',
  `captcha_enabled` enum('1','2') DEFAULT '1',
  `status` int(11) DEFAULT '0',
  `version` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_returncall_system` (`id`, `enabled`, `title`, `title_end`, `windows`, `captcha_enabled`, `status`, `version`) VALUES
(1, '0', 'Обратный звонок', 'Спасибо! Мы скоро свяжемся с Вами.', '1', '1', 1, '1.7');

DROP TABLE IF EXISTS `phpshop_modules_seourlpro_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_seourlpro_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paginator` enum('1','2') DEFAULT '1',
  `seo_brands_enabled` enum('1','2') DEFAULT '1',
  `cat_content_enabled` enum('1','2') DEFAULT '1',
  `seo_news_enabled` enum('1','2') DEFAULT '1',
  `seo_page_enabled` enum('1','2') DEFAULT '1',
  `redirect_enabled` enum('1','2') DEFAULT '1',
  `html_enabled` enum('1','2') default '1',
  `version` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_seourlpro_system` (`id`, `paginator`, `seo_brands_enabled`, `cat_content_enabled`, `seo_news_enabled`, `seo_page_enabled`, `redirect_enabled`, `version`) VALUES
(1, '2', '2', '1', '2', '2', '1', '2.3');

DROP TABLE IF EXISTS `phpshop_modules_sticker_forms`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sticker_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `path` varchar(64) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `mail` varchar(64) NOT NULL DEFAULT '',
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  `dir` text NOT NULL,
  `skin` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_sticker_forms` (`id`, `name`, `path`, `content`, `mail`, `enabled`, `dir`, `skin`) VALUES
(1, 'Консультации', 'three', '<p class=\"icon\">&nbsp;</p>\r\n<h6 class=\"no-margin text-uppercase\">Помощь и консультации</h6>\r\n<p>поможем с выбором</p>', '', '1', '', 'hub'),
(2, 'Гарантия возврата денег', 'two', '<p class=\"icon\">&nbsp;</p>\r\n<h6 class=\"no-margin text-uppercase\">Гарантия возврата денег</h6>\r\n<p>правка слоганов в Модули - Стикеры</p>', '', '1', '', 'hub'),
(3, 'Бесплатная доставка', 'one', '<p class=\"icon\">&nbsp;</p>\r\n<h6 class=\"no-margin text-uppercase\">Бесплатная доставка</h6>\r\n<p>при заказе от 5000 руб.</p>', '', '1', '', 'hub'),
(19, 'Полоска над шапкой', 'top', 'Бесплатная доставка при заказе от 3000 рублей. <a href=\"/page/address.html\">Подробности</a>', '', '1', '', ''),
(10, 'Мы принимаем к оплате', 'pay', '<p><img src=\"/UserFiles/Image/trial/pay.png\" alt=\"Payment methods\" width=\"250\" height=\"25\" /></p>', '', '1', '', ''),
(15, 'Надежно', 'slogan1', '<div class=\"media\">\r\n<figure class=\"w-100 max-w-8rem mr-4\"><img class=\"img-fluid\" src=\"images/lock.png\" alt=\"\" /></figure>\r\n<div class=\"media-body\">\r\n<h4 class=\"mb-1\">Надежно</h4>\r\n<p class=\"font-size-1 mb-0\">Это слоган в меню Модули - Стикеры - Надежно.</p>\r\n</div>\r\n</div>', '', '1', '/', ''),
(16, '30 дней на возврат', 'slogan2', '<div class=\"media\">\r\n<figure class=\"w-100 max-w-8rem mr-4\"><img class=\"img-fluid\" src=\"images/refund.png\" alt=\"\" /></figure>\r\n<div class=\"media-body\">\r\n<h4 class=\"mb-1\">30 дней на возврат</h4>\r\n<p class=\"font-size-1 mb-0\">Это слоган в меню Модули - Стикеры - 30 дней на возврат.</p>\r\n</div>\r\n</div>', '', '1', '/', ''),
(17, 'Удобная доставка', 'slogan3', '<div class=\"media\">\r\n<figure class=\"w-100 max-w-8rem mr-4\"><img class=\"img-fluid\" src=\"images/delivery-man.png\" alt=\"\" /></figure>\r\n<div class=\"media-body\">\r\n<h4 class=\"mb-1\">Удобная доставка</h4>\r\n<p class=\"font-size-1 mb-0\">Это слоган в меню Модули - стикеры - Удобная доставка.</p>\r\n</div>\r\n</div>', '', '1', '/', ''),
(20, 'Баннер в меню', 'menu', '<div class=\"navbar-banner\">\r\n    <div class=\"navbar-banner-content\">\r\n        <div class=\"mb-4\">\r\n            <span class=\"d-block h2 text-white\">Брендинг Работает</span>\r\n            <p class=\"text-white\">Оцените уровень нашего качества как в области проектирования, так и в области кастомизации.</p>\r\n        </div>\r\n        <a class=\"btn btn-primary btn-sm transition-3d-hover\" href=\"#\">Узнать больше <i class=\"fas fa-angle-right fa-sm ml-1\"></i></a>\r\n    </div>\r\n</div>', '', '0', '', 'flow'),
(22, 'Доставка в карточке товара', 'accordion', ' <!-- Accordion -->\r\n      <div id=\"shopCartAccordionExample2\" class=\"accordion mb-5\">\r\n        <!-- Card -->\r\n        <div class=\"card card-bordered shadow-none\">\r\n          <div class=\"card-body card-collapse\" id=\"shopCardHeadingOne\">\r\n            <a class=\"btn btn-link btn-block card-btn collapsed\" href=\"javascript:;\" role=\"button\"\r\n                    data-toggle=\"collapse\"\r\n                    data-target=\"#shopCardOne\"\r\n                    aria-expanded=\"false\"\r\n                    aria-controls=\"shopCardOne\">\r\n              <span class=\"row align-items-center\">\r\n                <span class=\"col-9\">\r\n                  <span class=\"media align-items-center\">\r\n                    <span class=\"w-100 max-w-6rem mr-3\">\r\n                      <img class=\"img-fluid\" src=\"images/icon-65.svg\" alt=\"\">\r\n                    </span>\r\n                    <span class=\"media-body\">\r\n                      <span class=\"d-block font-size-1 font-weight-bold\">Доставка</span>\r\n                    </span>\r\n                  </span>\r\n                </span>\r\n                <span class=\"col-3 text-right\">\r\n                  <span class=\"card-btn-toggle\">\r\n                    <span class=\"card-btn-toggle-default\">&#43;</span>\r\n                    <span class=\"card-btn-toggle-active\">&#8722;</span>\r\n                  </span>\r\n                </span>\r\n              </span>\r\n            </a>\r\n          </div>\r\n          <div id=\"shopCardOne\" class=\"collapse\" aria-labelledby=\"shopCardHeadingOne\" data-parent=\"#shopCartAccordionExample2\">\r\n            <div class=\"card-body\">\r\n              <p class=\"small mb-0\">Это слоган в меню Модули - Стикеры - Доставка в карточке товара.</p>\r\n            </div>\r\n          </div>\r\n        </div>\r\n        <!-- End Card -->\r\n\r\n        <!-- Card -->\r\n        <div class=\"card card-bordered shadow-none\">\r\n          <div class=\"card-body card-collapse\" id=\"shopCardHeadingTwo\">\r\n            <a class=\"btn btn-link btn-block card-btn collapsed\" href=\"javascript:;\" role=\"button\"\r\n                    data-toggle=\"collapse\"\r\n                    data-target=\"#shopCardTwo\"\r\n                    aria-expanded=\"false\"\r\n                    aria-controls=\"shopCardTwo\">\r\n              <span class=\"row align-items-center\">\r\n                <span class=\"col-9\">\r\n                  <span class=\"media align-items-center\">\r\n                    <span class=\"w-100 max-w-6rem mr-3\">\r\n                      <img class=\"img-fluid\" src=\"images/icon-64.svg\" alt=\"\">\r\n                    </span>\r\n                    <span class=\"media-body\">\r\n                      <span class=\"d-block font-size-1 font-weight-bold\">Оплата</span>\r\n                    </span>\r\n                  </span>\r\n                </span>\r\n                <span class=\"col-3 text-right\">\r\n                  <span class=\"card-btn-toggle\">\r\n                    <span class=\"card-btn-toggle-default\">&#43;</span>\r\n                    <span class=\"card-btn-toggle-active\">&#8722;</span>\r\n                  </span>\r\n                </span>\r\n              </span>\r\n            </a>\r\n          </div>\r\n          <div id=\"shopCardTwo\" class=\"collapse\" aria-labelledby=\"shopCardHeadingTwo\" data-parent=\"#shopCartAccordionExample2\">\r\n            <div class=\"card-body\">\r\n              <p class=\"small mb-0\">Это слоган в меню Модули - Стикеры - Доставка в карточке товара.</p>\r\n            </div>\r\n          </div>\r\n        </div>\r\n        <!-- End Card -->\r\n      </div>\r\n      <!-- End Accordion -->', '', '1', '', 'flow');


DROP TABLE IF EXISTS `phpshop_modules_sticker_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_sticker_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial` varchar(64) NOT NULL DEFAULT '',
  `version` varchar(64) DEFAULT '1.0',
  `editor` enum('0','1') default '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_sticker_system` (`id`, `serial`, `version`,`editor`) VALUES
(1, '', '1.3','0');

DROP TABLE IF EXISTS `phpshop_modules_visualcart_log`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_visualcart_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `user` varchar(255) DEFAULT '',
  `ip` varchar(64) DEFAULT '',
  `status` enum('1','2') DEFAULT '1',
  `content` varchar(64) DEFAULT '',
  `num` tinyint(11) DEFAULT '0',
  `product_id` int(11) DEFAULT '0',
  `price` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_visualcart_memory`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_visualcart_memory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memory` varchar(64) DEFAULT '',
  `cart` text,
  `date` int(11) DEFAULT '0',
  `user` int(11) DEFAULT '0',
  `ip` varchar(64) DEFAULT '',
  `referal` text,
  `tel` varchar(64) DEFAULT NULL,
  `mail` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `sum` float DEFAULT NULL,
  `sendmail` enum('0','1') DEFAULT '0',
  `server` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_visualcart_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_visualcart_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enabled` enum('0','1','2') DEFAULT '1',
  `flag` enum('1','2') DEFAULT '1',
  `title` varchar(64) DEFAULT '',
  `pic_width` tinyint(100) DEFAULT '0',
  `memory` enum('0','1') DEFAULT '1',
  `nowbuy` enum('0','1') DEFAULT '1',
  `referal` enum('0','1') DEFAULT '0',
  `version` varchar(64) DEFAULT '2.5',
  `sendmail` int(11) DEFAULT '10',
  `day` INT(11) DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_visualcart_system` VALUES (1, '0', '1', 'Корзина', 50,'1','1','0','2.5','10','10');


DROP TABLE IF EXISTS `phpshop_payment_systems`;
CREATE TABLE IF NOT EXISTS `phpshop_payment_systems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `path` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '1',
  `num` tinyint(11) DEFAULT '0',
  `message` text,
  `message_header` text,
  `yur_data_flag` enum('0','1') DEFAULT '0',
  `icon` varchar(255) DEFAULT '',
  `color` varchar(64) DEFAULT '#000000',
  `servers` varchar(1000) DEFAULT '',
  `company` int(11) DEFAULT '0',
  `status` INT(11) DEFAULT '0',
  `sum_max` float DEFAULT '0',
  `sum_min` float DEFAULT '0',
  `discount_max` float DEFAULT '0',
  `discount_min` float DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`, `color`, `servers`, `company`) VALUES
(1, 'Оплата при получении', 'message', '1', 0, '<h3>Благодарим Вас за заказ!</h3>\n<p>В ближайшее время с Вами свяжется наш менеджер для уточнения деталей.</p>', '', '0', '/UserFiles/Image/trial/purse.png', '#000000', '', 0);

DROP TABLE IF EXISTS `phpshop_news`;
CREATE TABLE IF NOT EXISTS `phpshop_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datas` varchar(32) DEFAULT '',
  `zag` varchar(255) DEFAULT '',
  `kratko` text,
  `podrob` text,
  `datau` int(11) DEFAULT '0',
  `odnotip` text,
  `servers` varchar(1000) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `news_seo_name` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_newsletter`;
CREATE TABLE IF NOT EXISTS `phpshop_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `content` text,
  `template` int(11) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  `servers` INT(11) DEFAULT '0',
  `recipients` text NULL,
  `bot_message` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_notes`;
CREATE TABLE IF NOT EXISTS `phpshop_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT '0',
  `message` text,
  `status` int(11) DEFAULT '0',
  `name` varchar(64) DEFAULT NULL,
  `tel` varchar(64) DEFAULT NULL,
  `mail` varchar(64) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_notice`;
CREATE TABLE IF NOT EXISTS `phpshop_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `product_id` int(11) DEFAULT '0',
  `datas_start` varchar(64) DEFAULT '',
  `datas` varchar(64) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_orders`;
CREATE TABLE IF NOT EXISTS `phpshop_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datas` int(11) NOT NULL DEFAULT '0',
  `uid` varchar(64) DEFAULT '0',
  `orders` blob,
  `status` text,
  `user` int(11) UNSIGNED DEFAULT '0',
  `seller` enum('0','1') DEFAULT '0',
  `statusi` int(11) DEFAULT '0',
  `country` varchar(255) DEFAULT '',
  `state` varchar(255) DEFAULT '',
  `city` varchar(255) DEFAULT '',
  `index` varchar(255) DEFAULT '',
  `fio` varchar(255) DEFAULT '',
  `tel` varchar(255) DEFAULT '',
  `street` varchar(255) DEFAULT '',
  `house` varchar(255) DEFAULT '',
  `porch` varchar(255) DEFAULT '',
  `door_phone` varchar(255) DEFAULT '',
  `flat` varchar(255) DEFAULT '',
  `delivtime` varchar(255) DEFAULT '',
  `org_name` varchar(255) DEFAULT '',
  `org_inn` varchar(255) DEFAULT '',
  `org_kpp` varchar(255) DEFAULT '',
  `org_yur_adres` varchar(255) DEFAULT '',
  `org_fakt_adres` varchar(255) DEFAULT '',
  `org_ras` varchar(255) DEFAULT '',
  `org_bank` varchar(255) DEFAULT '',
  `org_kor` varchar(255) DEFAULT '',
  `org_bik` varchar(255) DEFAULT '',
  `org_city` varchar(255) DEFAULT '',
  `dop_info` text,
  `sum` float DEFAULT NULL,
  `files` text,
  `tracking` varchar(64) DEFAULT '',
  `admin` int(11) DEFAULT '0',
  `servers` int(11) DEFAULT '0',
  `paid` tinyint(1) DEFAULT NULL,
  `bonus_minus` int(11) DEFAULT '0',
  `bonus_plus` int(11) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  `company` int(11) DEFAULT '0',
  KEY `seller` (`seller`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_orders` (`id`, `datas`, `uid`, `orders`, `status`, `user`, `seller`, `statusi`, `country`, `state`, `city`, `index`, `fio`, `tel`, `street`, `house`, `porch`, `door_phone`, `flat`, `delivtime`, `org_name`, `org_inn`, `org_kpp`, `org_yur_adres`, `org_fakt_adres`, `org_ras`, `org_bank`, `org_kor`, `org_bik`, `org_city`, `dop_info`, `sum`, `files`, `tracking`, `admin`, `servers`, `paid`, `bonus_minus`, `bonus_plus`, `date`, `company`) VALUES
(1, 1631544463, '1-69', 0x613a323a7b733a343a2243617274223b613a353a7b733a343a2263617274223b613a323a7b693a31383b613a31333a7b733a323a226964223b733a323a223138223b733a343a226e616d65223b733a31373a22d2eee2e0f0312031323220e6e5ebf2fbe9223b733a353a227072696365223b733a353a223130303030223b733a373a2270726963655f6e223b733a313a2230223b733a31313a2270726963655f7075726368223b693a303b733a333a22756964223b4e3b733a333a226e756d223b693a373b733a363a2265645f697a6d223b4e3b733a393a227069635f736d616c6c223b733a34303a222f5573657246696c65732f496d6167652f747269616c2f7072696d65722d666f746f33732e6a7067223b733a363a22776569676874223b4e3b733a383a2263617465676f7279223b733a313a2235223b733a363a22706172656e74223b693a313b733a353a22746f74616c223b733a353a223730303030223b7d693a32303b613a31343a7b733a323a226964223b733a323a223230223b733a343a226e616d65223b733a31373a22d2eee2e0f0312031333420f7e5f0edfbe9223b733a353a227072696365223b733a353a223132303030223b733a373a2270726963655f6e223b733a313a2230223b733a31313a2270726963655f7075726368223b693a303b733a333a22756964223b4e3b733a333a226e756d223b693a313b733a363a2265645f697a6d223b4e3b733a393a227069635f736d616c6c223b733a33393a222f5573657246696c65732f496d6167652f747269616c2f7072696d65722d666f746f732e6a7067223b733a363a22776569676874223b4e3b733a383a2263617465676f7279223b733a313a2235223b733a363a22706172656e74223b693a313b733a31303a22706172656e745f756964223b733a373a223030302d303031223b733a353a22746f74616c223b733a353a223132303030223b7d7d733a333a226e756d223b693a383b733a333a2273756d223b733a353a223832303030223b733a363a22776569676874223b693a303b733a383a22646f737461766b61223b693a303b7d733a363a22506572736f6e223b613a393a7b733a343a226f756964223b733a343a22312d3639223b733a343a2264617461223b733a31303a2231363331353434343633223b733a343a2274696d65223b733a383a2231373a343320706d223b733a343a226d61696c223b733a31343a22636c69656e74406d61696c2e7275223b733a31313a226e616d655f706572736f6e223b733a343a22c8e2e0ed223b733a31343a22646f737461766b615f6d65746f64223b693a313b733a383a22646973636f756e74223b693a303b733a373a22757365725f6964223b693a313b733a31313a226f726465725f6d65746f64223b693a313b7d7d, 'a:2:{s:7:\"maneger\";N;s:4:\"time\";s:0:\"\";}', 1, '0', 0, '', '', '', '', 'Иван', '+7(926) 111-11-11', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 82000, NULL, '', 0, 0, NULL, 0, 0, 1631544463, 0),
(2, 1631546140, '2-70', 0x613a323a7b733a343a2243617274223b613a353a7b733a343a2263617274223b613a313a7b693a31333b613a31323a7b733a323a226964223b733a323a223133223b733a343a226e616d65223b733a373a22d2eee2e0f03133223b733a353a227072696365223b733a343a2232303030223b733a373a2270726963655f6e223b733a313a2230223b733a31313a2270726963655f7075726368223b693a303b733a333a22756964223b733a373a223030302d303133223b733a333a226e756d223b693a313b733a363a2265645f697a6d223b733a333a22f8f22e223b733a393a227069635f736d616c6c223b733a33393a222f5573657246696c65732f496d6167652f747269616c2f7072696d65722d666f746f732e6a7067223b733a363a22776569676874223b4e3b733a383a2263617465676f7279223b733a313a2239223b733a353a22746f74616c223b733a343a2232303030223b7d7d733a333a226e756d223b693a313b733a333a2273756d223b733a343a2232303030223b733a363a22776569676874223b693a303b733a383a22646f737461766b61223b693a303b7d733a363a22506572736f6e223b613a393a7b733a343a226f756964223b733a343a22322d3639223b733a343a2264617461223b733a31303a2231363331353436313430223b733a343a2274696d65223b733a383a2231383a343020706d223b733a343a226d61696c223b733a31343a22636c69656e74406d61696c2e7275223b733a31313a226e616d655f706572736f6e223b733a303a22223b733a31343a22646f737461766b615f6d65746f64223b693a313b733a383a22646973636f756e74223b693a303b733a373a22757365725f6964223b733a313a2231223b733a31313a226f726465725f6d65746f64223b693a31303030343b7d7d, 'a:2:{s:7:\"maneger\";N;s:4:\"time\";s:0:\"\";}', 1, '0', 0, '', '', '', '', 'Иван', '+7(926) 111-11-11', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 2000, NULL, '', 0, 0, NULL, 0, 0, 1631546140, 0),
(3, 1631547140, '3-71', 0x613a323a7b733a343a2243617274223b613a353a7b733a343a2263617274223b613a313a7b693a31333b613a31323a7b733a323a226964223b733a323a223133223b733a343a226e616d65223b733a373a22d2eee2e0f03133223b733a353a227072696365223b733a343a2232303030223b733a373a2270726963655f6e223b733a313a2230223b733a31313a2270726963655f7075726368223b693a303b733a333a22756964223b733a373a223030302d303133223b733a333a226e756d223b693a313b733a363a2265645f697a6d223b733a333a22f8f22e223b733a393a227069635f736d616c6c223b733a33393a222f5573657246696c65732f496d6167652f747269616c2f7072696d65722d666f746f732e6a7067223b733a363a22776569676874223b4e3b733a383a2263617465676f7279223b733a313a2239223b733a353a22746f74616c223b733a343a2232303030223b7d7d733a333a226e756d223b693a313b733a333a2273756d223b733a343a2232303030223b733a363a22776569676874223b693a303b733a383a22646f737461766b61223b693a303b7d733a363a22506572736f6e223b613a393a7b733a343a226f756964223b733a343a22322d3639223b733a343a2264617461223b733a31303a2231363331353436313430223b733a343a2274696d65223b733a383a2231383a343020706d223b733a343a226d61696c223b733a31343a22636c69656e74406d61696c2e7275223b733a31313a226e616d655f706572736f6e223b733a303a22223b733a31343a22646f737461766b615f6d65746f64223b693a313b733a383a22646973636f756e74223b693a303b733a373a22757365725f6964223b733a313a2231223b733a31313a226f726465725f6d65746f64223b693a31303030343b7d7d, 'a:2:{s:7:\"maneger\";N;s:4:\"time\";s:0:\"\";}', 1, '0', 0, '', '', '', '', 'Иван', '+7(926) 111-11-11', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 2000, NULL, '', 0, 0, NULL, 0, 0, 1631546140, 0);

DROP TABLE IF EXISTS `phpshop_order_status`;
CREATE TABLE IF NOT EXISTS `phpshop_order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '',
  `color` varchar(64) DEFAULT '',
  `sklad_action` enum('0','1') DEFAULT '0',
  `cumulative_action` enum('0','1') DEFAULT '0',
  `mail_action` enum('0','1') DEFAULT '1',
  `mail_message` text,
  `sms_action` enum('0','1') DEFAULT '0',
  `num` int(11) DEFAULT '0',
  `bot_action` enum('0','1') DEFAULT '0',
  `external_code` VARCHAR(64) NOT NULL,
  `bot_message` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_order_status` (`id`, `name`, `color`, `sklad_action`, `cumulative_action`, `mail_action`, `mail_message`, `sms_action`, `num`, `bot_action`) VALUES
(1, 'Аннулирован', 'red', '0', '0', '1', '', '0', 0, '0'),
(2, 'Доставляется', '#DA881C', '0', '0', '1', '', '0', 0, '0'),
(3, 'Выполнен', '#20ed41', '1', '1', '1', '', '0', 0, '0'),
(101, 'Оплачено платежными системами', '#20a9ed', '1', '1', '1', '', '0', 0, '0');

DROP TABLE IF EXISTS `phpshop_page`;
CREATE TABLE IF NOT EXISTS `phpshop_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `link` varchar(64) DEFAULT '',
  `category` int(11) DEFAULT '0',
  `keywords` text,
  `description` varchar(255) DEFAULT '',
  `content` text,
  `servers` varchar(1000) DEFAULT '',
  `num` smallint(3) DEFAULT '0',
  `datas` int(11) DEFAULT '0',
  `odnotip` text,
  `title` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '0',
  `secure` enum('0','1') DEFAULT '0',
  `secure_groups` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `preview` text,
  `footer` enum('0','1') DEFAULT '1',
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `link` (`link`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_page` (`id`, `name`, `link`, `category`, `keywords`, `description`, `content`, `servers`, `num`, `datas`, `odnotip`, `title`, `enabled`, `secure`, `secure_groups`, `icon`, `preview`, `footer`) VALUES
(1, 'О компании', 'o_kompanii', 1000, '', '', '', '', 3, 1630592817, '', '', '1', '0', '', '', '', '1'),
(2, 'Контакты', 'address', 1000, '', '', '<h3>Наш адрес:</h3>\n<p>@streetAddress@</p>\n<h3>Почта:</h3>\n<p><a href=\"mailto:@adminMail@\">@adminMail@</a></p>\n<h3>Телефон:</h3>\n<p><a href=\"tel:@telNumMobile@\">@telNumMobile@</a></p>\n<h3>Мы работаем:</h3>\n<p>10:00 - 18:00 пнд-птн <br /> субб вскр - выходной</p>\n<h3>Как нас найти:</h3>\n<p>Метро «Шоссе Энтузиастов», первый вагон из центра. После эскалатора первый выход направо на шоссе Энтузиастов. По шоссе Энтузиастов в сторону области 10 минут пешком до здания с зеленой вывеской на первом этаже «Банк «Далена». Вход со стороны банка. Далее звоните по контактным телефонам, чтобы вас встретили у входа.</p>\n<p><iframe src=\"https://yandex.ru/map-widget/v1/?um=constructor:055c7ef4dbce4860769e42eb3b7eefb5e095a818465716830ffd258a408d8bb2&source=constructor\" width=\"100%\" height=\"595\" frameborder=\"0\"></iframe></p>\n<p>Эта страница редактируется в меню Веб-сайт-Страницы. Как встроить такую карту на сайт, мы показали в <a href=\"https://docs.phpshop.ru/stranicy/content#kak-vstavit-kartu-proezda-na-sait\" target=\"_blank\" rel=\"noopener\">Учебнике</a>.</p>', '', 2, 1630572921, '', '', '1', '0', '', '', '', '1'),
(5, 'Статья1', 'statya1', 1, '', '', '<p>Современные технологии достигли такого уровня, что семантический разбор внешних противодействий влечет за собой процесс внедрения и модернизации направлений прогрессивного развития. Есть над чем задуматься: действия представителей оппозиции лишь добавляют фракционных разногласий и в равной степени предоставлены сами себе. Сделанные на базе интернет-аналитики выводы объявлены нарушающими общечеловеческие нормы этики и морали. Разнообразный и богатый опыт говорит нам, что понимание сути ресурсосберегающих технологий, в своём классическом представлении, допускает внедрение позиций, занимаемых участниками в отношении поставленных задач!</p>\n<p>Некоторые особенности внутренней политики в равной степени предоставлены сами себе. Активно развивающиеся страны третьего мира формируют глобальную экономическую сеть и при этом - разоблачены. Не следует, однако, забывать, что современная методология разработки прекрасно подходит для реализации экономической целесообразности принимаемых решений.</p>\n<p>Высокий уровень вовлечения представителей целевой аудитории является четким доказательством простого факта: высококачественный прототип будущего проекта создаёт предпосылки для форм воздействия. В рамках спецификации современных стандартов, акционеры крупнейших компаний и по сей день остаются уделом либералов, которые жаждут быть ассоциативно распределены по отраслям. Каждый из нас понимает очевидную вещь: постоянный количественный рост и сфера нашей активности предопределяет высокую востребованность соответствующих условий активизации. Повседневная практика показывает, что реализация намеченных плановых заданий однозначно фиксирует необходимость инновационных методов управления процессами. Элементы политического процесса, вне зависимости от их уровня, должны быть объективно рассмотрены соответствующими инстанциями. И нет сомнений, что реплицированные с зарубежных источников, современные исследования представляют собой не что иное, как квинтэссенцию победы маркетинга над разумом и должны быть объединены в целые кластеры себе подобных.</p>\n<p>Курс на социально-ориентированный национальный проект обеспечивает актуальность прогресса профессионального сообщества. Разнообразный и богатый опыт говорит нам, что социально-экономическое развитие однозначно фиксирует необходимость форм воздействия. В целом, конечно, реализация намеченных плановых заданий способствует повышению качества позиций, занимаемых участниками в отношении поставленных задач. Задача организации, в особенности же укрепление и развитие внутренней структуры предопределяет высокую востребованность кластеризации усилий.</p>\n<p>Есть над чем задуматься: предприниматели в сети интернет призывают нас к новым свершениям, которые, в свою очередь, должны быть превращены в посмешище, хотя само их существование приносит несомненную пользу обществу. Также как дальнейшее развитие различных форм деятельности предопределяет высокую востребованность поставленных обществом задач. Кстати, непосредственные участники технического прогресса могут быть в равной степени предоставлены сами себе. В своём стремлении повысить качество жизни, они забывают, что укрепление и развитие внутренней структуры создаёт необходимость включения в производственный план целого ряда внеочередных мероприятий с учётом комплекса дальнейших направлений развития. Но сложившаяся структура организации является качественно новой ступенью системы массового участия.</p>', '', 9, 1630583011, '', '', '1', '0', '', '/UserFiles/Image/trial/blog270x130.jpg', '<p>Современные технологии достигли такого уровня, что семантический разбор внешних противодействий влечет за собой процесс внедрения и модернизации направлений прогрессивного развития.</p>', '1'),
(3, 'Доставка', 'dostavka', 1000, '', '', '', '', 1, 1630576470, '', '', '1', '0', '', '', '', '1'),
(4, 'Главный заголовок магазина', 'index', 2000, '', '', '<p>Это текст, содержащий ключевые слова вашего магазина из раздела Веб-сайт - Страницы - Начальная страница. Текст должен хорошо читаться. Заголовок этой страницы уже отмечен тегом H1 в шаблонах, он будет восприниматься поисковиками, как важный. Остальные заголовки заполняются в меню Настройки - Seo заголовки.</p>', '', 1, 2147483647, '', '', '1', '0', '', '', '', '0'),
(6, 'Статья2', 'statya2', 1, '', '', '<p>Современные технологии достигли такого уровня, что консультация с широким активом создаёт предпосылки для форм воздействия. В своём стремлении улучшить пользовательский опыт мы упускаем, что базовые сценарии поведения пользователей представляют собой не что иное, как квинтэссенцию победы маркетинга над разумом и должны быть рассмотрены исключительно в разрезе маркетинговых и финансовых предпосылок. Предварительные выводы неутешительны: постоянный количественный рост и сфера нашей активности играет важную роль в формировании направлений прогрессивного развития. Есть над чем задуматься: ключевые особенности структуры проекта являются только методом политического участия и указаны как претенденты на роль ключевых факторов. Банальные, но неопровержимые выводы, а также предприниматели в сети интернет, превозмогая сложившуюся непростую экономическую ситуацию, разоблачены. Имеется спорная точка зрения, гласящая примерно следующее: диаграммы связей и по сей день остаются уделом либералов, которые жаждут быть ассоциативно распределены по отраслям.</p>\n<p>Есть над чем задуматься: ключевые особенности структуры проекта смешаны с не уникальными данными до степени совершенной неузнаваемости, из-за чего возрастает их статус бесполезности. Интерактивные прототипы и по сей день остаются уделом либералов, которые жаждут быть в равной степени предоставлены сами себе!</p>\n<p>Следует отметить, что укрепление и развитие внутренней структуры напрямую зависит от инновационных методов управления процессами. Мы вынуждены отталкиваться от того, что новая модель организационной деятельности напрямую зависит от как самодостаточных, так и внешне зависимых концептуальных решений.</p>\n<p>Повседневная практика показывает, что постоянный количественный рост и сфера нашей активности однозначно фиксирует необходимость системы массового участия. Но предприниматели в сети интернет, вне зависимости от их уровня, должны быть функционально разнесены на независимые элементы.</p>\n<p>Как уже неоднократно упомянуто, активно развивающиеся страны третьего мира освещают чрезвычайно интересные особенности картины в целом, однако конкретные выводы, разумеется, рассмотрены исключительно в разрезе маркетинговых и финансовых предпосылок. В рамках спецификации современных стандартов, некоторые особенности внутренней политики освещают чрезвычайно интересные особенности картины в целом, однако конкретные выводы, разумеется, рассмотрены исключительно в разрезе маркетинговых и финансовых предпосылок. Вот вам яркий пример современных тенденций - граница обучения кадров требует определения и уточнения существующих финансовых и административных условий. Есть над чем задуматься: базовые сценарии поведения пользователей, инициированные исключительно синтетически, объективно рассмотрены соответствующими инстанциями. Есть над чем задуматься: стремящиеся вытеснить традиционное производство, нанотехнологии набирают популярность среди определенных слоев населения, а значит, должны быть ассоциативно распределены по отраслям.</p>', '', 2, 1630584176, '', '', '1', '0', '', '/UserFiles/Image/trial/blog270x130.jpg', '<p>Есть над чем задуматься: ключевые особенности структуры проекта смешаны с не уникальными данными до степени совершенной неузнаваемости, из-за чего возрастает их статус бесполезности. </p>', '0'),
(7, 'Статья3', 'statya3', 2, '', '', '<p>Учитывая ключевые сценарии поведения, сплочённость команды профессионалов играет определяющее значение для глубокомысленных рассуждений. Вот вам яркий пример современных тенденций - курс на социально-ориентированный национальный проект представляет собой интересный эксперимент проверки существующих финансовых и административных условий. Идейные соображения высшего порядка, а также современная методология разработки не оставляет шанса для поэтапного и последовательного развития общества. Следует отметить, что внедрение современных методик предопределяет высокую востребованность первоочередных требований.</p>\n<p>Задача организации, в особенности же реализация намеченных плановых заданий, в своём классическом представлении, допускает внедрение переосмысления внешнеэкономических политик. Разнообразный и богатый опыт говорит нам, что синтетическое тестирование выявляет срочную потребность соответствующих условий активизации. Противоположная точка зрения подразумевает, что ключевые особенности структуры проекта ассоциативно распределены по отраслям. Базовые сценарии поведения пользователей, инициированные исключительно синтетически, обнародованы. Но некоторые особенности внутренней политики представляют собой не что иное, как квинтэссенцию победы маркетинга над разумом и должны быть разоблачены. Каждый из нас понимает очевидную вещь: семантический разбор внешних противодействий играет определяющее значение для поэтапного и последовательного развития общества.</p>\n<p>Как принято считать, стремящиеся вытеснить традиционное производство, нанотехнологии указаны как претенденты на роль ключевых факторов. Сложившаяся структура организации однозначно фиксирует необходимость первоочередных требований.</p>', '', 1, 1630582983, '1,2,3', '', '1', '0', '', '/UserFiles/Image/trial/blog270x130.jpg', '<p>Следует отметить, что внедрение современных методик предопределяет высокую востребованность первоочередных требований.</p>', '0'),
(8, 'Статья4', 'statya4', 2, '', '', '<p>Учитывая ключевые сценарии поведения, сплочённость команды профессионалов играет определяющее значение для глубокомысленных рассуждений. Вот вам яркий пример современных тенденций - курс на социально-ориентированный национальный проект представляет собой интересный эксперимент проверки существующих финансовых и административных условий. Идейные соображения высшего порядка, а также современная методология разработки не оставляет шанса для поэтапного и последовательного развития общества. Следует отметить, что внедрение современных методик предопределяет высокую востребованность первоочередных требований.</p>\r\n<p>Задача организации, в особенности же реализация намеченных плановых заданий, в своём классическом представлении, допускает внедрение переосмысления внешнеэкономических политик. Разнообразный и богатый опыт говорит нам, что синтетическое тестирование выявляет срочную потребность соответствующих условий активизации. Противоположная точка зрения подразумевает, что ключевые особенности структуры проекта ассоциативно распределены по отраслям. Базовые сценарии поведения пользователей, инициированные исключительно синтетически, обнародованы. Но некоторые особенности внутренней политики представляют собой не что иное, как квинтэссенцию победы маркетинга над разумом и должны быть разоблачены. Каждый из нас понимает очевидную вещь: семантический разбор внешних противодействий играет определяющее значение для поэтапного и последовательного развития общества.</p>\r\n<p>Банальные, но неопровержимые выводы, а также интерактивные прототипы, инициированные исключительно синтетически, смешаны с не уникальными данными до степени совершенной неузнаваемости, из-за чего возрастает их статус бесполезности. Являясь всего лишь частью общей картины, реплицированные с зарубежных источников, современные исследования призывают нас к новым свершениям, которые, в свою очередь, должны быть превращены в посмешище, хотя само их существование приносит несомненную пользу обществу.</p>\r\n<p>И нет сомнений, что явные признаки победы институционализации освещают чрезвычайно интересные особенности картины в целом, однако конкретные выводы, разумеется, обнародованы. Предварительные выводы неутешительны: высокотехнологичная концепция общественного уклада предоставляет широкие возможности для кластеризации усилий. Есть над чем задуматься: ключевые особенности структуры проекта, превозмогая сложившуюся непростую экономическую ситуацию, функционально разнесены на независимые элементы! Предварительные выводы неутешительны: сложившаяся структура организации является качественно новой ступенью укрепления моральных ценностей.</p>\r\n<p>Как принято считать, стремящиеся вытеснить традиционное производство, нанотехнологии указаны как претенденты на роль ключевых факторов. Сложившаяся структура организации однозначно фиксирует необходимость первоочередных требований.</p>', '', 2, 1630592890, '', '', '1', '0', '', '', '', '0'),
(9, 'Согласие на обработку персональных данных', 'soglasie_na_obrabotku_personalnyh_dannyh', 0, '', '', '<p>Согласие на обработку персональных данных</p>\r\n<p>Настоящим я, далее &laquo;Субъект Персональных Данных&raquo;, во исполнение требований Федерального закона от 27.07.2006 г. 152-ФЗ &laquo;О персональных данных&raquo; (с изменениями и дополнениями) свободно, своей волей и в своем интересе даю свое согласие&nbsp;<strong>Индивидуальном предпринимателю Иванову Ивану Ивановичу</strong>&nbsp;(далее &laquo;Интернет-магазин&raquo;, адрес:&nbsp;<strong>(тут ваш адрес)&nbsp;</strong>) на обработку своих персональных данных, указанных при регистрации путем заполнения веб-формы на сайте Интернет-магазина&nbsp;<strong>вашдомен.ру</strong>&nbsp;и его поддоменов&nbsp;<strong>*.вашдомен.ру</strong>&nbsp;(далее Сайт), направляемой (заполненной) с использованием Сайта.</p>\r\n<p>Под персональными данными я понимаю любую информацию, относящуюся ко мне как к Субъекту Персональных Данных, в том числе мои фамилию, имя, отчество, адрес, образование, профессию, контактные данные (телефон, факс, электронная почта, почтовый адрес), фотографии,&nbsp; иную другую информацию. Под обработкой персональных данных я понимаю сбор, систематизацию, накопление, уточнение, обновление, изменение, использование, распространение, передачу, в том числе трансграничную, обезличивание, блокирование, уничтожение, бессрочное хранение), и любые другие действия (операции) с персональными данными.</p>\r\n<p>Обработка персональных данных Субъекта Персональных Данных осуществляется исключительно в целях регистрации Субъекта Персональных Данных в базе данных Интернет-магазина с последующим направлением Субъекту Персональных Данных почтовых сообщений и смс-уведомлений, в том числе рекламного содержания, от Интернет-магазина, его аффилированных лиц и/или субподрядчиков, информационных и новостных рассылок,&nbsp; приглашений на мероприятия Интернет-магазина и другой информации рекламно-новостного содержания, а также с целью подтверждения личности Субъекта Персональных Данных при посещении мероприятий Интернет-магазина.</p>\r\n<p>Датой выдачи согласия на обработку персональных данных Субъекта Персональных Данных является дата отправки регистрационной веб-формы с Сайта Интернет-магазина.</p>\r\n<p>Обработка персональных данных Субъекта Персональных Данных может осуществляться с помощью средств автоматизации и/или без использования средств автоматизации в соответствии с действующим законодательством РФ и внутренними положениями Интернет-магазина.</p>\r\n<p>Интернет-магазин принимает необходимые правовые, организационные и технические меры или обеспечивает их принятие для защиты персональных данных от неправомерного или случайного доступа к ним, уничтожения, изменения, блокирования, копирования, предоставления, распространения персональных данных, а также от иных неправомерных действий в отношении персональных данных, а также принимает на себя обязательство сохранения конфиденциальности персональных данных Субъекта Персональных Данных. Интернет-магазин вправе привлекать для обработки персональных данных Субъекта Персональных Данных субподрядчиков, а также вправе передавать персональные данные для обработки своим аффилированным лицам, обеспечивая при этом принятие такими субподрядчиками и аффилированными лицами соответствующих обязательств в части конфиденциальности персональных данных.</p>\r\n<p>Я ознакомлен(а), что:</p>\r\n<ol>\r\n<li>настоящее согласие на обработку моих персональных данных, указанных при регистрации на Сайте Интернет-магазина, направляемых (заполненных) с использованием Cайта, действует в течение 20 (двадцати) лет с момента регистрации на Cайте Интернет-магазина;</li>\r\n<li>согласие может быть отозвано мною на основании письменного заявления в произвольной форме;</li>\r\n<li>предоставление персональных данных третьих лиц без их согласия влечет ответственность в соответствии с действующим законодательством Российской Федерации.</li>\r\n</ol>', '', 11, 0, '', '', '1', '0', '', '', '', '0'),
(10, 'Условия оферты при оплате Visa и Mastercard', 'agreement', 0, '', '', '', '', 1, 1630653714, '', '', '1', '0', '', '', '', '0'),
(11, 'Политика конфиденциальности', 'politika_konfidencialnosti', 0, '', '', '<h2>Политика конфиденциальности для интернет-магазина</h2>\r\n<p>В примере жирным я выделил то, что вам надо подправить для своего проекта.</p>\r\n<ol>\r\n<li>ОПРЕДЕЛЕНИЕ ТЕРМИНОВ\r\n<ol>\r\n<li>Существующая на текущий момент политика конфиденциальности персональных данных (далее Политика конфиденциальности) работает со следующими понятиями:\r\n<ol>\r\n<li>&laquo;Администрация сайта Интернет-магазина (далее Администрация сайта)&raquo;. Так называют представляющих интересы организации специалистов, в чьи обязанности входит управление сайтом, то есть организация и (или) обработка поступивших на него персональных данных. Для выполнения этих обязанностей они должны чётко представлять, для чего обрабатываются сведения, какие сведения должна быть обработаны, какие действия (операции) должны производиться с полученными сведениями.</li>\r\n<li>&laquo;Персональные данные&raquo; сведения, имеющие прямое или косвенное отношение к определённому либо определяемому физическому лицу (также называемому субъектом персональных данных).</li>\r\n<li>&laquo;Обработка персональных данных&raquo; любая операция (действие) либо совокупность таковых, которые Администрация производит с персональными данными. Их могут собирать, записывать, систематизировать, накапливать, хранить, уточнять (при необходимости обновлять или изменять), извлекать, использовать, передавать (распространять, предоставлять, открывать к ним доступ), обезличивать, блокировать, удалять и даже уничтожать. Данные операции (действия) могут выполняться как автоматически, так и вручную.</li>\r\n<li>&laquo;Конфиденциальность персональных данных&raquo; обязательное требование, предъявляемое к Оператору или иному работающему с данными Пользователя должностному лицу, хранить полученные сведения в тайне, не посвящая в них посторонних, если предоставивший персональные данные Пользователь не изъявил своё согласие, а также отсутствует законное основание для разглашения.</li>\r\n<li>&laquo;Пользователь сайта Интернет-магазина&raquo; (далее Пользователь)&raquo; человек, посетивший сайт Интернет-магазина, а также пользующийся его программами и продуктами.</li>\r\n<li>&laquo;Cookies&raquo; короткий фрагмент данных, пересылаемый веб-браузером или веб-клиентом веб-серверу в HTTP-запросе, всякий раз, когда Пользователь пытается открыть страницу Интернет-магазина. Фрагмент хранится на компьютере Пользователя.</li>\r\n<li>&laquo;IP-адрес&raquo; уникальный сетевой адрес узла в компьютерной сети, построенной по протоколу TCP/IP.</li>\r\n</ol>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>ОБЩИЕ ПОЛОЖЕНИЯ\r\n<ol>\r\n<li>Просмотр сайта Интернет-магазина, а также использование его программ и продуктов подразумевают автоматическое согласие с принятой там Политикой конфиденциальности, подразумевающей предоставление Пользователем персональных данных на обработку.</li>\r\n<li>Если Пользователь не принимает существующую Политику конфиденциальности, Пользователь должен покинуть Интернет-магазин.</li>\r\n<li>Имеющаяся Политика конфиденциальности распространяется только на сайт Интернет-магазина. Если по ссылкам, размещённым на сайте последнего, Пользователь зайдёт на ресурсы третьих лиц, Интернет-магазин за его действия ответственности не несёт.</li>\r\n<li>Проверка достоверности персональных данных, которые решил сообщить принявший Политику конфиденциальности Пользователь, не входит в обязанности Администрации сайта.</li>\r\n</ol>\r\n</li>\r\n<li>ПРЕДМЕТ ПОЛИТИКИ КОНФИДЕНЦИАЛЬНОСТИ\r\n<ol>\r\n<li>Согласно проводимой в текущий период Политике конфиденциальности Администрация Интернет-магазина обязана не разглашать персональные данные, сообщаемые Пользователями, регистрирующимися на сайте или оформляющими заказ на покупку товара, а также обеспечивать этим данным абсолютную конфиденциальность.</li>\r\n<li>Чтобы сообщить персональные данные, Пользователь заполняет расположенные на сайте интернет-магазина электронные формы. Персональными данными Пользователя, которые подлежат обработке, являются:\r\n<ol>\r\n<li>его фамилия, имя, отчество;</li>\r\n<li>его контактный телефон;</li>\r\n<li>его электронный адрес (e-mail);</li>\r\n<li>адрес, по которому должен быть доставлен купленный им товар;</li>\r\n<li>адрес проживания Пользователя.</li>\r\n</ol>\r\n</li>\r\n<li>Защита данных, автоматически передаваемых при просмотре рекламных блоков и посещении страниц с установленными на них статистическими скриптами системы (пикселями) осуществляется Интернет-магазином. Вот перечень этих данных:<br />IP-адрес;<br />сведения из cookies;<br />сведения о браузере (либо другой программе, через которую становится доступен показ рекламы);<br />время посещения сайта;<br />адрес страницы, на которой располагается рекламный блок;<br />реферер (адрес предыдущей страницы).</li>\r\n<li>Последствием отключения cookies может стать невозможность доступа к требующим авторизации частям сайта Интернет-магазина.</li>\r\n<li>Интернет-магазин собирает статистику об IP-адресах всех посетителей. Данные сведения нужны, чтобы выявить и решить технические проблемы и проконтролировать, насколько законным будет проведение финансовых платежей.</li>\r\n<li>Любые другие неоговорённые выше персональные сведения (о том, когда и какие покупки были сделаны, какой при этом использовался браузер, какая была установлена операционная система и пр.) надёжно хранятся и не распространяются. Исключение существующая Политика конфиденциальности предусматривает для случаев, описанных в п.п. 5.2 и 5.3.</li>\r\n</ol>\r\n</li>\r\n<li>ЦЕЛИ СБОРА ПЕРСОНАЛЬНОЙ ИНФОРМАЦИИ ПОЛЬЗОВАТЕЛЯ\r\n<ol>\r\n<li>Сбор персональных данных Пользователя Администрацией Интернет-магазина проводится ради того, чтобы:\r\n<ol>\r\n<li>Идентифицировать Пользователя, который прошёл процедуру регистрации на сайте Интернет-магазина, чтобы оформить заказ и (или) приобрести товар данного магазина дистанционно.</li>\r\n<li>Открыть Пользователю доступ к персонализированным ресурсам данного сайта.</li>\r\n<li>Установить с Пользователем обратную связь, под которой подразумевается, в частности, рассылка запросов и уведомлений, касающихся использования сайта Интернет-магазина, обработка пользовательских запросов и заявок, оказание прочих услуг.</li>\r\n<li>Определить местонахождение Пользователя, чтобы обеспечить безопасность платежей и предотвратить мошенничество.</li>\r\n<li>Подтвердить, что данные, которые предоставил Пользователь, полны и достоверны.</li>\r\n<li>Создать учётную запись для совершения Покупок, если Пользователь изъявил на то своё желание.</li>\r\n<li>Уведомить Пользователя о состоянии его заказа в Интернет-магазине.</li>\r\n<li>Обрабатывать и получать платежи, подтверждать налог или налоговые льготы, оспаривать платёж, определять, целесообразно ли предоставить конкретному Пользователю кредитную линию.</li>\r\n<li>Обеспечить Пользователю максимально быстрое решение проблем, встречающихся при использовании Интернет-магазина, за счёт эффективной клиентской и технической поддержки.</li>\r\n<li>Своевременно информировать Пользователя об обновлённой продукции, ознакомлять его с уникальными предложениями, новыми прайсами, новостями о деятельности Интернет-магазина или его партнёров и прочими сведениями, если Пользователь изъявит на то своё согласие.</li>\r\n<li>Рекламировать товары Интернет-магазина, если Пользователь изъявит на то своё согласие.</li>\r\n<li>Предоставить Пользователю доступ на сайты или сервисы Интернет-магазина, помогая ему тем самым получать продукты, обновления и услуги.</li>\r\n</ol>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>СПОСОБЫ И СРОКИ ОБРАБОТКИ ПЕРСОНАЛЬНОЙ ИНФОРМАЦИИ\r\n<ol>\r\n<li>Срок обработки персональных данных Пользователя ничем не ограничен. Процедура обработки может проводиться любым предусмотренным законодательством способом. В частности, с помощью информационных&nbsp;систем&nbsp;персональных данных, которые могут вестись автоматически либо без средств автоматизации.</li>\r\n<li>Обработанные Администрацией сайта персональные данные Пользователя могут передаваться третьим лицам, в число которых входят курьерские службы, организации почтовой связи, операторы электросвязи. Делается это для того, чтобы выполнить заказ Пользователя, оставленный им на сайте Интернет-магазина, и доставить товар по адресу. Согласие Пользователя на подобную передачу предусмотрено правилами политики сайта.</li>\r\n<li>Также обработанные Администрацией сайта персональные данные могут передаваться уполномоченным органов государственной власти Российской Федерации, если это осуществляется на законных основаниях и в предусмотренном российским законодательством порядке.</li>\r\n<li>Если персональные данные будут утрачены или разглашены, Пользователь уведомляется об этом Администрацией сайта.</li>\r\n<li>Все действия Администрации сайта направлены на то, чтобы не допустить к персональным данным Пользователя третьих лиц (за исключением п.п. 5.2, 5.3). Последним эта информация не должна быть доступна даже случайно, дабы те не уничтожили её, не изменили и не блокировали, не копировали и не распространяли, а также не совершали прочие противозаконные действия. Для защиты пользовательских данных Администрация располагает комплексом организационных и технических мер.</li>\r\n<li>Если персональные данные будут утрачены либо разглашены, Администрация сайта совместно с Пользователем готова принять все возможные меры, дабы предотвратить убытки и прочие негативные последствия, вызванные данной ситуацией.</li>\r\n</ol>\r\n</li>\r\n<li>ОБЯЗАТЕЛЬСТВА СТОРОН\r\n<ol>\r\n<li>В обязанности Пользователя входит:\r\n<ol>\r\n<li>Сообщение соответствующих требованиям Интернет-магазина сведений о себе.</li>\r\n<li>Обновление и дополнение предоставляемых им сведений в случае изменения таковых.</li>\r\n</ol>\r\n</li>\r\n<li>В обязанности Администрации сайта входит:\r\n<ol>\r\n<li>Применение полученных сведений исключительно в целях, обозначенных в п. 4 существующей Политики конфиденциальности.</li>\r\n<li>Обеспечение конфиденциальности поступивших от Пользователя сведений. Они не должны разглашаться, если Пользователь не даст на то письменное разрешение. Также Администрация не имеет права продавать, обменивать, публиковать либо разглашать прочими способами переданные Пользователем персональные данные, исключая п.п. 5.2 и 5.3 существующей Политики конфиденциальности.</li>\r\n<li>Принятие мер предосторожности, дабы персональные данные Пользователя оставались строго конфиденциальными, точно также, как остаются конфиденциальными такого рода сведения в современном деловом обороте.</li>\r\n<li>Блокировка персональных пользовательских данных с того момента, с которого Пользователь либо его законный представитель сделает соответствующий запрос. Право сделать запрос на блокировку также предоставляется органу, уполномоченному защищать права Пользователя, предоставившего Администрации сайта свои данные, на период проверки, в случае обнаружения недостоверности сообщённых персональных данных либо неправомерности действий.</li>\r\n</ol>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>ОТВЕТСТВЕННОСТЬ СТОРОН\r\n<ol>\r\n<li>В случае неисполнения Администрацией сайта собственных обязательств и, как следствие, убытков Пользователя, понесённых из-за неправомерного использования предоставленной им информации, ответственность возлагается на неё. Об этом, в частности, утверждает российское законодательство. Исключение существующая в настоящее время Политика конфиденциальности делает для случаев, отражённых в п.п. 5.2, 5.3 и 7.2.</li>\r\n<li>Но существует ряд случаев, когда Администрация сайта ответственности не несёт, если пользовательские данные утрачиваются или разглашаются. Это происходит тогда, когда они:\r\n<ol>\r\n<li>Превратились в достояние общественности до того, как были утрачены или разглашены.</li>\r\n<li>Были предоставлены третьими лицами до того, как их получила Администрация сайта.</li>\r\n<li>Разглашались с согласия Пользователя.</li>\r\n</ol>\r\n</li>\r\n</ol>\r\n</li>\r\n<li>РАЗРЕШЕНИЕ СПОРОВ\r\n<ol>\r\n<li>Если Пользователь недоволен действиями Администрации Интернет-магазина и намерен отстаивать свои права в суде, до того как обратиться с иском, он в обязательном порядке должен предъявить претензию (письменно предложить урегулировать конфликт добровольно).</li>\r\n<li>Получившая претензию Администрация обязана в течение 30 календарных дней с даты её получения письменно уведомить Пользователя о её рассмотрении и принятых мерах.</li>\r\n<li>Если обе стороны так и не смогли договориться, спор передаётся в судебный орган, где его должны рассмотреть согласно действующему российскому законодательству.</li>\r\n<li>Регулирование отношений Пользователя и Администрации сайта в Политике конфиденциальности проводится согласно действующему российскому законодательству.</li>\r\n</ol>\r\n</li>\r\n<li>ДОПОЛНИТЕЛЬНЫЕ УСЛОВИЯ\r\n<ol>\r\n<li>Администрация сайта вправе менять существующую на текущий момент Политику конфиденциальности, не спрашивая согласия у Пользователя.</li>\r\n<li>Вступление в силу новой Политики конфиденциальности начинается после того, как информация о ней будет выложена на сайт Интернет-магазина, если изменившаяся Политика не подразумевает иного варианта размещения.</li>\r\n<li>&nbsp;Все предложения, пожелания, требования или вопросы по настоящей Политике конфиденциальности следует сообщать в раздел обратной связи, расположенный по адресу:&nbsp;<strong>(ссылка)</strong>. Или путем отправки электронного письма по адресу&nbsp;<strong>(тут ваш email)</strong></li>\r\n<li>Прочитать о существующей Политике конфиденциальности можно, зайдя на страницу по&nbsp;<strong>адресу www.адрес магазина.ru</strong></li>\r\n</ol>\r\n</li>\r\n</ol>', '', 10, 0, '', '', '1', '0', '', '', '', '0');

DROP TABLE IF EXISTS `phpshop_page_categories`;
CREATE TABLE IF NOT EXISTS `phpshop_page_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `num` int(64) DEFAULT '1',
  `parent_to` int(11) DEFAULT '0',
  `content` text,
  `servers` varchar(1000) DEFAULT '',
  `menu` enum('0','1') DEFAULT '0',
  `page_cat_seo_name` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `keywords` text,
  PRIMARY KEY (`id`),
  KEY `parent_to` (`parent_to`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_page_categories` (`id`, `name`, `num`, `parent_to`, `content`, `servers`, `menu`, `page_cat_seo_name`, `icon`, `title`, `description`, `keywords`) VALUES
(1, 'Темы1', 1, 0, '', '', '0', 'temy1', '/UserFiles/Image/trial/blog270x130.jpg', '', '', ''),
(2, 'Темы2', 2, 0, '', '', '0', 'temy2', '/UserFiles/Image/trial/blog270x130.jpg', '', '', '');

DROP TABLE IF EXISTS `phpshop_parent_name`;
CREATE TABLE IF NOT EXISTS `phpshop_parent_name` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  `color` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_payment`;
CREATE TABLE IF NOT EXISTS `phpshop_payment` (
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `sum` float DEFAULT '0',
  `datas` int(11) DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `order` (`uid`)
) ;


DROP TABLE IF EXISTS `phpshop_photo`;
CREATE TABLE IF NOT EXISTS `phpshop_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` int(11) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '0',
  `name` varchar(64) DEFAULT '',
  `num` tinyint(11) DEFAULT '0',
  `info` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parent` (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_photo_categories`;
CREATE TABLE IF NOT EXISTS `phpshop_photo_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_to` int(11) DEFAULT '0',
  `link` varchar(64) DEFAULT '',
  `name` varchar(64) DEFAULT '',
  `num` tinyint(11) DEFAULT '0',
  `content` text,
  `enabled` enum('0','1') DEFAULT '0',
  `page` varchar(255) DEFAULT '',
  `count` tinyint(11) DEFAULT '2',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_products`;
CREATE TABLE IF NOT EXISTS `phpshop_products` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `description` text,
  `content` text,
  `price` float DEFAULT '0',
  `price_n` float DEFAULT '0',
  `sklad` enum('0','1') DEFAULT '0',
  `p_enabled` enum('0','1') DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '1',
  `uid` varchar(64) DEFAULT '',
  `spec` enum('0','1') DEFAULT '0',
  `odnotip` varchar(255) DEFAULT '',
  `vendor` varchar(1000) DEFAULT '',
  `vendor_array` blob,
  `yml` enum('0','1') DEFAULT '0',
  `num` int(11) DEFAULT '1',
  `newtip` enum('0','1') DEFAULT '0',
  `title` varchar(255) DEFAULT '',
  `title_enabled` enum('0','1','2') DEFAULT '0',
  `datas` int(11) DEFAULT '0',
  `page` varchar(255) DEFAULT '',
  `user` tinyint(11) DEFAULT '0',
  `descrip` varchar(255) DEFAULT '',
  `descrip_enabled` enum('0','1','2') DEFAULT '0',
  `title_shablon` varchar(255) DEFAULT '',
  `descrip_shablon` varchar(255) DEFAULT '',
  `keywords` varchar(255) DEFAULT '',
  `keywords_enabled` enum('0','1','2') DEFAULT '0',
  `keywords_shablon` varchar(255) DEFAULT '',
  `pic_small` varchar(255) DEFAULT '',
  `pic_big` varchar(255) DEFAULT '',
  `yml_bid_array` tinyblob,
  `parent_enabled` enum('0','1') DEFAULT '0',
  `parent` text,
  `items` int(11) DEFAULT '0',
  `weight` float DEFAULT '0',
  `price2` float DEFAULT '0',
  `price3` float DEFAULT '0',
  `price4` float DEFAULT '0',
  `price5` float DEFAULT '0',
  `files` text,
  `baseinputvaluta` int(11) DEFAULT '0',
  `ed_izm` varchar(255) DEFAULT '',
  `dop_cat` varchar(255) DEFAULT '',
  `rate` float UNSIGNED DEFAULT '0',
  `rate_count` int(10) UNSIGNED DEFAULT '0',
  `price_search` float DEFAULT '0',
  `parent2` text,
  `color` varchar(64) DEFAULT NULL,
  `vendor_code` varchar(255) DEFAULT '',
  `vendor_name` varchar(255) DEFAULT '',
  `productday` enum('0','1') DEFAULT '0',
  `hit` enum('0','1') DEFAULT '0',
  `prod_seo_name` varchar(255) DEFAULT '',
  `prod_seo_name_old` varchar(255) DEFAULT '',
  `length` varchar(64) DEFAULT '',
  `width` varchar(64) DEFAULT '',
  `height` varchar(64) DEFAULT '',
  `price_purch` float DEFAULT '0',
  `yandex_vat_code` int(11) default 0,
  `external_code` varchar(64) DEFAULT '',
  `type` enum('1','2') DEFAULT '1',
  `import_id` VARCHAR(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `enabled` (`enabled`),
  KEY `uid` (`uid`),
  KEY `vendor` (`vendor`),
  KEY `external_code` (`external_code`),
  KEY `spec` (`spec`),
  KEY `newtip` (`newtip`),
  KEY `yml` (`yml`),
  KEY `parent_enabled` (`parent_enabled`),
  KEY `sklad` (`sklad`),
  KEY `dop_cat` (`dop_cat`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_products` (`id`, `category`, `name`, `description`, `content`, `price`, `price_n`, `sklad`, `p_enabled`, `enabled`, `uid`, `spec`, `odnotip`, `vendor`, `vendor_array`, `yml`, `num`, `newtip`, `title`, `title_enabled`, `datas`, `page`, `user`, `descrip`, `descrip_enabled`, `title_shablon`, `descrip_shablon`, `keywords`, `keywords_enabled`, `keywords_shablon`, `pic_small`, `pic_big`, `yml_bid_array`, `parent_enabled`, `parent`, `items`, `weight`, `price2`, `price3`, `price4`, `price5`, `files`, `baseinputvaluta`, `ed_izm`, `dop_cat`, `rate`, `rate_count`, `price_search`, `parent2`, `color`, `vendor_code`, `vendor_name`, `productday`, `hit`, `prod_seo_name`, `prod_seo_name_old`, `length`, `width`, `height`, `price_purch`) VALUES
(1, 8, 'Товар с подтипами1', '', '', 8000, 0, '0', '1', '1', '000-001', '1', '', 'i6-9ii4-1i', 0x613a323a7b693a363b613a313a7b693a303b733a313a2239223b7d693a343b613a313a7b693a303b733a313a2231223b7d7d, '1', 0, '1', '', '0', 1634645649, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', '20,18,17', 80, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 4, 2, 0, NULL, NULL, '', '', '0', '0', 'tovar1', '', '', '', '', 0),
(2, 5, 'Товар2', '', '', 7500, 0, '0', '1', '1', '000-002', '1', '', 'i7-15ii4-7i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223135223b7d693a343b613a313a7b693a303b733a313a2237223b7d7d, '1', 0, '1', '', '0', 1634714968, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 75, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar2', '', '', '', '', 0),
(3, 5, 'Товар3', '', '', 7000, 0, '0', '1', '1', '000-003', '1', '', 'i7-16ii4-8i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223136223b7d693a343b613a313a7b693a303b733a313a2238223b7d7d, '1', 0, '1', '', '0', 1634714974, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 70, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar3', '', '', '', '', 0),
(4, 6, 'Товар4', '', '', 6500, 0, '0', '1', '1', '000-004', '0', '', 'i7-17ii4-1i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223137223b7d693a343b613a313a7b693a303b733a313a2231223b7d7d, '1', 0, '0', '', '0', 1634714992, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 65, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar4', '', '', '', '', 0),
(5, 6, 'Товар5', '', '', 6000, 0, '0', '1', '1', '000-005', '1', '', 'i7-17ii4-8i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223137223b7d693a343b613a313a7b693a303b733a313a2238223b7d7d, '1', 0, '1', '', '0', 1634715001, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 60, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar5', '', '', '', '', 0),
(6, 3, 'Товар6', '', '', 5500, 0, '0', '1', '1', '000-006', '1', '', 'i7-15ii4-1ii4-7ii4-1%2C7i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223135223b7d693a343b613a333a7b693a303b733a313a2231223b693a313b733a313a2237223b693a323b733a353a223125324337223b7d7d, '1', 0, '1', '', '0', 1634715016, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 55, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar6', '', '', '', '', 0),
(7, 4, 'Товар7', '', '', 5000, 0, '0', '1', '1', '000-007', '0', '', 'i7-17ii4-7i', 0x613a323a7b693a373b613a313a7b693a303b733a323a223137223b7d693a343b613a313a7b693a303b733a313a2237223b7d7d, '1', 0, '0', '', '0', 1634715027, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 50, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar7', '', '', '', '', 0),
(8, 8, 'Товар8', '', '', 4500, 0, '0', '1', '1', '000-008', '0', '', 'i6-11i', 0x613a313a7b693a363b613a313a7b693a303b733a323a223131223b7d7d, '1', 0, '0', '', '0', 1634718917, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 45, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar8', '', '', '', '', 0),
(9, 8, 'Товар9', '', '', 4000, 0, '0', '1', '1', '000-009', '1', '', 'i6-12i', 0x613a313a7b693a363b613a313a7b693a303b733a323a223132223b7d7d, '1', 0, '1', '', '0', 1634718924, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 40, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar9', '', '', '', '', 0),
(10, 9, 'Товар11', '', '', 3500, 0, '0', '1', '1', '000-011', '1', '', 'i6-9i', 0x613a313a7b693a363b613a313a7b693a303b733a313a2239223b7d7d, '1', 0, '1', '', '0', 1634718943, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 35, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar11', '', '', '', '', 0),
(11, 8, 'Товар10', '', '', 3000, 0, '0', '1', '1', '000-010', '1', '', 'i6-10i', 0x613a313a7b693a363b613a313a7b693a303b733a323a223130223b7d7d, '1', 0, '1', '', '0', 1634718912, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 30, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar10', '', '', '', '', 0),
(12, 9, 'Товар12', '', '', 2500, 0, '0', '1', '1', '000-012', '1', '', 'i6-9ii6-10ii6-9%2C10i', 0x613a313a7b693a363b613a333a7b693a303b733a313a2239223b693a313b733a323a223130223b693a323b733a363a22392532433130223b7d7d, '1', 0, '1', '', '0', 1634718953, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 25, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar12', '', '', '', '', 0),
(13, 9, 'Товар13', '', '', 2000, 0, '0', '1', '1', '000-013', '0', '', 'i6-11i', 0x613a313a7b693a363b613a313a7b693a303b733a323a223131223b7d7d, '1', 0, '0', '', '0', 1634718960, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 20, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar13', '', '', '', '', 0),
(14, 9, 'Товар14', '', '', 1500, 0, '0', '1', '1', '000-014', '0', '', 'i6-11ii6-12ii6-11%2C12i', 0x613a313a7b693a363b613a333a7b693a303b733a323a223131223b693a313b733a323a223132223b693a323b733a373a2231312532433132223b7d7d, '1', 0, '0', '', '0', 1634718969, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 15, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar14', '', '', '', '', 0),
(15, 9, 'Товар15', '', '', 1000, 0, '0', '1', '1', '000-015', '0', '', 'i6-12ii6-13ii6-12%2C13i', 0x613a313a7b693a363b613a333a7b693a303b733a323a223132223b693a313b733a323a223133223b693a323b733a373a2231322532433133223b7d7d, '1', 0, '0', '', '0', 1634718978, 'null', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-fotos.jpg', '/UserFiles/Image/trial/primer-foto.jpg', 0x613a313a7b733a333a22626964223b733a303a22223b7d, '0', NULL, 10, 0, 0, 0, 0, 0, 'N;', 6, 'шт.', '', 0, 0, 0, NULL, NULL, '', '', '0', '0', 'tovar15', '', '', '', '', 0),
(17, 8, 'Товар1 110 зеленый', NULL, NULL, 8000, 0, '0', '0', '1', '', '0', '', '', 0x4e3b, '0', 1, '0', '', '0', 1631538468, '', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-foto2s.jpg', '/UserFiles/Image/trial/primer-foto2.jpg', 0x4e3b, '1', '110', 1, 0, 0, 0, 0, 0, 'N;', 6, '', '', 0, 0, 0, 'зеленый', '#008000', '', '', '0', '0', '', '', '', '', '', 0),
(18, 8, 'Товар1 122 желтый', NULL, NULL, 10000, 0, '0', '0', '1', '', '0', '', '', 0x4e3b, '0', 1, '0', '', '0', 1631538470, '', 1, '', '0', '', '', '', '0', '', '/UserFiles/Image/trial/primer-foto3s.jpg', '/UserFiles/Image/trial/primer-foto3.jpg', 0x4e3b, '1', '122', 1, 0, 0, 0, 0, 0, 'N;', 6, '', '', 0, 0, 0, 'желтый', '#FFFF00', '', '', '0', '0', '', '', '', '', '', 0),
(20, 8, 'Товар1 134 черный', NULL, NULL, 12000, 0, '0', '0', '1', '', '0', '', '', 0x4e3b, '0', 1, '0', '', '0', 1631538055, '', 1, '', '0', '', '', '', '0', '', '', '', 0x4e3b, '1', '134', 1, 0, 0, 0, 0, 0, 'N;', 6, '', '', 0, 0, 0, 'черный', '#000000', '', '', '0', '0', '', '', '', '', '', 0);
COMMIT;

DROP TABLE IF EXISTS `phpshop_promotions`;
CREATE TABLE IF NOT EXISTS `phpshop_promotions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `enabled` enum('0','1') DEFAULT '0',
  `description` text NOT NULL,
  `label` text NOT NULL,
  `active_check` enum('0','1') NOT NULL,
  `active_date_ot` varchar(255) NOT NULL,
  `active_date_do` varchar(255) NOT NULL,
  `discount_check` enum('0','1') NOT NULL,
  `discount_tip` enum('0','1') NOT NULL,
  `discount` int(11) NOT NULL,
  `num_check` int(11) DEFAULT '0',
  `categories_check` enum('0','1') NOT NULL,
  `categories` text NOT NULL,
  `status_check` enum('0','1') NOT NULL DEFAULT '0',
  `statuses` text NOT NULL,
  `products_check` enum('0','1') NOT NULL,
  `products` text NOT NULL,
  `sum_order_check` enum('0','1') NOT NULL,
  `sum_order` int(11) NOT NULL,
  `delivery_method_check` enum('0','1') NOT NULL,
  `delivery_method` int(11) NOT NULL,
  `date_create` timestamp NOT NULL,
  `block_old_price` enum('0','1') DEFAULT '0',
  `hide_old_price` enum('0','1') DEFAULT '0',
  `action` enum('1','2') DEFAULT '1',
  `disable_categories` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_promotions` (`id`, `name`, `enabled`, `description`, `label`, `active_check`, `active_date_ot`, `active_date_do`, `discount_check`, `discount_tip`, `discount`, `num_check`, `categories_check`, `categories`, `status_check`, `statuses`, `products_check`, `products`, `sum_order_check`, `sum_order`, `delivery_method_check`, `delivery_method`, `date_create`, `block_old_price`, `hide_old_price`, `action`, `disable_categories`) VALUES
(1, 'Скидка на каталог1', '1', '', 'Промоакция', '0', '', '', '0', '1', 30, 0, '1', '5,6,3,4,', '0', 'a:1:{i:0;s:4:\"null\";}', '0', '', '0', 0, '0', 0, '2021-10-12 12:31:15', '0', '0', '1', '0');

DROP TABLE IF EXISTS `phpshop_rating_categories`;
CREATE TABLE IF NOT EXISTS `phpshop_rating_categories` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `ids_dir` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '0',
  `revoting` enum('0','1') DEFAULT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_rating_charact`;
CREATE TABLE IF NOT EXISTS `phpshop_rating_charact` (
  `id_charact` int(11) NOT NULL AUTO_INCREMENT,
  `id_category` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `num` int(11) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id_charact`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_rating_votes`;
CREATE TABLE IF NOT EXISTS `phpshop_rating_votes` (
  `id_vote` int(11) NOT NULL AUTO_INCREMENT,
  `id_charact` int(11) DEFAULT '0',
  `id_good` int(11) DEFAULT '0',
  `id_user` int(11) DEFAULT '0',
  `userip` varchar(16) DEFAULT '',
  `rate` tinyint(4) DEFAULT '0',
  `date` int(11) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id_vote`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_rssgraber`;
CREATE TABLE IF NOT EXISTS `phpshop_rssgraber` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link` text,
  `day_num` int(1) DEFAULT '1',
  `news_num` mediumint(8) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '1',
  `start_date` int(16) UNSIGNED DEFAULT '0',
  `end_date` int(16) UNSIGNED DEFAULT '0',
  `last_load` int(16) UNSIGNED DEFAULT '0',
  `servers` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_rssgraber_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_rssgraber_jurnal` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` int(15) UNSIGNED DEFAULT '0',
  `link_id` int(11) DEFAULT '0',
  `status` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_search_base`;
CREATE TABLE IF NOT EXISTS `phpshop_search_base` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `uid` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '1',
  `category` int(11) DEFAULT '0',
  `url` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_search_jurnal`;
CREATE TABLE IF NOT EXISTS `phpshop_search_jurnal` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `num` tinyint(32) DEFAULT '0',
  `datas` varchar(11) DEFAULT '',
  `dir` varchar(255) DEFAULT '',
  `cat` tinyint(11) DEFAULT '0',
  `set` tinyint(2) DEFAULT '0',
  `ip` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_servers`;
CREATE TABLE IF NOT EXISTS `phpshop_servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `host` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '0',
  `code` varchar(64) DEFAULT '',
  `skin` varchar(64) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `descrip` varchar(255) DEFAULT '',
  `tel` varchar(255) DEFAULT '',
  `company` varchar(255) DEFAULT '',
  `adres` varchar(255) DEFAULT '',
  `logo` varchar(255) DEFAULT '',
  `adminmail` varchar(255) DEFAULT '',
  `currency` int(11) DEFAULT NULL,
  `lang` varchar(32) DEFAULT NULL,
  `admoption` blob,
  `warehouse` int(11) DEFAULT '0',
  `price` enum('1','2','3','4','5') DEFAULT '1',
  `admin` int(11) DEFAULT '0',
  `icon` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT '0',
  `shop_type` ENUM('0','1','2') NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_shopusers`;
CREATE TABLE IF NOT EXISTS `phpshop_shopusers` (
  `id` int(64) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` varchar(64) DEFAULT '',
  `password` varchar(64) DEFAULT '',
  `datas` varchar(64) DEFAULT '',
  `mail` varchar(64) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `company` varchar(255) DEFAULT '',
  `inn` varchar(64) DEFAULT '',
  `tel` varchar(64) DEFAULT '',
  `adres` text,
  `enabled` enum('0','1') DEFAULT '0',
  `status` varchar(64) DEFAULT '0',
  `kpp` varchar(64) DEFAULT '',
  `tel_code` varchar(64) DEFAULT '',
  `wishlist` blob,
  `data_adres` blob,
  `cumulative_discount` int(11) DEFAULT '0',
  `sendmail` enum('0','1') DEFAULT '1',
  `servers` int(11) DEFAULT '0',
  `bonus` int(11) DEFAULT '0',
  `token` int(11) DEFAULT NULL,
  `token_time` int(11) DEFAULT NULL,
  `bot` varchar(64) DEFAULT '',
  `dialog_ban` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_shopusers` (`id`, `login`, `password`, `datas`, `mail`, `name`, `company`, `inn`, `tel`, `adres`, `enabled`, `status`, `kpp`, `tel_code`, `wishlist`, `data_adres`, `cumulative_discount`, `sendmail`, `servers`, `bonus`, `token`, `token_time`, `bot`, `dialog_ban`) VALUES
(1, 'client@mail.ru', 'Mm04emk0OXI=', '1631544462', 'client@mail.ru', 'Иван', '', '', '+7(926) 111-11-11', '', '1', '0', '', '', 0x613a303a7b7d, 0x613a323a7b733a343a226c697374223b613a313a7b693a303b613a313a7b733a373a2274656c5f6e6577223b733a31373a222b372839323629203131312d31312d3131223b7d7d733a343a226d61696e223b693a303b7d, 0, '1', 0, 0, NULL, NULL, '5e51b87faca2c5e8746c17a19a7d3e29', '0');
DROP TABLE IF EXISTS `phpshop_shopusers_status`;
CREATE TABLE IF NOT EXISTS `phpshop_shopusers_status` (
  `id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `discount` float DEFAULT '0',
  `price` enum('1','2','3','4','5') DEFAULT '1',
  `enabled` enum('0','1') DEFAULT '1',
  `cumulative_discount_check` enum('0','1') DEFAULT '0',
  `cumulative_discount` blob,
  `warehouse` enum('0','1') DEFAULT '1',
  `cart_min` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_slider`;
CREATE TABLE IF NOT EXISTS `phpshop_slider` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '0',
  `num` smallint(6) DEFAULT '0',
  `link` varchar(255) DEFAULT '',
  `alt` varchar(255) DEFAULT '',
  `servers` varchar(1000) DEFAULT '',
  `mobile` enum('0','1') DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `link_text` varchar(255) DEFAULT NULL,
  `color` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_slider` (`id`, `image`, `enabled`, `num`, `link`, `alt`, `servers`, `mobile`, `name`, `link_text`) VALUES
(1, '/UserFiles/Image/trial/slider1700x600.jpg', '1', 1, '/id/tovar10-11.html', 'Загрузите слайды одного размера', '', '0', 'Заголовок слайдера', 'Подробнее'),
(2, '/UserFiles/Image/trial/slider1700x600.jpg', '1', 2, '/katalog2-podkatalog2.html', 'Описание акции', '', '0', 'Заголовок слайдера', 'Купить'),
(3, '/UserFiles/Image/trial/slider-mobile800x400.jpg', '1', 3, '/katalog2-podkatalog2.html', 'С картинкой маленького размера.', '', '1', 'Слайдер для мобильных', 'Подробнее'),
(4, '/UserFiles/Image/trial/slider-mobile800x400.jpg', '1', 4, '/katalog1.html', 'Загрузите слайд маленького размера.', '', '1', 'Слайдер для мобильных', 'Подробнее');

DROP TABLE IF EXISTS `phpshop_sort`;
CREATE TABLE IF NOT EXISTS `phpshop_sort` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `category` int(11) UNSIGNED DEFAULT '0',
  `num` int(11) DEFAULT '0',
  `page` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `description` text,
  `sort_seo_name` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `meta_description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_sort` (`id`, `name`, `category`, `num`, `page`, `icon`, `description`, `sort_seo_name`, `title`, `meta_description`) VALUES
(1, '100х100', 4, 1, '', '', NULL, '', '', ''),
(8, '100х300', 4, 3, '', '', NULL, '', '', ''),
(7, '100х200', 4, 2, '', '', NULL, '', '', ''),
(15, 'Brand1', 7, 1, '', '/UserFiles/Image/trial/logo1.svg', '', 'brand1', '', ''),
(16, 'Brand2', 7, 2, '', '/UserFiles/Image/trial/logo2.svg', '', 'brand2', '', ''),
(17, 'Brand3', 7, 3, '', '/UserFiles/Image/trial/logo3.svg', '', 'brand3', '', '');
COMMIT;

DROP TABLE IF EXISTS `phpshop_sort_categories`;
CREATE TABLE IF NOT EXISTS `phpshop_sort_categories` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `num` int(11) DEFAULT '0',
  `category` int(11) DEFAULT '0',
  `filtr` enum('0','1') DEFAULT '0',
  `description` varchar(255) DEFAULT '',
  `goodoption` enum('0','1') DEFAULT '0',
  `optionname` enum('0','1') DEFAULT '0',
  `page` varchar(255) DEFAULT '',
  `brand` enum('0','1') DEFAULT '0',
  `product` enum('0','1') DEFAULT '0',
  `virtual` enum('0','1') DEFAULT '0',
  `yandex_param` enum('1','2') DEFAULT '1',
  `yandex_param_unit` varchar(64) DEFAULT '',
  `servers` varchar(1000) DEFAULT '',
  `show_preview` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=cp1251;


INSERT INTO `phpshop_sort_categories` (`id`, `name`, `num`, `category`, `filtr`, `description`, `goodoption`, `optionname`, `page`, `brand`, `product`, `virtual`, `yandex_param`, `yandex_param_unit`, `servers`, `show_preview`) VALUES
(4, 'Размер', 0, 3, '1', '', '0', '0', '', '0', '0', '0', '1', '', '', '1'),
(3, 'Общая группа', 0, 0, '0', '', '0', '0', '', '0', '0', '0', '1', '', '', '0'),
(7, 'Бренды', 0, 3, '1', '', '0', '0', '', '1', '0', '0', '1', '', '', '0');

DROP TABLE IF EXISTS `phpshop_system`;
CREATE TABLE IF NOT EXISTS `phpshop_system` (
  `id` int(32) NOT NULL,
  `name` text,
  `company` text,
  `num_row` int(10) DEFAULT NULL,
  `num_row_adm` int(10) DEFAULT NULL,
  `dengi` tinyint(11) DEFAULT NULL,
  `percent` varchar(16) DEFAULT '',
  `skin` varchar(32) DEFAULT NULL,
  `adminmail2` varchar(64) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `keywords` varchar(255) DEFAULT '',
  `kurs` float DEFAULT '0',
  `spec_num` tinyint(5) DEFAULT '0',
  `new_num` tinyint(11) DEFAULT '0',
  `tel` text,
  `bank` blob,
  `num_vitrina` enum('1','2','3','4','5','6') DEFAULT '3',
  `icon` varchar(255) DEFAULT '',
  `updateU` varchar(32) DEFAULT '',
  `nds` varchar(64) DEFAULT '',
  `nds_enabled` enum('0','1') DEFAULT '1',
  `admoption` blob,
  `shop_type` ENUM('0','1','2') NULL DEFAULT '0',
  `descrip` varchar(255) DEFAULT '',
  `descrip_shablon` varchar(255) DEFAULT '',
  `title_shablon` varchar(255) DEFAULT '',
  `keywords_shablon` varchar(255) DEFAULT '',
  `title_shablon2` varchar(255) DEFAULT '',
  `descrip_shablon2` varchar(255) DEFAULT '',
  `keywords_shablon2` varchar(255) DEFAULT '',
  `logo` varchar(255) DEFAULT '',
  `title_shablon3` varchar(255) DEFAULT '',
  `descrip_shablon3` varchar(255) DEFAULT '',
  `keywords_shablon3` varchar(255) DEFAULT '',
  `rss_use` int(1) UNSIGNED DEFAULT '1',
  `1c_load_accounts` enum('0','1') DEFAULT '1',
  `1c_load_invoice` enum('0','1') DEFAULT '1',
  `1c_option` blob,
  `sort_title_shablon` varchar(255) DEFAULT '',
  `sort_description_shablon` varchar(255) DEFAULT '',
  `ai` BLOB NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_system` (`id`, `name`, `company`, `num_row`, `num_row_adm`, `dengi`, `percent`, `skin`, `adminmail2`, `title`, `keywords`, `kurs`, `spec_num`, `new_num`, `tel`, `bank`, `num_vitrina`, `icon`, `updateU`, `nds`, `nds_enabled`, `admoption`, `shop_type`, `descrip`, `descrip_shablon`, `title_shablon`, `keywords_shablon`, `title_shablon2`, `descrip_shablon2`, `keywords_shablon2`, `logo`, `title_shablon3`, `descrip_shablon3`, `keywords_shablon3`, `rss_use`, `1c_load_accounts`, `1c_load_invoice`, `1c_option`, `sort_title_shablon`, `sort_description_shablon`, `ai`) VALUES
(1, 'Название интернет-магазина', 'Продавец', 8, 3, 6, '0', 'flow', 'admin@localhost', 'Демо-версия скрипта интернет-магазина PHPShop', 'скрипт магазина, купить интернет-магазин', 6, 8, 8, '&#43;7(495)111-22-33', 0x613a31373a7b733a383a226f72675f6e616d65223b733a31343a22cecece2022cff0eee4e0e2e5f622223b733a31323a226f72675f75725f6164726573223b733a34313a2230303030303020e32e20cceef1eae2e02c20f3eb2e20def0e8e4e8f7e5f1eae0ff2c20e4eeec20312e223b733a393a226f72675f6164726573223b733a33303a22cceef1eae2e02c20f3eb2e20d4e8e7e8f7e5f1eae0ff2c20e4eeec20312e223b733a373a226f72675f696e6e223b733a393a22373737373737373737223b733a373a226f72675f6b7070223b733a31303a2238383838383838383838223b733a393a226f72675f7363686574223b733a31363a2231313131313131313131313131313131223b733a383a226f72675f62616e6b223b733a32333a22cec0ce2022c2e0f820f2e5f1f2eee2fbe920e1e0edea22223b733a373a226f72675f626963223b733a383a223436373738383838223b733a31343a226f72675f62616e6b5f7363686574223b733a31353a22323232323232323232323232323232223b733a393a226f72675f7374616d70223b733a33323a222f5573657246696c65732f496d6167652f547269616c2f7374616d702e706e67223b733a373a226f72675f736967223b733a33363a222f5573657246696c65732f496d6167652f547269616c2f66616373696d696c652e706e67223b733a31313a226f72675f7369675f627568223b733a33363a222f5573657246696c65732f496d6167652f547269616c2f66616373696d696c652e706e67223b733a373a226f72675f74656c223b733a303a22223b733a383a226f72675f74696d65223b733a31333a2231303a3030202d2031383a3030223b733a393a226f72675f666f726d61223b733a313a2231223b733a383a226f72675f6f67726e223b733a303a22223b733a383a226f72675f6c6f676f223b733a303a22223b7d, '4', '/UserFiles/Image/trial/favicon1.png', '1409661405', '20', '1', 0x613a3139343a7b733a31323a22736b6c61645f737461747573223b733a313a2231223b733a31333a22636c6f75645f656e61626c6564223b693a303b733a32333a226469676974616c5f70726f647563745f656e61626c6564223b693a303b733a31333a22757365725f63616c656e646172223b693a303b733a31393a22757365725f70726963655f6163746976617465223b693a303b733a32323a22757365725f6d61696c5f61637469766174655f707265223b693a303b733a31383a227273735f6772616265725f656e61626c6564223b693a303b733a31373a22696d6167655f736176655f736f75726365223b693a303b733a363a22696d675f776d223b4e3b733a353a22696d675f77223b733a343a2231303030223b733a353a22696d675f68223b733a343a2231303030223b733a363a22696d675f7477223b733a333a22333030223b733a363a22696d675f7468223b733a333a22333030223b733a31343a2277696474685f706f64726f626e6f223b733a333a22313030223b733a31323a2277696474685f6b7261746b6f223b733a333a22313030223b733a31323a22626173655f656e61626c6564223b4e3b733a31313a22736d735f656e61626c6564223b693a303b733a31343a226e6f746963655f656e61626c6564223b693a303b733a373a22626173655f6964223b733a303a22223b733a393a22626173655f686f7374223b733a303a22223b733a31333a22736b6c61645f656e61626c6564223b733a313a2231223b733a31303a2270726963655f7a6e616b223b733a313a2230223b733a31383a22757365725f6d61696c5f6163746976617465223b693a303b733a31313a22757365725f737461747573223b733a313a2230223b733a393a22757365725f736b696e223b733a313a2231223b733a31323a22636172745f6d696e696d756d223b733a313a2230223b733a31333a2277617465726d61726b5f626967223b613a32313a7b733a31343a226269675f6d657267654c6576656c223b693a37303b733a31313a226269675f656e61626c6564223b733a313a2231223b733a383a226269675f74797065223b733a333a22706e67223b733a31323a226269675f706e675f66696c65223b733a33303a222f5573657246696c65732f496d6167652f73686f705f6c6f676f2e706e67223b733a31323a226269675f636f7079466c6167223b733a313a2230223b733a363a226269675f736d223b693a303b733a31363a226269675f706f736974696f6e466c6167223b733a313a2234223b733a31333a226269675f706f736974696f6e58223b693a303b733a31333a226269675f706f736974696f6e59223b693a303b733a393a226269675f616c706861223b693a37303b733a383a226269675f74657874223b733a303a22223b733a32313a226269675f746578745f706f736974696f6e466c6167223b693a303b733a383a226269675f73697a65223b693a303b733a393a226269675f616e676c65223b693a303b733a31383a226269675f746578745f706f736974696f6e58223b693a303b733a31383a226269675f746578745f706f736974696f6e59223b693a303b733a31303a226269675f636f6c6f7252223b693a303b733a31303a226269675f636f6c6f7247223b693a303b733a31303a226269675f636f6c6f7242223b693a303b733a31343a226269675f746578745f616c706861223b693a303b733a383a226269675f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31353a2277617465726d61726b5f736d616c6c223b613a32313a7b733a31363a22736d616c6c5f6d657267654c6576656c223b693a3130303b733a31333a22736d616c6c5f656e61626c6564223b733a313a2231223b733a31303a22736d616c6c5f74797065223b733a333a22706e67223b733a31343a22736d616c6c5f706e675f66696c65223b733a32353a222f5573657246696c65732f496d6167652f6c6f676f2e706e67223b733a31343a22736d616c6c5f636f7079466c6167223b733a313a2230223b733a383a22736d616c6c5f736d223b693a303b733a31383a22736d616c6c5f706f736974696f6e466c6167223b733a313a2231223b733a31353a22736d616c6c5f706f736974696f6e58223b693a303b733a31353a22736d616c6c5f706f736974696f6e59223b693a303b733a31313a22736d616c6c5f616c706861223b693a35303b733a31303a22736d616c6c5f74657874223b733a303a22223b733a32333a22736d616c6c5f746578745f706f736974696f6e466c6167223b693a303b733a31303a22736d616c6c5f73697a65223b693a303b733a31313a22736d616c6c5f616e676c65223b693a303b733a32303a22736d616c6c5f746578745f706f736974696f6e58223b693a303b733a32303a22736d616c6c5f746578745f706f736974696f6e59223b693a303b733a31323a22736d616c6c5f636f6c6f7252223b693a303b733a31323a22736d616c6c5f636f6c6f7247223b693a303b733a31323a22736d616c6c5f636f6c6f7242223b693a303b733a31363a22736d616c6c5f746578745f616c706861223b693a303b733a31303a22736d616c6c5f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31353a2277617465726d61726b5f6973686f64223b613a32313a7b733a31363a226973686f645f6d657267654c6576656c223b693a3130303b733a31333a226973686f645f656e61626c6564223b4e3b733a31303a226973686f645f74797065223b733a333a22706e67223b733a31343a226973686f645f706e675f66696c65223b733a303a22223b733a31343a226973686f645f636f7079466c6167223b733a313a2230223b733a383a226973686f645f736d223b693a303b733a31383a226973686f645f706f736974696f6e466c6167223b733a313a2231223b733a31353a226973686f645f706f736974696f6e58223b693a303b733a31353a226973686f645f706f736974696f6e59223b693a303b733a31313a226973686f645f616c706861223b693a303b733a31303a226973686f645f74657874223b733a303a22223b733a32333a226973686f645f746578745f706f736974696f6e466c6167223b693a303b733a31303a226973686f645f73697a65223b693a303b733a31313a226973686f645f616e676c65223b693a303b733a32303a226973686f645f746578745f706f736974696f6e58223b693a303b733a32303a226973686f645f746578745f706f736974696f6e59223b693a303b733a31323a226973686f645f636f6c6f7252223b693a303b733a31323a226973686f645f636f6c6f7247223b693a303b733a31323a226973686f645f636f6c6f7242223b693a303b733a31363a226973686f645f746578745f616c706861223b693a303b733a31303a226973686f645f666f6e74223b733a31363a226e6f726f626f745f666f6e742e747466223b7d733a31343a226e6f776275795f656e61626c6564223b733a313a2232223b733a363a22656469746f72223b733a373a2274696e796d6365223b733a353a227468656d65223b733a373a2264656661756c74223b733a32343a22736d735f7374617475735f6f726465725f656e61626c6564223b693a303b733a31373a226d61696c5f736d74705f7265706c79746f223b733a303a22223b733a393a22736d735f70686f6e65223b733a303a22223b733a383a22736d735f75736572223b733a303a22223b733a383a22736d735f70617373223b733a303a22223b733a383a22736d735f6e616d65223b733a303a22223b733a393a226163655f7468656d65223b733a343a226461776e223b733a393a2261646d5f7469746c65223b733a303a22223b733a31343a227365617263685f656e61626c6564223b733a313a2233223b733a31343a226d61696c5f736d74705f686f7374223b733a303a22223b733a31343a226d61696c5f736d74705f706f7274223b733a303a22223b733a31343a226d61696c5f736d74705f75736572223b733a303a22223b733a31343a226d61696c5f736d74705f70617373223b733a303a22223b733a32303a22706172656e745f70726963655f656e61626c6564223b693a303b733a31373a226d61696c5f736d74705f656e61626c6564223b693a303b733a31353a226d61696c5f736d74705f6465627567223b693a303b733a31343a226d61696c5f736d74705f61757468223b693a303b733a31323a2272756c655f656e61626c6564223b693a303b733a31353a226361746c6973745f656e61626c6564223b733a313a2231223b733a31373a227265636170746368615f656e61626c6564223b693a303b733a31343a227265636170746368615f706b6579223b733a303a22223b733a31343a227265636170746368615f736b6579223b733a303a22223b733a31343a226461646174615f656e61626c6564223b693a303b733a31323a226461646174615f746f6b656e223b733a303a22223b733a32313a226d756c74695f63757272656e63795f736561726368223b693a303b733a31373a22696d6167655f726573756c745f70617468223b733a303a22223b733a31343a2277617465726d61726b5f74657874223b733a383a22594f55524c4f474f223b733a32303a2277617465726d61726b5f746578745f636f6c6f72223b733a373a2223636363636363223b733a31393a2277617465726d61726b5f746578745f73697a65223b733a323a223230223b733a31393a2277617465726d61726b5f746578745f666f6e74223b733a343a2256657261223b733a31353a2277617465726d61726b5f7269676874223b733a323a223230223b733a31363a2277617465726d61726b5f626f74746f6d223b733a323a223330223b733a32303a2277617465726d61726b5f746578745f616c706861223b733a323a223430223b733a31353a2277617465726d61726b5f696d616765223b733a303a22223b733a32313a22696d6167655f61646170746976655f726573697a65223b693a303b733a31353a22696d6167655f736176655f6e616d65223b693a303b733a32313a2277617465726d61726b5f6269675f656e61626c6564223b693a303b733a32343a2277617465726d61726b5f736f757263655f656e61626c6564223b693a303b733a31373a2279616e6465786d61705f656e61626c6564223b733a313a2231223b733a393a226875625f7468656d65223b733a32333a22626f6f7473747261702d7468656d652d64656661756c74223b733a31353a226875625f666c7569645f7468656d65223b733a32333a22626f6f7473747261702d7468656d652d64656661756c74223b733a32343a2277617465726d61726b5f63656e7465725f656e61626c6564223b693a303b733a31393a2266696c7465725f63616368655f706572696f64223b733a303a22223b733a32303a2266696c7465725f63616368655f656e61626c6564223b693a303b733a32313a2266696c7465725f70726f64756374735f636f756e74223b733a313a2231223b733a31323a2270726f6d6f5f6e6f74696365223b623a313b733a31353a22696d6167655f736176655f70617468223b693a303b733a31313a226e65775f656e61626c6564223b693a303b733a31323a22636861745f656e61626c6564223b693a303b733a31383a22696d6167655f736176655f636174616c6f67223b693a303b733a32333a2277617465726d61726b5f736d616c6c5f656e61626c6564223b693a303b733a31323a2261737465726f5f7468656d65223b733a32303a22626f6f7473747261702d7468656d652d626c7565223b733a31383a2261737465726f5f666c7569645f7468656d65223b733a32303a22626f6f7473747261702d7468656d652d626c7565223b733a31333a2261737465726f5f656469746f72223b4e3b733a31303a226c65676f5f7468656d65223b733a32333a22626f6f7473747261702d7468656d652d64656661756c74223b733a31363a226c65676f5f666c7569645f7468656d65223b733a32333a22626f6f7473747261702d7468656d652d64656661756c74223b733a31313a226c65676f5f656469746f72223b613a353a7b733a313a2268223b693a313b733a313a2266223b693a313b733a313a2263223b693a313b733a313a2270223b693a323b733a313a2273223b693a323b7d733a31333a226d6574726963615f746f6b656e223b733a303a22223b733a31303a226d6574726963615f6964223b733a303a22223b733a31333a2279616e6465785f6170696b6579223b733a303a22223b733a393a22676f6f676c655f6964223b733a303a22223b733a31353a226d6574726963615f656e61626c6564223b693a303b733a31343a226d6574726963615f776964676574223b693a303b733a31373a226d6574726963615f65636f6d6d65726365223b693a303b733a31343a22676f6f676c655f656e61626c6564223b693a303b733a31363a22676f6f676c655f616e616c6974696373223b693a303b733a31373a22736b6c61645f73756d5f656e61626c6564223b693a303b733a31373a22696d6167655f6469616c6f675f70617468223b733a303a22223b733a31313a22636861745f6469616c6f67223b693a303b733a31323a227469746c655f6469616c6f67223b733a303a22223b733a31313a22746578745f6469616c6f67223b733a303a22223b733a31363a2274696d655f66726f6d5f6469616c6f67223b733a313a2238223b733a31373a2274696d655f756e74696c5f6469616c6f67223b733a323a223230223b733a31303a226461795f6469616c6f67223b733a313a2231223b733a31323a22636f6c6f725f6469616c6f67223b733a373a2223343261356635223b733a31333a226d617267696e5f6469616c6f67223b733a313a2230223b733a31313a2273697a655f6469616c6f67223b733a323a223536223b733a31323a2273697a656d5f6469616c6f67223b733a323a223536223b733a31323a2274656c656772616d5f626f74223b733a303a22223b733a31343a2274656c656772616d5f61646d696e223b733a303a22223b733a31343a2274656c656772616d5f746f6b656e223b733a303a22223b733a363a22766b5f626f74223b733a303a22223b733a31353a22766b5f636f6e6669726d6174696f6e223b733a303a22223b733a393a22766b5f736563726574223b733a303a22223b733a383a22766b5f61646d696e223b733a303a22223b733a383a22766b5f746f6b656e223b733a303a22223b733a31363a2274656c656772616d5f656e61626c6564223b693a303b733a31303a22766b5f656e61626c6564223b693a303b733a31353a2274656c656772616d5f6469616c6f67223b693a303b733a393a22766b5f6469616c6f67223b693a303b733a31313a226d61696c5f6469616c6f67223b693a303b733a31313a22707573685f6469616c6f67223b693a303b733a31343a2274656c656772616d5f6f72646572223b693a303b733a383a22766b5f6f72646572223b693a303b733a31323a226d6f62696c5f6469616c6f67223b693a303b733a31303a2274656c5f6469616c6f67223b693a303b733a31353a2274696d655f6f66665f6469616c6f67223b693a303b733a31333a226176617461725f6469616c6f67223b733a33383a222f70687073686f702f6c69622f74656d706c617465732f636861742f6176617461722e706e67223b733a31333a226361746c6973745f6465707468223b733a313a2233223b733a31313a227365617263685f706f6c65223b733a313a2231223b733a383a2274696d657a6f6e65223b733a303a22223b733a31353a22757365725f70686f6e655f6d61736b223b733a32313a22262334333b372839393929203939392d39392d3939223b733a353a22626f6e7573223b733a313a2230223b733a31313a226f726465725f626f6e7573223b733a313a2230223b733a383a226c616e675f61646d223b733a373a227275737369616e223b733a32303a22757365725f736572766572735f636f6e74726f6c223b693a303b733a32333a22757365725f70686f6e655f6d61736b5f656e61626c6564223b693a303b733a31393a22757365725f6974656d735f6163746976617465223b693a303b733a31313a22616a61785f7363726f6c6c223b693a303b733a32313a22616a61785f7363726f6c6c5f706167696e61746f72223b693a303b733a31343a226c6963656e73655f6e6f74696365223b623a313b733a31343a22696d6167655f736176655f73656f223b693a303b733a383a22696d675f74775f63223b733a333a22343130223b733a383a22696d675f74685f63223b733a333a22323030223b733a383a22696d675f74775f73223b733a343a2231343430223b733a383a22696d675f74685f73223b733a333a22333030223b733a393a22696d6167655f6f6666223b693a303b733a393a22696d6167655f636174223b693a303b733a31323a22696d6167655f736c69646572223b693a303b733a32313a22696d6167655f736c696465725f6164617074697665223b693a303b733a31383a22696d6167655f6361745f6164617074697665223b693a303b733a343a226c616e67223b733a373a227275737369616e223b733a32303a2279616e6465785f7365617263685f6170696b6579223b733a303a22223b733a31363a2279616e6465785f7365617263685f6964223b733a303a22223b733a393a22736d735f6c6f67696e223b693a303b733a31303a22707573685f746f6b656e223b733a303a22223b733a373a22707573685f6964223b733a303a22223b733a31323a22707573685f656e61626c6564223b693a303b733a31363a226d6574726963615f77656276697a6f72223b693a303b733a32313a2279616e6465785f7365617263685f656e61626c6564223b693a303b733a31343a2266666d7065675f656e61626c6564223b693a303b733a31313a2266666d7065675f70617468223b733a32313a222f7573722f6c6f63616c2f62696e2f66666d706567223b733a31303a22696d6167655f77656270223b693a303b733a31353a22696d6167655f776562705f73617665223b693a303b733a31323a2266666d7065675f696d616765223b733a323a223130223b733a31373a22736d617274636170746368615f706b6579223b733a303a22223b733a31373a22736d617274636170746368615f736b6579223b733a303a22223b733a31363a2279616e6465785f69645f6170696b6579223b733a303a22223b733a353a22766b5f6964223b733a303a22223b733a31313a22766b5f69645f746f6b656e223b733a303a22223b733a31323a22766b5f69645f6170696b6579223b733a303a22223b733a31313a2277617070695f746f6b656e223b733a303a22223b733a383a2277617070695f6964223b733a303a22223b733a31373a2277617070695f77686174736170705f6964223b733a303a22223b733a31373a2277617070695f74656c656772616d5f6964223b733a303a22223b733a31323a2277617070695f6d61785f6964223b733a303a22223b733a31333a2268636170746368615f706b6579223b733a303a22223b733a31333a2268636170746368615f736b6579223b733a303a22223b733a31393a2274656c656772616d5f6e6577735f64656c696d223b733a303a22223b733a31393a2274656c656772616d5f6e6577735f746f6b656e223b733a303a22223b733a32333a22766b5f726576696577735f636f6e6669726d6174696f6e223b733a303a22223b733a31373a22766b5f726576696577735f736563726574223b733a303a22223b733a31363a22766b5f726576696577735f746f6b656e223b733a303a22223b733a31363a226d656d6361636865645f736572766572223b733a393a223132372e302e302e31223b733a31343a226d656d6361636865645f706f7274223b733a353a223131323131223b733a31363a2268636170746368615f656e61626c6564223b693a303b733a32323a2279616e6465785f7370656c6c65725f656e61626c6564223b693a303b733a31373a2279616e6465785f69645f656e61626c6564223b693a303b733a32313a2274656c656772616d5f6e6577735f656e61626c6564223b693a303b733a31333a22766b5f69645f656e61626c6564223b693a303b733a32303a22736d617274636170746368615f656e61626c6564223b693a303b733a31333a2277617070695f656e61626c6564223b693a303b7d, '0', 'PHPShop – это готовое решение для быстрого создания интернет-магазина.', '@Podcatalog@, @Catalog@, @System@', '@Podcatalog@ - @Catalog@ - @System@', '@Podcatalog@, @Catalog@, @Generator@', '@Product@ - @Podcatalog@ - @Catalog@', '@Product@, @Podcatalog@, @Catalog@', '@Product@,@System@', '/UserFiles/Image/trial/logo.svg', '@Catalog@ - @System@', '@Catalog@', '@Catalog@', 0, '0', '0', 0x613a373a7b733a31313a227570646174655f6e616d65223b733a313a2231223b733a31343a227570646174655f636f6e74656e74223b733a313a2231223b733a31383a227570646174655f6465736372697074696f6e223b733a313a2231223b733a31353a227570646174655f63617465676f7279223b733a313a2231223b733a31313a227570646174655f736f7274223b733a313a2231223b733a31323a227570646174655f7072696365223b733a313a2231223b733a31313a227570646174655f6974656d223b733a313a2231223b7d, '', '', '');
DROP TABLE IF EXISTS `phpshop_users`;
CREATE TABLE IF NOT EXISTS `phpshop_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` blob,
  `login` varchar(64) DEFAULT '',
  `password` varchar(64) DEFAULT '',
  `mail` varchar(64) DEFAULT '',
  `enabled` enum('0','1') DEFAULT '1',
  `name` varchar(255) DEFAULT '',
  `hash` varchar(255) DEFAULT '',
  `token` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_valuta`;
CREATE TABLE IF NOT EXISTS `phpshop_valuta` (
  `id` tinyint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT '',
  `code` varchar(64) DEFAULT '',
  `iso` varchar(64) DEFAULT '',
  `kurs` varchar(64) DEFAULT '0',
  `num` tinyint(11) DEFAULT '0',
  `enabled` enum('0','1') DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_valuta` (`id`, `name`, `code`, `iso`, `kurs`, `num`, `enabled`) VALUES
(5, 'Доллар', '$', 'USD', '0.010', 0, '1'),
(6, 'Рубль', 'руб.', 'RUB', '1', 1, '1');

DROP TABLE IF EXISTS `phpshop_warehouses`;
CREATE TABLE IF NOT EXISTS `phpshop_warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `uid` varchar(64) DEFAULT NULL,
  `enabled` enum('0','1') DEFAULT '1',
  `num` int(11) DEFAULT NULL,
  `servers` varchar(1000) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_exchanges_log`;
CREATE TABLE IF NOT EXISTS `phpshop_exchanges_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `option` blob NOT NULL,
  `import_id` VARCHAR(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `phpshop_modules_yandexkassa_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexkassa_system` (
  `id` int(11) NOT NULL auto_increment,
  `status` int(11) NOT NULL,
  `title` text NOT NULL,
  `title_end` text NOT NULL,
  `shop_id` varchar(64) NOT NULL default '',
  `api_key` varchar(255) NOT NULL default '',
  `payment_mode` ENUM('1','2') NOT NULL DEFAULT '1',
  `receipt_status`int(11) NOT NULL,
  `version` varchar(64) DEFAULT '1.0' NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_yandexkassa_system` (`id`, `status`, `title`, `title_end`, `shop_id`, `api_key`,`payment_mode`, `version`) VALUES
(1, 0, 'Оплатить сейчас', 'Оплатите пожалуйста свой заказ', '665601', 'test_IBkYJDzgL1-gaz04YTHNxQekxtaGz6z-7_40u0rRlYs', '1', 1.8);

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10004, 'Мир, Visa, Mastercard, Юmoney (ЮKassa)', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/yookassa.png');

CREATE TABLE IF NOT EXISTS `phpshop_modules_yandexkassa_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `yandex_id` varchar(255) NULL,
  `status_code` varchar(255) NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_tinkoff_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_tinkoff_system` (
    `id` int(11) NOT NULL auto_increment,
    `title` text NOT NULL,
    `terminal` varchar(64) NOT NULL default '',
    `secret_key` varchar(64) NOT NULL default '',
    `gateway` varchar(64) NOT NULL default '',
    `force_payment` enum('0','1') NOT NULL default '0',
    `version` varchar(64) DEFAULT '1.0',
    `enabled_taxation` int DEFAULT 0,
    `status` int(11) NOT NULL,
    `title_end` text NOT NULL,
    `taxation` varchar(64) NOT NULL,
    `status_confirmed` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_tinkoff_system` (`id`, `title`, `terminal`, `secret_key`, `gateway`, `force_payment`, `version`, `enabled_taxation`, `status`, `title_end`, `taxation`) VALUES
(1, 'Платежная система Т-Банк', 'TinkoffBankTest', 'TinkoffBankTest', 'https://securepay.tinkoff.ru/v2', '0', 2.5, 0, 0, '', 'osn');

INSERT INTO `phpshop_payment_systems` (`id`, `name`, `path`, `enabled`, `num`, `message`, `message_header`, `yur_data_flag`, `icon`) VALUES
(10032, 'Мир, Visa, Mastercard (Т-Банк)', 'modules', '0', 0, '', '', '', '/UserFiles/Image/Payments/tbank.svg');

CREATE TABLE IF NOT EXISTS `phpshop_modules_tinkoff_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `message` blob NOT NULL,
  `order_id` varchar(64) NOT NULL,
  `status` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `time` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=cp1251;

CREATE TABLE IF NOT EXISTS `phpshop_bot` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `date` int(11) NOT NULL,
  `enabled` enum('0','1') NOT NULL DEFAULT '1',
  `description` varchar(255) NOT NULL,
  `date_block` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_2` (`name`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
