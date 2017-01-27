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


CREATE TABLE IF NOT EXISTS `prefix_cron_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `method` varchar(500) NOT NULL,
  `plugin` varchar(50) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `count_run` int(11) NOT NULL DEFAULT '0',
  `period_run` int(11) NOT NULL,
  `date_create` datetime NOT NULL,
  `date_run_last` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `count_run` (`count_run`),
  KEY `state` (`state`),
  KEY `plugin` (`plugin`),
  KEY `method` (`method`(255)),
  KEY `period_run` (`period_run`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


-- 14.09.2014

--
-- Структура таблицы `prefix_plugin_migration`
--

CREATE TABLE IF NOT EXISTS `prefix_plugin_migration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  `date_create` datetime NOT NULL,
  `file` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `file` (`file`(255)),
  KEY `code` (`code`),
  KEY `version` (`version`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `prefix_plugin_version`
--

CREATE TABLE IF NOT EXISTS `prefix_plugin_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  `date_update` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`),
  KEY `version` (`version`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- Структура таблицы `prefix_notify_task`
--

CREATE TABLE IF NOT EXISTS `prefix_notify_task` (
  `notify_task_id` int(10) unsigned NOT NULL,
  `user_login` varchar(30) DEFAULT NULL,
  `user_mail` varchar(50) DEFAULT NULL,
  `notify_subject` varchar(200) DEFAULT NULL,
  `notify_text` text,
  `notify_text_alt` text DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `notify_task_status` tinyint(2) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `prefix_notify_task`
--
ALTER TABLE `prefix_notify_task`
ADD PRIMARY KEY (`notify_task_id`), ADD KEY `date_created` (`date_created`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `prefix_notify_task`
--
ALTER TABLE `prefix_notify_task`
MODIFY `notify_task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;