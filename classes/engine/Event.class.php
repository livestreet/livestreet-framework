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
 * Абстрактный класс внешнего обработчика евента.
 *
 * От этого класса наследуются внешние обработчики евентов.
 *
 * @package framework.engine
 * @since 2.0
 */
abstract class Event extends LsObject
{

    /**
     * Объект текущего экшена
     *
     * @var null|Action
     */
    protected $oAction = null;
    /**
     * Объект для анализа структуры класса экшена
     *
     * @var null
     */
    protected $oActionReflection = null;

    /**
     * Устанавливает объект экшена
     *
     * @param Action $oAction Объект текущего экшена
     */
    public function SetActionObject($oAction)
    {
        $this->oAction = $oAction;
        $this->oActionReflection = new ReflectionClass($this->oAction);
    }

    /**
     * Запускается для обработки евента, если у него не указанно имя, например, "User::"
     */
    public function Exec()
    {

    }

    /**
     * Запускается всегда перед вызовом метода евента
     */
    public function Init()
    {

    }

    public function __get($sName)
    {
        if ($this->oActionReflection->hasProperty($sName)) {
            return call_user_func_array(array($this->oAction, 'ActionGet'), array($sName));
        }
        return parent::__get($sName);
    }

    public function __set($sName, $mValue)
    {
        if ($this->oActionReflection->hasProperty($sName)) {
            return call_user_func_array(array($this->oAction, 'ActionSet'), array($sName, $mValue));
        }
    }

    public function __call($sName, $aArgs)
    {
        /**
         * Обработка вызова методов экшена
         */
        if ($this->oAction->ActionCallExists($sName)) {
            array_unshift($aArgs, $sName);
            return call_user_func_array(array($this->oAction, 'ActionCall'), $aArgs);
        }

        return parent::__call($sName, $aArgs);
    }
}