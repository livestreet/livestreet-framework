<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * !!!!! ВНИМАНИЕ !!!!!
 *
 * Ничего не изменяйте в этом файле!
 * Все изменения нужно вносить в файл /application/config/config.local.php
 */

/**
 * Настройки HTML вида
 */
$config['view']['skin'] = 'developer';              // шаблон(скин)
$config['view']['theme'] = 'default';                // тема оформления шаблона (шаблон должен поддерживать темы)
$config['view']['name'] = 'Your Site';              // название сайта
$config['view']['title_separator'] = ' / ';                     // Разделитель HTML заголовков страниц
$config['view']['title_sort_reverse'] = true;                 // Сортировать части HTML заголовка страницы в обратном порядке
$config['view']['description'] = 'Description your site';  // seo description
$config['view']['keywords'] = 'site, google, internet'; // seo keywords
$config['view']['wysiwyg'] = false;                    // использовать или нет визуальный редактор TinyMCE
$config['view']['noindex'] = true;                     // "прятать" или нет ссылки от поисковиков, оборачивая их в тег <noindex> и добавляя rel="nofollow"
$config['view']['mod_delimiter'] = '--';                     // Разделитель между названием компонента и модификтором
$config['view']['rtl'] = false;                    // Поддержка RTL языков

/**
 * Настройка пагинации
 */
$config['pagination']['pages']['count'] = 4;                  // количество ссылок на другие страницы в пагинации

/**
 * Настройки путей
 * Основные
 */
$config['path']['root']['server'] = dirname(dirname(dirname(__FILE__))); // Из расчета, что каталог с фреймворком лежит в корне сайта, иначе нужно переопределить настройку в конфиге /application/config/config.php
$config['path']['root']['web'] = isset($_SERVER['HTTP_HOST']) ? 'http://' . $_SERVER['HTTP_HOST'] : null;
$config['path']['application']['server'] = '___path.root.server___/application';
$config['path']['application']['web'] = '___path.root.web___/application';
$config['path']['framework']['server'] = dirname(dirname(__FILE__));
$config['path']['framework']['web'] = '___path.root.web___/' . trim(str_replace(dirname(dirname(dirname(__FILE__))), '',
            $config['path']['framework']['server']),
        '/\\'); // Подставляет название каталога в котором фреймворк, относительно корня сайта. Необходимо переопределить при изменении расположения фреймворка.
/**
 * Производные
 */
$config['path']['application']['plugins']['server'] = '___path.application.server___/plugins';
$config['path']['application']['plugins']['web'] = '___path.application.web___/plugins';
$config['path']['framework']['libs_vendor']['server'] = '___path.framework.server___/libs/vendor';
$config['path']['framework']['libs_vendor']['web'] = '___path.framework.web___/libs/vendor';
$config['path']['framework']['libs_application']['server'] = '___path.framework.server___/libs/application';
$config['path']['framework']['libs_application']['web'] = '___path.framework.web___/libs/application';
$config['path']['framework']['frontend']['web'] = '___path.framework.web___/frontend/framework';
$config['path']['skin']['web'] = '___path.application.web___/frontend/skin/___view.skin___';
$config['path']['skin']['assets']['web'] = '___path.skin.web___/assets';
$config['path']['uploads']['base'] = '/uploads';
$config['path']['uploads']['images'] = '___path.uploads.base___/images';
$config['path']['tmp']['server'] = '___path.application.server___/tmp';
$config['path']['cache_assets']['server'] = '___path.tmp.server___/templates/cache';
$config['path']['offset_request_url'] = 0;                                                       // иногда помогает если сервер использует внутренние реврайты
/**
 * Для совместимости с прошлыми версиями
 * Данные настройки будут удалены
 */
