<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Maxim Mzhelskiy <rus.engine@gmail.com>
 *
 */

require_once("Action.class.php");
require_once("ActionPlugin.class.php");

/**
 * Класс роутинга(контроллера)
 * Инициализирует ядро, определяет какой экшен запустить согласно URL'у и запускает его.
 *
 * @package framework.engine
 * @since 1.0
 */
class Router extends LsObject
{
    /**
     * Конфигурация роутинга, получается из конфига
     *
     * @var array
     */
    protected $aConfigRoute = array();
    /**
     * Текущий префикс в URL, может указывать, например, на язык: ru или en
     *
     * @var string|null
     */
    static protected $sPrefixUrl = null;
    /**
     * Порт при http запросе
     *
     * @var null
     */
    static protected $iHttpPort = null;
    /**
     * Порт при https запросе
     *
     * @var null
     */
    static protected $iHttpSecurePort = null;
    /**
     * Указывает на необходимость принудительного использования https
     *
     * @var bool
     */
    static protected $bHttpSecureForce = false;
    /**
     * Указывает на необходимость принудительного использования http
     *
     * @var bool
     */
    static protected $bHttpNotSecureForce = false;
    /**
     * Текущий экшен
     *
     * @var string|null
     */
    static protected $sAction = null;
    /**
     * Текущий евент
     *
     * @var string|null
     */
    static protected $sActionEvent = null;
    /**
     * Имя текущего евента
     *
     * @var string|null
     */
    static protected $sActionEventName = null;
    /**
     * Класс текущего экшена
     *
     * @var string|null
     */
    static protected $sActionClass = null;
    /**
     * Текущий полный ЧПУ url
     *
     * @var string|null
     */
    static protected $sPathWebCurrent = null;
    /**
     * Список параметров ЧПУ url
     * <pre>/action/event/param0/param1/../paramN/</pre>
     *
     * @var array
     */
    static protected $aParams = array();
    /**
     * Объект текущего экшена
     *
     * @var Action|null
     */
    protected $oAction = null;
    /**
     * Объект ядра
     *
     * @var Engine|null
     */
    protected $oEngine = null;
    /**
     * Покаывать или нет статистику выполнения
     *
     * @var bool
     */
    static protected $bShowStats = true;
    /**
     * Объект роутинга
     * @see getInstance
     *
     * @var Router|null
     */
    static protected $oInstance = null;

    /**
     * Делает возможным только один экземпляр этого класса
     *
     * @return Router
     */
    static public function getInstance()
    {
        if (isset(self::$oInstance) and (self::$oInstance instanceof self)) {
            return self::$oInstance;
        } else {
            self::$oInstance = new self();
            return self::$oInstance;
        }
    }

    /**
     * Загрузка конфига роутинга при создании объекта
     */
    public function __construct()
    {
        parent::__construct();
        $this->LoadConfig();
    }

    /**
     * Запускает весь процесс :)
     *
     */
    public function Exec($aParams = array())
    {
        $this->ParseUrl();
        if (isset($aParams['callback_after_parse_url'])) {
            /**
             * Для возможности изменять результат парсинга URL, например, для учета поддоменов
             */
            call_user_func($aParams['callback_after_parse_url']);
        }
        $this->DefineActionClass(); // Для возможности ДО инициализации модулей определить какой action/event запрошен
        $this->oEngine = Engine::getInstance();
        $this->oEngine->Init();
        $this->ExecAction();
        $this->Shutdown(false);
    }

    /**
     * Завершение работы роутинга
     *
     * @param bool $bExit Принудительно завершить выполнение скрипта
     */
    public function Shutdown($bExit = true)
    {
        $this->AssignVars();
        $this->oEngine->Shutdown();
        $this->Viewer_Display($this->oAction->GetTemplate());
        if ($bExit) {
            exit();
        }
    }

    /**
     * Парсим URL
     * Пример: http://site.ru/action/event/param1/param2/  на выходе получим:
     *  self::$sAction='action';
     *    self::$sActionEvent='event';
     *    self::$aParams=array('param1','param2');
     *
     */
    protected function ParseUrl()
    {
        $sReq = $this->GetRequestUri();
        $aRequestUrl = $this->GetRequestArray($sReq);

        /**
         * Проверяем на наличие префикса в URL
         */
        if ($sPrefixRule = Config::Get('router.prefix')) {
            if (isset($aRequestUrl[0]) and preg_match('#^' . $sPrefixRule . '$#i', $aRequestUrl[0])) {
                self::$sPrefixUrl = array_shift($aRequestUrl);
            } elseif ($sPrefixDefault = Config::Get('router.prefix_default')) {
                self::$sPrefixUrl = $sPrefixDefault;
            }
        }

        $aRequestUrl = $this->RewriteRequest($aRequestUrl);

        self::$sAction = array_shift($aRequestUrl);
        self::$sActionEvent = array_shift($aRequestUrl);
        self::$aParams = $aRequestUrl;
    }

