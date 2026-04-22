
DROP TABLE IF EXISTS `phpshop_modules_partner_users`;
CREATE TABLE `phpshop_modules_partner_users` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(64) default '',
  `password` varchar(64) default '',
  `name` varchar(64) default '',
  `date` varchar(64) default '',
  `money` varchar(255) default '',
  `enabled` enum('0','1') default '0',
  `content` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `phpshop_modules_partner_payment`;
CREATE TABLE `phpshop_modules_partner_payment` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) NOT NULL default '0',
  `partner_id` varchar(11) NOT NULL default '0',
  `sum` float NOT NULL default '0',
  `enabled` enum('0','1') NOT NULL default '0',
  `date_done` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

DROP TABLE IF EXISTS `phpshop_modules_partner_log`;
CREATE TABLE `phpshop_modules_partner_log` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(11) default '0',
  `order_id` int(11) default '0',
  `order_uid` varchar(64) default '0',
  `partner_id` varchar(11) default '0',
  `path` varchar(255) default '',
  `sum` float default '0',
  `enabled` enum('0','1') default '0',
  `percent` float default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;


DROP TABLE IF EXISTS `phpshop_modules_partner_system`;
CREATE TABLE IF NOT EXISTS `phpshop_modules_partner_system` (
  `id` int(11) NOT NULL auto_increment,
  `enabled` enum('0','1') NOT NULL default '0',
  `percent` float NOT NULL default '0',
  `order_status` tinyint(11) NOT NULL default '0',
  `rule` text NOT NULL,
  `cookies_day` int(11) default '360',
  `stat_day` int(11) default '30',
  `version` varchar(64)  DEFAULT '1.0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO `phpshop_modules_partner_system` VALUES (1, '1', 5, 4, '<h1>Правила и условия партнёрской программы</h1>\r\n\r\n<p>\r\n    <b>1. Участники партнёрской программы.</b><br>\r\n    Участниками партнёрской программы могут быть физические лица. Под физическими лицами понимаются граждане РФ, иностранные граждане, лица без гражданства, а так же предприниматели без образования юридического лица.\r\n\r\n<p>\r\n    <b>2. Оплата услуг партнёра.</b><br>\r\n    Мы будем выплачивать Вам комиссию, установленную в размере @partnerPercent@% от стоимости оплаченного заказа.\r\n\r\n<p>\r\n    <b>3. Способы оплаты.</b><br>\r\n    Все партнёрские выплаты производятся в рублях через электронную платёжную систему ЮMoney, Вы можете ознакомиться с данной системой по адресу https://yoomoney.ru/\r\n\r\n<p>\r\n    <b>4. Минимальная сумма к оплате.</b><br>\r\n    Минимальная сумма к оплате установлена в размере 500 руб. В случае, если заработанная Вами партнёрская комиссия не превышает 500 руб, деньги остаются на Вашем аккаунте до тех пор, пока сумма комиссии не достигнет по крайней мере 500 руб. Оплата партнёрских комиссий производится каждые 2 недели.\r\n\r\n<p>\r\n    <b>5. Партнёрская комиссия.</b><br>\r\n    Партнёрская комиссия будет выплачена только если заказ оформлен и оплачен покупателем, которого привели Вы.\r\n\r\n<p>\r\n    <b>Партнёрская комиссия начисляется только за оплаченные заказы.</b>\r\n\r\n<p>\r\n    Партнёрская комиссия не будет выплачена, если:<br>\r\n    а) Посетитель, пришедший с Вашего сайта не будет учтён нашей системой по техническим причинам (отключены "Cookies" и т.д.).<br>\r\n    б) Посетитель перешёл в магазин по партнёрской ссылке другого партнёра.<br>\r\n    в) Посетиитель, оформивший заказ через Вашу партнёрскую ссылку не оплатил его.<br>\r\n\r\n<p>\r\n    <b>6. Условия.</b><br>\r\n    Покупатели, совершающие заказы через партнёров считаются нашими покупателями и подчиняются правилам нашего магазина. Правила работы магазина могут быть изменены нами без предварительного уведомления.\r\n\r\n<p>\r\n    <b>7. Разногласия</b><br>\r\n    В случае возникновения разногласий, стороны будут стремиться урегулировать возникшие разногласия путем переговоров. В случае, если стороны не придут к соглашению, то спор подлежит рассмотрению в суде РФ.\r\n\r\n<p><a href="/partner/register_user.html" style="font-size:17px">Регистрация в партнерской программе</a>',360,30,'2.0');