//$config['path']['root']['application']     	= '___path.root.server___/application';           // полный путь до сайта в файловой системе
$config['path']['root']['engine'] = '___path.framework.server___';                         // полный путь до сайта в файловой системе;
$config['path']['root']['engine_lib'] = '___path.framework.web___/libs';                        // полный путь до сайта в файловой системе
//$config['path']['root']['framework']		= '___path.root.engine___';
$config['path']['static']['root'] = '___path.root.web___';                                   // чтоб можно было статику засунуть на отдельный сервер
$config['path']['static']['skin'] = '___path.skin.web___';
//$config['path']['static']['assets']         = '___path.static.skin___/assets';                         // Папка с ассетами (js, css, images)
//$config['path']['static']['framework']      = "___path.static.root___/framework/frontend/framework";   // Front-end framework todo: need fix path
$config['path']['uploads']['root'] = '___path.uploads.base___';                                              // директория для загрузки файлов

/**
 * Настройки шаблонизатора Smarty
 */
$config['path']['smarty']['template'] = '___path.application.server___/frontend/skin/___view.skin___';
$config['path']['smarty']['compiled'] = '___path.tmp.server___/templates/compiled';
$config['path']['smarty']['cache'] = '___path.tmp.server___/templates/cache';
$config['path']['smarty']['plug'] = '___path.framework.server___/classes/modules/viewer/plugs';
$config['smarty']['compile_check'] = true; // Проверять или нет файлы шаблона на изменения перед компиляцией, false может значительно увеличить быстродействие, но потребует ручного удаления кеша при изменения шаблона
$config['smarty']['force_compile'] = false; // Принудительно компилировать шаблоны при каждом запросе, true - существенно снижает производительность
/**
 * Настройки плагинов
 */
$config['sys']['plugins']['activation_file'] = 'plugins.dat'; // файл со списком активных плагинов в каталоге /plugins/
/**
 * Системные настройки модулей
 */
$config['sys']['module']['use_auto_hooks'] = true; // использовать генерацию автоматических хуков для методов модулей (_before/_after). Отключение поднимает производительность, но может нарушить работу некоторых плагинов.
/**
 * Настройка каптчи
 */
$config['sys']['captcha']['type'] = 'kcaptcha'; // тип используемой каптчи: kcaptcha, recaptcha
/**
 * Настройки куков
 */
$config['sys']['cookie']['host'] = null;                    // хост для установки куков
$config['sys']['cookie']['path'] = '/';                     // путь для установки куков
/**
 * Настройки сессий
 */
$config['sys']['session']['standart'] = true;                             // Использовать или нет стандартный механизм сессий
$config['sys']['session']['name'] = 'PHPSESSID';                      // название сессии
$config['sys']['session']['timeout'] = null;                             // Тайм-аут сессии в секундах
$config['sys']['session']['host'] = '___sys.cookie.host___'; // хост сессии в куках
$config['sys']['session']['path'] = '___sys.cookie.path___'; // путь сессии в куках
/**
 * Настройки почтовых уведомлений
 */
$config['sys']['mail']['type'] = 'mail';                 // Какой тип отправки использовать
$config['sys']['mail']['from_email'] = 'admin@admin.adm';      // Мыло с которого отправляются все уведомления
$config['sys']['mail']['from_name'] = 'Почтовик Your Site';  // Имя с которого отправляются все уведомления
$config['sys']['mail']['charset'] = 'UTF-8';                // Какую кодировку использовать в письмах
$config['sys']['mail']['smtp']['host'] = 'localhost';            // Настройки SMTP - хост
$config['sys']['mail']['smtp']['port'] = 25;                     // Настройки SMTP - порт
$config['sys']['mail']['smtp']['user'] = '';                     // Настройки SMTP - пользователь
$config['sys']['mail']['smtp']['password'] = '';                     // Настройки SMTP - пароль
$config['sys']['mail']['smtp']['secure'] = '';                     // Настройки SMTP - протокол шифрования: tls, ssl
$config['sys']['mail']['smtp']['auth'] = true;                   // Использовать авторизацию при отправке
$config['sys']['mail']['include_comment'] = true;                   // Включает в уведомление о новых комментах текст коммента
$config['sys']['mail']['include_talk'] = true;                   // Включает в уведомление о новых личных сообщениях текст сообщения
/**
 * Настройки кеширования
 */