    /**
     * Метод выполняет первичную обработку $_SERVER['REQUEST_URI']
     *
     * @return string
     */
    protected function GetRequestUri()
    {
        $sReq = preg_replace("/\/+/", '/', $_SERVER['REQUEST_URI']);
        $sReq = preg_replace("/^\/(.*)\/?$/U", '\\1', $sReq);
        $sReq = preg_replace("/^(.*)\?.*$/U", '\\1', $sReq);
        /**
         * Формируем $sPathWebCurrent ДО применения реврайтов
         */
        self::$sPathWebCurrent = self::GetPathRootWeb() . "/" . join('/', $this->GetRequestArray($sReq));
        return $sReq;
    }

    /**
     * Возвращает массив реквеста
     *
     * @param string $sReq Строка реквеста
     * @return array
     */
    protected function GetRequestArray($sReq)
    {
        $aRequestUrl = ($sReq == '') ? array() : explode('/', trim($sReq, '/'));
        for ($i = 0; $i < Config::Get('path.offset_request_url'); $i++) {
            array_shift($aRequestUrl);
        }
        $aRequestUrl = array_map('urldecode', $aRequestUrl);
        return $aRequestUrl;
    }

    /**
     * Применяет к реквесту правила реврайта из конфига Config::Get('router.uri')
     *
     * @param $aRequestUrl    Массив реквеста
     * @return array
     */
    protected function RewriteRequest($aRequestUrl)
    {
        /**
         * Правила Rewrite для REQUEST_URI
         */
        $sReq = implode('/', $aRequestUrl);
        if ($aRewrite = Config::Get('router.uri')) {
            $sReq = preg_replace(array_keys($aRewrite), array_values($aRewrite), $sReq);
        }
        return ($sReq == '') ? array() : explode('/', $sReq);
    }

    /**
     * Выполняет загрузку конфигов роутинга
     *
     */
    public function LoadConfig()
    {
        //Конфиг роутинга, содержит соответствия URL и классов экшенов
        $this->aConfigRoute = Config::Get('router');
        // Переписываем конфиг согласно правилу rewrite
        foreach ((array)$this->aConfigRoute['rewrite'] as $sPage => $sRewrite) {
            if (isset($this->aConfigRoute['page'][$sPage])) {
                $this->aConfigRoute['page'][$sRewrite] = $this->aConfigRoute['page'][$sPage];
                unset($this->aConfigRoute['page'][$sPage]);
            }
        }
    }

    /**
     * Загружает в шаблонизатор Smarty необходимые переменные
     *
     */
    protected function AssignVars()
    {
        $this->Viewer_Assign('sAction', $this->Standart(self::$sAction));
        $this->Viewer_Assign('sEvent', self::$sActionEvent);
        $this->Viewer_Assign('aParams', self::$aParams);
        $this->Viewer_Assign('PATH_WEB_CURRENT', func_urlspecialchars(self::$sPathWebCurrent));
    }

    /**
     * Запускает на выполнение экшен
     * Может запускаться рекурсивно если в одном экшене стоит переадресация на другой
     *
     */
    public function ExecAction()
    {
        $this->DefineActionClass();
        /**
         * Сначала запускаем инициализирующий евент
         */
        $this->Hook_Run('init_action');

        $sActionClass = $this->DefineActionClass();
        /**
         * Определяем наличие делегата экшена
         */
        if ($aChain = $this->Plugin_GetDelegationChain('action', $sActionClass)) {
            if (!empty($aChain)) {
                $sActionClass = $aChain[0];
            }
        }
        self::$sActionClass = $sActionClass;
        /**
         * Если класс экешна начинается с Plugin*_, значит необходимо загрузить объект из указанного плагина
         */
        if (!preg_match('/^Plugin([\w]+)_Action([\w]+)$/i', $sActionClass, $aMatches)) {
            //require_once(Config::Get('path.root.application').'/classes/actions/'.$sActionClass.'.class.php');
        } else {
            //require_once(Config::Get('path.root.application').'/plugins/'.func_underscore($aMatches[1]).'/classes/actions/Action'.ucfirst($aMatches[2]).'.class.php');
        }

        $sClassName = $sActionClass;
        $this->oAction = new $sClassName(self::$sAction);
        /**
         * Инициализируем экшен
         */
        $this->Hook_Run("action_init_" . strtolower($sActionClass) . "_before");
        $sInitResult = $this->oAction->Init();
        $this->Hook_Run("action_init_" . strtolower($sActionClass) . "_after");

        if ($sInitResult === 'next') {
            $this->ExecAction();
        } else {
            $res = $this->oAction->ExecEvent();
            self::$sActionEventName = $this->oAction->GetCurrentEventName();

            $this->Hook_Run("action_shutdown_" . strtolower($sActionClass) . "_before");
            $this->oAction->EventShutdown();
            $this->Hook_Run("action_shutdown_" . strtolower($sActionClass) . "_after");

            if ($res === 'next') {
                $this->ExecAction();
            }
        }
    }

