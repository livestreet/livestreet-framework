--
-- Структура таблицы `prefix_storage`
--

CREATE TABLE IF NOT EXISTS `prefix_storage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` mediumtext NOT NULL,
  `instance` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_instance` (`key`,`instance`),
  KEY `instance` (`instance`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;