// Устанавливаем настройки кеширования
$config['sys']['cache']['use'] = false;               // использовать кеширование или нет
$config['sys']['cache']['type'] = 'file';             // тип кеширования: file, xcache и memory. memory использует мемкеш, xcache - использует XCache
$config['sys']['cache']['dir'] = '___path.tmp.server___/';       // каталог для файлового кеша, также используется для временных картинок. По умолчанию подставляем каталог для хранения сессий
$config['sys']['cache']['prefix'] = 'livestreet_cache'; // префикс кеширования, чтоб можно было на одной машине держать несколько сайтов с общим кешевым хранилищем
$config['sys']['cache']['directory_level'] = 1;         // уровень вложенности директорий файлового кеша
$config['sys']['cache']['solid'] = true;               // Настройка использования раздельного и монолитного кеша для отдельных операций

/**
 * Настройки логирования
 */
$config['sys']['logs']['file'] = 'log.log';       // файл общего лога
$config['sys']['logs']['cron_file'] = 'cron.log';      // файл лога крон-процессов
$config['sys']['logs']['sql_query'] = false;           // логировать или нет SQL запросы
$config['sys']['logs']['sql_query_file'] = 'sql_query.log'; // файл лога SQL запросов
$config['sys']['logs']['sql_error'] = true;            // логировать или нет ошибки SQl
$config['sys']['logs']['sql_error_file'] = 'sql_error.log'; // файл лога ошибок SQL
$config['sys']['logs']['cron'] = true;            // логировать или нет cron скрипты
$config['sys']['logs']['php'] = true;            // логировать или нет PHP ошибки
$config['sys']['logs']['console'] = false;            // позволяет удобно выводить отладочную информацию через консоль браузера
$config['sys']['logs']['format'] = "[%datetime%] %channel%.%level_name% %extra.process_id% %extra.uid%: %message% %context%\n"; // Дефолтный формат логов
/**
 * Конфигурация инстансов логгера
 */
$config['sys']['logs']['instances'] = array(
    /**
     * Стандартный поток логов
     */
    'default'  => array(
        'handlers'   => array(
            'Stream' => array(
                '___path.application.server___/logs/___sys.logs.file___',
                'debug',
                'formatter' => array(
                    'Line',
                    '___sys.logs.format___'
                )
            ),
        ),
        'processors' => array(
            'Uid',
            'ProcessId',
        )
    ),
    /**
     * Логи запросов к БД
     */
    'db_query' => array(
        'handlers'   => array(
            'Stream' => array(
                '___path.application.server___/logs/___sys.logs.sql_query_file___',
                'debug',
                'formatter' => array(
                    'Line',
                    '___sys.logs.format___',
                    null,
                    true
                )
            ),
        ),
        'processors' => array(
            'Uid',
            'ProcessId',
        )
    ),
    /**
     * Логи ошибок к БД
     */
    'db_error' => array(
        'handlers'   => array(
            'Stream' => array(
                '___path.application.server___/logs/___sys.logs.sql_error_file___',
                'debug',
                'formatter' => array(
                    'Line',
                    '___sys.logs.format___',
                    null,
                    true
                )
            ),
        ),
        'processors' => array(
            'Uid',
            'ProcessId',
        )
    ),
    /**
     * Логи cron скриптов
     */
    'cron'     => array(
        'handlers'   => array(
            'Stream' => array(
                '___path.application.server___/logs/___sys.logs.cron_file___',
                'debug',
                'formatter' => array(
                    'Line',
                    '___sys.logs.format___'
                )
            ),
        ),
        'processors' => array(
            'Uid',
            'ProcessId',
        )
    ),
    /**
     * Вывод собщений в консоле браузера
     */
    'console'  => array(
        'handlers' => array(
            'BrowserConsole' => array(),
        ),
    ),
);

