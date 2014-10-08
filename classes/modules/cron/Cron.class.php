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
 * Модуль управления центральным кроном - запуск запланированных задач
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCron extends ModuleORM
{

    const TASK_STATE_NOT_ACTIVE = 0;
    const TASK_STATE_ACTIVE = 1;

    /**
     * Запускает выполнение центрального крона
     * Выбирает необходимые задачи и выполняет их
     */
    public function RunMain()
    {
        /**
         * Получаем список активных задач
         * TODO: можно сделать выборку нужных задач сразу из БД, а не сравнивать в php время
         */
        $aTasks = $this->GetTaskItemsByFilter(array('state' => self::TASK_STATE_ACTIVE));
        $aTasksReady = array();
        foreach ($aTasks as $oTask) {
            if (!$oTask->getDateRunLast() or (strtotime($oTask->getDateRunLast()) + $oTask->getPeriodRun() * 60 < time())) {
                $aTasksReady[] = $oTask;
            }
        }

        if (Config::Get('module.cron.use_fork')) {
            $aResult = \iFixit\Forker\Forker::map($aTasksReady, function ($iIndex, $oTask) {
                /**
                 * Производим переподключение к основной БД
                 */
                $this->Database_ReConnect();
                $oTask->beforeRun(true);
                return $oTask->run();
            });
        } else {
            foreach ($aTasksReady as $oTask) {
                $oTask->beforeRun(false);
                $oTask->run();
            }
        }
    }

    /**
     * Запускает задачу на выполнение
     *
     * @param $oTask
     *
     * @return array
     */
    public function RunTask($oTask)
    {
        $aLog = array(
            'state'  => 'successful',
            'return' => null,
        );
        /**
         * Запускаем
         */
        try {
            $aLog['return'] = call_user_func(array($this, $oTask->getMethod()), $oTask);
        } catch (Exception $e) {
            $aLog['state'] = 'error';
            $aLog['message'] = $e->getMessage() . ' (code:' . $e->getCode() . ';line:' . $e->getLine() . ')';
        }
        /**
         * Обновляем количество пусков и дату последнего запуска
         */
        $oTask->setCountRun($oTask->getCountRun() + 1);
        $oTask->setDateRunLast(date('Y-m-d H:i:s'));
        $oTask->Update();
        /**
         * Записываем в лог
         */
        $this->WriteLog('Run cron task "' . $oTask->getTitleWithLang() . '"', $aLog);
        return $aLog;
    }

    /**
     * Записывает сообщение в лог крона
     *
     * @param       $sMsg
     * @param array $aData
     */
    protected function WriteLog($sMsg, $aData = array())
    {
        $this->Logger_Notice($sMsg, $aData, 'cron');
    }

    /**
     * Создает новую задачу в БД
     * Метод предназначен для использования в плагинах в момент их активации
     *
     * @param        $sTitle
     * @param        $sMethod
     * @param        $iPeriod
     * @param string $sPlugin
     *
     * @return ModuleCron_EntityTask|string
     */
    public function CreateTask($sTitle, $sMethod, $iPeriod, $sPlugin = null)
    {
        $sPlugin = $sPlugin ? Plugin::GetPluginCode($sPlugin) : '';
        if ($oTask = $this->GetTaskByMethodAndPlugin($sMethod, $sPlugin)) {
            return $oTask;
        }
        $oTask = Engine::GetEntity('ModuleCron_EntityTask');
        $oTask->setTitle($sTitle);
        $oTask->setMethod($sMethod);
        $oTask->setPeriodRun($iPeriod);
        $oTask->setPlugin($sPlugin);
        $oTask->setState(self::TASK_STATE_ACTIVE);
        if ($oTask->_Validate()) {
            $oTask->Add();
            return $oTask;
        } else {
            return $oTask->_getValidateError();
        }
    }

    /**
     * Удаляет все крон-задачи конкретного плагина
     *
     * @param $sPlugin
     */
    public function RemoveTasksByPlugin($sPlugin)
    {
        if ($sPlugin = Plugin::GetPluginCode($sPlugin)) {
            $aTasks = $this->GetTaskItemsByPlugin($sPlugin);
            foreach ($aTasks as $oTask) {
                $oTask->Delete();
            }
        }
    }
}