    /**
     * Определяет какой класс соответствует текущему экшену
     *
     * @return string
     */
    protected function DefineActionClass()
    {
        if (isset($this->aConfigRoute['page'][self::$sAction])) {

        } elseif (self::$sAction === null) {
            self::$sAction = $this->aConfigRoute['config']['default']['action'];
            if (!is_null($sEvent = $this->aConfigRoute['config']['default']['event'])) {
                self::$sActionEvent = $sEvent;
            }
            if (is_array($aParams = $this->aConfigRoute['config']['default']['params'])) {
                self::$aParams = $aParams;
            }
            if (is_array($aRequest = $this->aConfigRoute['config']['default']['request'])) {
                foreach ($aRequest as $k => $v) {
                    if (!array_key_exists($k, $_REQUEST)) {
                        $_REQUEST[$k] = $v;
                    }
                }
            }
        } else {
            //Если не находим нужного класса то отправляем на страницу ошибки
            self::$sAction = $this->aConfigRoute['config']['action_not_found'];
            self::$sActionEvent = '404';
        }
        self::$sActionClass = $this->aConfigRoute['page'][self::$sAction];
        return self::$sActionClass;
    }

    /**
     * Функция переадресации на другой экшен
     * Если ею завершить евент в экшене то запуститься новый экшен
     * Пример: <pre>return Router::Action('error');</pre>
     *
     * @param string $sAction Экшен
     * @param string $sEvent Евент
     * @param array $aParams Список параметров
     * @return string 'next'
     */
    static public function Action($sAction, $sEvent = null, $aParams = null)
    {
        self::$sAction = self::getInstance()->Rewrite($sAction);
        self::$sActionEvent = $sEvent;
        if (is_array($aParams)) {
            self::$aParams = $aParams;
        }
        return 'next';
    }

    /**
     * Алиас короткого вызова перенаправления на экшен error с необходимым текстом ошибки
     *
     * @param string $sMsg Текст ошибки
     * @param string|null $sTitle Заголовок ошибки
     *
     * @return string
     */
    static public function ActionError($sMsg, $sTitle = null)
    {
        self::getInstance()->Message_AddErrorSingle($sMsg, $sTitle);
        return self::Action('error');
    }

    /**
     * Возвращает текущий ЧПУ url
     *
     * @return string
     */
    static public function GetPathWebCurrent()
    {
        return self::$sPathWebCurrent;
    }

    /**
     * Устанавливает текущий url
     *
     * @param string $sUrl
     */
    static public function SetPathWebCurrent($sUrl)
    {
        self::$sPathWebCurrent = $sUrl;
    }