/**
 * Дополнительные настройки отладки
 */
$config['sys']['debug']['action_error'] = true;        // Выводить или нет отладочную информацию при использовании метода Action->EventErrorDebug();
/**
 * Языковые настройки
 */
$config['lang']['current'] = 'ru';                                                // текущий язык текстовок
$config['lang']['default'] = 'ru';                                                // язык, который будет использовать на сайте по умолчанию
$config['lang']['dir'] = 'i18n';                                              // название директории с языковыми файлами
$config['lang']['path'] = '___path.application.server___/frontend/___lang.dir___';   // полный путь до языковых файлов
$config['lang']['load_to_js'] = array();                                             // Массив текстовок, которые необходимо прогружать на страницу в виде JS хеша, позволяет использовать текстовки внутри js
/**
 * Настройки модулей
 */
// Модуль Text
$config['module']['text']['use_markdown'] = false;       // Если установлена true, текст будет обрабатываться как Markdown
// Модуль Lang
$config['module']['lang']['delete_undefined'] = true;   // Если установлена true, то модуль будет автоматически удалять из языковых конструкций переменные вида %%var%%, по которым не была произведена замена
// Для совместимости со старыми версиями
$config['module']['lang']['i18n_mapping'] = array(
    /*
     * новый формат записи => старый
     */
    'ru' => 'russian',
    'ua' => 'ukrainian',
    'en' => 'english',
    'de' => 'deutsch',
);
// Модуль Notify
$config['module']['notify']['delayed'] = false;    // Указывает на необходимость использовать режим отложенной рассылки сообщений на email
$config['module']['notify']['insert_single'] = false;    // Если опция установлена в true, систему будет собирать записи заданий удаленной публикации, для вставки их в базу единым INSERT
$config['module']['notify']['per_process'] = 10;       // Количество отложенных заданий, обрабатываемых одним крон-процессом
$config['module']['notify']['dir'] = 'emails'; // Путь до папки с емэйлами относительно шаблона
$config['module']['notify']['prefix'] = 'email';  // Префикс шаблонов емэйлов
// Модуль Image
$config['module']['image']['driver'] = 'gd';
$config['module']['image']['params']['default']['size_max_width'] = 7000;
$config['module']['image']['params']['default']['size_max_height'] = 7000;
$config['module']['image']['params']['default']['format_auto'] = true;
$config['module']['image']['params']['default']['format'] = 'jpg';
$config['module']['image']['params']['default']['quality'] = 95;
$config['module']['image']['params']['default']['watermark_use'] = false;    // Использовать ватермарк или нет
$config['module']['image']['params']['default']['watermark_type'] = 'image'; // Тип: image - накладывается изображение. Другие типы пока не поддерживаются
$config['module']['image']['params']['default']['watermark_image'] = null; // Полный серверный путь до картинки ватермарка
$config['module']['image']['params']['default']['watermark_position'] = 'bottom-right'; // Значения: bottom-left, bottom-right, top-left, top-right, center
$config['module']['image']['params']['default']['watermark_min_width'] = 100; // Минимальная ширина изображения, начиная с которой будет наложен ватермарк
$config['module']['image']['params']['default']['watermark_min_height'] = 100; // Минимальная высота изображения, начиная с которой будет наложен ватермарк
/**
 * Модуль Asset
 * Параметры обработки css/js-файлов
 */
