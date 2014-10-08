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
 * Сущность крон-задачи
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCron_EntityTask extends EntityORM
{

    /**
     * Определяем правила валидации
     *
     * @var array
     */
    protected $aValidateRules = array(
        array('title', 'string', 'max' => 200, 'min' => 1, 'allowEmpty' => false, 'label' => 'Название'),
        array('method', 'string', 'max' => 300, 'min' => 1, 'allowEmpty' => false, 'label' => 'Метод'),
        array('period_run', 'number', 'integerOnly' => true, 'min' => 5, 'allowEmpty' => false, 'label' => 'Период'),

        array('title', 'title_check'),
        array('method', 'method_check'),
        array('plugin', 'plugin_check'),
        array('state', 'state_check'),
    );

    /**
     * Выполняется перед сохранением
     *
     * @return bool
     */
    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            if ($this->_isNew()) {
                $this->setDateCreate(date("Y-m-d H:i:s"));
            }
        }
        return $bResult;
    }

    public function ValidateTitleCheck()
    {
        if (!$this->_hasValidateErrors()) {
            $this->setTitle(htmlspecialchars($this->getTitle()));
        }
        return true;
    }

    public function ValidateMethodCheck()
    {
        if (!$this->_hasValidateErrors()) {
            $this->setMethod(htmlspecialchars($this->getMethod()));
        }
        return true;
    }

    public function ValidatePluginCheck()
    {
        if (!$this->_hasValidateErrors() and $this->getPlugin()) {
            $this->setPlugin(htmlspecialchars($this->getPlugin()));
        }
        return true;
    }

    public function ValidateStateCheck($sValue, $aParams)
    {
        $this->setState($this->getState() == ModuleCron::TASK_STATE_ACTIVE ? ModuleCron::TASK_STATE_ACTIVE : ModuleCron::TASK_STATE_NOT_ACTIVE);
        return true;
    }

    /**
     * Возвращает заголовок задачи, считая, что в поле содержится языковой код текстовки
     *
     * @return string
     */
    public function getTitleWithLang()
    {
        return $this->Lang_Get($this->getTitle());
    }

    /**
     * Запускает задачу на выполнение
     *
     * @return mixed
     */
    public function run()
    {
        return $this->Cron_RunTask($this);
    }

    /**
     * Выполняется перед запуском задачи
     * В этом методе должна быть реализованна логика по инициализации окружения для выполнения задачи,
     * например, при $bFork=true нужно убедиться в корректности ресурсов, таких как подключение к БД и т.п.
     *
     * @param $bFork
     */
    public function beforeRun($bFork)
    {

    }
}