    static public function GetFixPathWeb($sUrl, $bWithScheme = true)
    {
        $sResult = '';
        $aPathFull = parse_url($sUrl);
        $sPath = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $sUrl);
        if (isset($aPathFull['host'])) {
            $sHost = $aPathFull['host'];
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $sHost = $_SERVER['HTTP_HOST'];
        } else {
            $sHost = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
        }
        $bSecure = self::$bHttpSecureForce ? self::$bHttpSecureForce : (self::$bHttpNotSecureForce ? false : self::GetIsSecureConnection());
        if ($bWithScheme) {
            $sResult = ($bSecure ? 'https' : 'http') . '://';
        }
        $sResult .= $sHost;
        $iPort = $bSecure ? self::GetSecurePort() : self::GetPort();
        if (($iPort !== 80 && !$bSecure) || ($iPort !== 443 && $bSecure)) {
            $sResult .= ':' . $iPort;
        }
        $sResult .= rtrim($sPath, '\\/');
        return $sResult;
    }

    /**
     * Возвращает веб адрес сайта с учетом типа коннекта (http или https) и нестандартных портов
     *
     * @param bool $bWithScheme Возвращать в урле схему или нет
     *
     * @return string
     */
    static public function GetPathRootWeb($bWithScheme = true)
    {
        return self::GetFixPathWeb(Config::Get('path.root.web'), $bWithScheme);
    }

    /**
     * Возвращает порт при https запросе
     *
     * @return int|null
     */
    static public function GetSecurePort()
    {
        if (is_null(self::$iHttpSecurePort)) {
            self::$iHttpSecurePort = self::GetIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
        }
        return self::$iHttpSecurePort;
    }

    /**
     * Устанавливает порт
     *
     * @param $iPort
     */
    static public function SetSecurePort($iPort)
    {
        self::$iHttpSecurePort = $iPort;
    }

    /**
     * Возвращает порт при http запросе
     *
     * @return int|null
     */
    static public function GetPort()
    {
        if (is_null(self::$iHttpPort)) {
            self::$iHttpPort = !self::GetIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        }
        return self::$iHttpPort;
    }

    /**
     * Устанавливает порт
     *
     * @param $iPort
     */
    static public function SetPort($iPort)
    {
        self::$iHttpPort = $iPort;
    }

    /**
     * Возвращает текущий префикс URL
     *
     * @return string
     */
    static public function GetPrefixUrl()
    {
        return self::$sPrefixUrl;
    }

    /**
     * Устанавливает текущий префикс URL
     *
     * @param string $sPrefix
     */
    static public function SetPrefixUrl($sPrefix)
    {
        self::$sPrefixUrl = $sPrefix;
    }

    /**
     * Возвращает текущий экшен
     *
     * @return string
     */
    static public function GetAction()
    {
        return self::getInstance()->Standart(self::$sAction);
    }

    /**
     * Устанавливает новый текущий экшен
     *
     * @param string $sAction Экшен
     */
    static public function SetAction($sAction)
    {
        self::$sAction = $sAction;
    }

    /**
     * Возвращает текущий евент
     *
     * @return string
     */
    static public function GetActionEvent()
    {
        return self::$sActionEvent;
    }

    /**
     * Возвращает имя текущего евента
     *
     * @return string
     */
    static public function GetActionEventName()
    {
        return self::$sActionEventName;
    }

    /**
     * Возвращает класс текущего экшена
     *
     * @return string
     */
    static public function GetActionClass()
    {
        return self::$sActionClass;
    }

    /**
     * Устанавливает новый текущий евент
     *
     * @param string $sEvent Евент
     */
    static public function SetActionEvent($sEvent)
    {
        self::$sActionEvent = $sEvent;
    }

    /**
     * Возвращает параметры(те которые передаются в URL)
     *
     * @return array
     */
    static public function GetParams()
    {
        return self::$aParams;
    }

    /**
     * Возвращает параметр по номеру, если его нет то возвращается null
     * Нумерация параметров начинается нуля
     *
     * @param int $iOffset
     * @param mixed|null $def
     * @return string
     */
    static public function GetParam($iOffset, $def = null)
    {
        $iOffset = (int)$iOffset;
        return isset(self::$aParams[$iOffset]) ? self::$aParams[$iOffset] : $def;
    }

    /**
     * Устанавливает значение параметра
     *
     * @param int $iOffset Номер параметра, по идее может быть не только числом
     * @param mixed $value
     */
    static public function SetParam($iOffset, $value)
    {
        self::$aParams[$iOffset] = $value;
    }

    /**
     * Устанавливает новые текущие параметры
     *
     * @param string $aParams Параметры
     */
    static public function SetParams($aParams)
    {
        self::$aParams = $aParams;
    }

    /**
     * Показывать или нет статистику выполение скрипта
     * Иногда бывает необходимо отключить показ, например, при выводе RSS ленты
     *
     * @param bool $bState
     */
    static public function SetIsShowStats($bState)
    {
        self::$bShowStats = $bState;
    }

    /**
     * Возвращает статус показывать или нет статистику
     *
     * @return bool
     */
    static public function GetIsShowStats()
    {
        return self::$bShowStats;
    }

    /**
     * Проверяет запрос послан как ajax или нет
     *
     * @return bool
     */
    static public function GetIsAjaxRequest()
    {
        return isAjaxRequest();
    }

    /**
     * Проверяет тип коннекта - http или https
     *
     * @return bool
     */
    static public function GetIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)
        || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
    }

    /**
     * Блокируем копирование/клонирование объекта роутинга
     *
     */
    public function __clone()
    {
        throw new Exception('Not allow clone Router');
    }

    /**
     * Возвращает правильную адресацию по переданому названию страницы(экшену)
     *
     * @param  string $sAction Экшен или путь, например, "people/top" или "/"
     * @return string
     */
    static public function GetPath($sAction)
    {
        if (!$sAction or $sAction == '/') {
            return self::GetPathRootWeb() . (self::$sPrefixUrl ? '/' . self::$sPrefixUrl : '') . '/';
        }
        // Если пользователь запросил action по умолчанию
        $sPage = ($sAction == 'default')
            ? self::getInstance()->aConfigRoute['config']['default']['action']
            : $sAction;
        $aUrl = explode('/', $sPage);
        $sPage = $sPageOriginal = array_shift($aUrl);
        $sAdditional = join('/', $aUrl);
        // Смотрим, есть ли правило rewrite
        $sPage = self::getInstance()->Rewrite($sPage);
        /**
         * Если нет GET параметров, то добавляем в конец '/'
         */
        if ($sAdditional and strpos($sAdditional, '?') === false) {
            $sAdditional .= '/';
        }

        $bHttpSecureForceOld = self::$bHttpSecureForce;
        $bHttpNotSecureForceOld = self::$bHttpNotSecureForce;
        /**
         * Проверяем на необходимость принудительного использования https
         */
        $aActionsSecure = (array)Config::Get('router.force_secure');
        if ($aActionsSecure) {
            if (in_array($sPageOriginal, (array)Config::Get('router.force_secure'))) {
                self::$bHttpSecureForce = true;
            } else {
                self::$bHttpNotSecureForce = true;
            }
        }
        $sPath = self::GetPathRootWeb() . (self::$sPrefixUrl ? '/' . self::$sPrefixUrl : '') . "/$sPage/" . ($sAdditional ? "{$sAdditional}" : '');
        /**
         * Возвращаем значения обратно
         */
        self::$bHttpSecureForce = $bHttpSecureForceOld;
        self::$bHttpNotSecureForce = $bHttpNotSecureForceOld;
        return $sPath;
    }

    /**
     * Проверяет на соответствие текущего экшена/евента переданным
     *
     * @param array $aActions Список экшенов с евентами в формате array('action1','action1','action3'=>array('event1','event2'))
     * @return bool
     */
    static public function CheckIsCurrentAction($aActions)
    {
        $bAllow = false;
        if (!is_array($aActions)) {
            $aActions = array($aActions);
        }
        foreach ($aActions as $mKey => $sAction) {
            if (is_int($mKey)) {
                $aEvents = array();
            } else {
                $aEvents = $sAction;
                $sAction = $mKey;
            }
            if (self::GetAction() == $sAction) {
                if ($aEvents) {
                    if (in_array(self::GetActionEvent(), $aEvents)) {
                        $bAllow = true;
                        break;
                    }
                } else {
                    $bAllow = true;
                    break;
                }
            }
        }
        return $bAllow;
    }

    /**
     * Try to find rewrite rule for given page.
     * On success return rigth page, else return given param.
     *
     * @param  string $sPage
     * @return string
     */
    public function Rewrite($sPage)
    {
        return (isset($this->aConfigRoute['rewrite'][$sPage]))
            ? $this->aConfigRoute['rewrite'][$sPage]
            : $sPage;
    }

    /**
     * Стандартизирует определение внутренних ресурсов.
     *
     * Пытается по переданому экшену найти rewrite rule и
     * вернуть стандартное название ресурса.
     *
     * @see    Rewrite
     * @param  string $sPage
     * @return string
     */
    public function Standart($sPage)
    {
        $aRewrite = array_flip($this->aConfigRoute['rewrite']);
        return (isset($aRewrite[$sPage]))
            ? $aRewrite[$sPage]
            : $sPage;
    }

    /**
     * Выполняет редирект, предварительно завершая работу Engine
     *
     * @param string $sLocation URL для редиректа
     * @param bool $bSaveMessages Перенести системные сообщения на следующую страницу
     */
    static public function Location($sLocation, $bSaveMessages = false)
    {
        if ($bSaveMessages) {
            self::getInstance()->Message_SaveMessages();
        }
        self::getInstance()->oEngine->Shutdown();
        func_header_location($sLocation);
    }

    /**
     * Выполняет локальный редирект, предварительно завершая работу Engine
     *
     * @param string $sLocation локальный адрес, который можно использовать в Router::GetPath();, например, 'blog/news'
     */
    static public function LocationAction($sLocation)
    {
        self::Location(self::GetPath($sLocation));
    }
}