$config['module']['asset']['force_https'] = true; // При использовании https принудительно заменять http на https у путях до css/js
$config['module']['asset']['css']['merge'] = true; // указывает на необходимость слияния css файлов
$config['module']['asset']['css']['compress'] = false; // указывает на необходимость компрессии файлов. Компрессия используется только в активированном режиме слияния файлов.
$config['module']['asset']['js']['merge'] = true; // указывает на необходимость слияния js файлов
$config['module']['asset']['js']['compress'] = false; // указывает на необходимость компрессии файлов. Компрессия используется только в активированном режиме слияния файлов.
// Модуль Security
$config['module']['security']['hash'] = "livestreet_security_key"; // "примесь" к строке, хешируемой в качестве security-кода
// Модуль Cron
$config['module']['cron']['use_fork'] = false; // Использовать параллельное выполнение задач через fork. Данный режим до конца не протестирован.
// Модуль Ls
$config['module']['ls']['send_general'] = true;    // Отправка на сервер LS общей информации о сайте (домен, версия LS и плагинов)
$config['module']['ls']['use_counter'] = true;    // Использование счетчика GA
/**
 * Модуль Validate
 */
// Настройки Google рекаптчи - https://www.google.com/recaptcha/admin#createsite
$config['module']['validate']['recaptcha']= array(
    'site_key' => '', // Ключ
    'secret_key' => '', // Секретный ключ
    'use_ip' => false, // Использовать при валидации IP адрес клиента
);

// Какие модули должны быть загружены на старте
$config['module']['autoLoad'] = array('Hook', 'Cache', 'Logger', 'Security', 'Session', 'Lang', 'Message');
/**
 * Настройка базы данных
 */
$config['db']['params']['host'] = 'localhost';
$config['db']['params']['port'] = '3306';
$config['db']['params']['user'] = 'root';
$config['db']['params']['pass'] = '';
$config['db']['params']['type'] = 'mysqli';
$config['db']['params']['dbname'] = 'social';
/**
 * Настройка таблиц базы данных
 */
$config['db']['table']['prefix'] = 'prefix_';
$config['db']['table']['notify_task'] = '___db.table.prefix___notify_task';
$config['db']['table']['plugin_manager_migration'] = '___db.table.prefix___plugin_migration';
$config['db']['table']['plugin_manager_version'] = '___db.table.prefix___plugin_version';
$config['db']['table']['storage'] = '___db.table.prefix___storage';
$config['db']['tables']['engine'] = 'InnoDB';  // InnoDB или MyISAM
/**
 * Настройка memcache
 */
$config['memcache']['servers'][0]['host'] = 'localhost';
$config['memcache']['servers'][0]['port'] = '11211';
$config['memcache']['servers'][0]['persistent'] = true;
$config['memcache']['compression'] = true;
/**
 * Настройки роутинга
 */
$config['router']['prefix'] = null; // Позволяет задать префикс URL, например, текущий язык сайта ru. В качестве значения используется регулярное выражение, например, '(ru)|(en)|(de)'
$config['router']['prefix_default'] = null; // Дефолтный префикс URL, указывается если в URL не определен свой префикс
$config['router']['rewrite'] = array();
// Правила реврайта для REQUEST_URI
$config['router']['uri'] = array();
// Распределение action
$config['router']['page']['error'] = 'ActionError';
$config['router']['page']['index'] = 'ActionIndex';
// Глобальные настройки роутинга
$config['router']['config']['default']['action'] = 'index';
$config['router']['config']['default']['event'] = null;
$config['router']['config']['default']['params'] = null;
$config['router']['config']['default']['request'] = null;
$config['router']['config']['action_not_found'] = 'error';
// Принудительное использование https для экшенов
$config['router']['force_secure'] = array();

$config['head']['default']['js'] = array();
$config['head']['default']['css'] = array();
/**
 * Установка локали
 */
setlocale(LC_ALL, "ru_RU.UTF-8");
date_default_timezone_set('Europe/Moscow'); // See http://php.net/manual/en/timezones.php

/**
 * Настройки типографа текста Jevix
 */
$config['jevix'] = require(dirname(__FILE__) . '/jevix.php');


return $config;