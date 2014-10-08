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

/**
 * Абстрактный класс для работы с крон-процессами.
 * Например, его использует отложенная рассылка почтовых уведомлений для пользователей.
 * Обработчик крона не запускается автоматически(!!), его необходимо добавлять в системный крон (nix*: crontab -e)
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Cron extends LsObject
{
    /**
     * Производить логирование или нет
     *
     * @var bool
     */
    protected $bLogEnable = true;
    /**
     * Дескриптор блокирующего файла
     * Если этот файл существует, то крон не запустится повторно.
     *
     * @var string
     */
    protected $oLockFile = null;
    /**
     * Имя процесса, под которым будут помечены все сообщения в логах
     *
     * @var string
     */
    protected $sProcessName;

    /**
     * @param string|null $sLockFile Полный путь до лок файла, например <pre>Config::Get('sys.cache.dir').'notify.lock'</pre>
     */
    public function __construct($sLockFile = null)
    {
        parent::__construct();
        $this->sProcessName = get_class($this);
        $oEngine = Engine::getInstance();
        /**
         * Инициализируем ядро
         */
        $oEngine->Init();

        if (!empty($sLockFile)) {
            $this->oLockFile = fopen($sLockFile, 'a');
        }
        /**
         * Инициализируем лог и делает пометку о старте процесса
         */
        $this->Log('Cron process started');
    }

    /**
     * Делает запись в лог
     *
     * @param  string $sMsg Сообщение для записи в лог
     */
    public function Log($sMsg)
    {
        if ($this->bLogEnable and Config::Get('sys.logs.cron')) {
            $sMsg = $this->sProcessName . ': ' . $sMsg;
            $this->Logger_Notice($sMsg, array(), 'cron');
        }
    }

    /**
     * Проверяет уникальность создаваемого процесса
     *
     * @return bool
     */
    public function isLock()
    {
        return ($this->oLockFile && !flock($this->oLockFile, LOCK_EX | LOCK_NB));
    }

    /**
     * Снимает блокировку на повторный процесс
     *
     * @return bool
     */
    public function unsetLock()
    {
        return ($this->oLockFile && @flock($this->oLockFile, LOCK_UN));
    }

    /**
     * Основной метод крон-процесса.
     * Реализует логику работы крон процесса с последующей передачей управления на пользовательскую функцию
     *
     * @return string
     */
    public function Exec()
    {
        /**
         * Если выполнение процесса заблокирован, завершаемся
         */
        if ($this->isLock()) {
            $this->Log('Try to exec already run process');
            return;
        }
        /**
         * Здесь мы реализуем дополнительную логику:
         * логирование вызова, обработка ошибок,
         * буферизация вывода.
         */
        ob_start();
        $this->Client();
        /**
         * Получаем весь вывод функции.
         */
        $sContent = ob_get_contents();
        ob_end_clean();

        return $sContent;
    }

    /**
     * Завершение крон-процесса
     */
    public function Shutdown()
    {
        $this->unsetLock();
        $this->Log('Cron process ended');
    }

    /**
     * Вызывается при уничтожении объекта
     */
    public function __destruct()
    {
        $this->Shutdown();
    }

    /**
     * Клиентская функция будет переопределятся в наследниках класса
     * для обеспечивания выполнения основного функционала.
     */
    abstract public function Client();
}