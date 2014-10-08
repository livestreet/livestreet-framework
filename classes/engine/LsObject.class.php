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
 * От этого класса наследуются все остальные
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class LsObject
{
    /**
     * Список поведений
     *
     * <pre>
     * array(
     *    'property'=>array(
     *        'class'=>'ModuleProperty_BehaviorProperty',
     *        'param1'=>12345,
     *        'param2'=>'two'
     *    ),
     *    'category'=>$oCategoryBehavior
     * )
     * </pre>
     *
     * @var array
     */
    protected $aBehaviors = array();
    /**
     * Список поведений в виде готовых объектов, формируется автоматически
     *
     * @var
     */
    protected $_aBehaviors;

    /**
     * Конструктор, запускается автоматически при создании объекта
     */
    public function __construct()
    {

    }

    /**
     * При клонировании сбрасываем поведения
     */
    public function __clone()
    {
        $this->_aBehaviors = null;
    }

    /**
     * Возвращает все объекты поведения
     *
     * @return array
     */
    public function GetBehaviors()
    {
        $this->PrepareBehaviors();
        return $this->_aBehaviors;
    }

    /**
     * Возвращает объект поведения по его имени
     *
     * @param string $sName
     *
     * @return Behavior|null
     */
    public function GetBehavior($sName)
    {
        $this->PrepareBehaviors();
        return isset($this->_aBehaviors[$sName]) ? $this->_aBehaviors[$sName] : null;
    }

    /**
     * Инициализация поведений
     */
    protected function PrepareBehaviors()
    {
        if (is_null($this->_aBehaviors)) {
            $this->_aBehaviors = array();
            foreach ($this->aBehaviors as $sName => $mBehavior) {
                $this->AttachBehavior($sName, $mBehavior);
            }
        }
    }

    /**
     * Присоединяет поведение к объекту
     *
     * @param string $sName
     * @param $mBehavior
     *
     * @return Behavior
     */
    public function AttachBehavior($sName, $mBehavior)
    {
        $this->PrepareBehaviors();
        if (!($mBehavior instanceof Behavior)) {
            if (is_string($mBehavior)) {
                $sClass = $mBehavior;
                $aParams = array();
            } else {
                if (isset($mBehavior['class'])) {
                    $sClass = $mBehavior['class'];
                    unset($mBehavior['class']);
                } else {
                    $sClass = array_shift($mBehavior);
                }
                $aParams = $mBehavior;
            }
            $mBehavior = Engine::GetBehavior($sClass, $aParams);
        }
        if (isset($this->_aBehaviors[$sName])) {
            $this->_aBehaviors[$sName]->Detach();
        }
        $mBehavior->Attach($this);
        return $this->_aBehaviors[$sName] = $mBehavior;
    }

    /**
     * Отсоединяет поведение от объекта
     *
     * @param string $sName
     *
     * @return Behavior|null
     */
    public function DetachBehavior($sName)
    {
        $this->PrepareBehaviors();
        if (isset($this->_aBehaviors[$sName])) {
            $oBehavior = $this->_aBehaviors[$sName];
            unset($this->_aBehaviors[$sName]);
            $oBehavior->Detach();
            return $oBehavior;
        } else {
            return null;
        }
    }

    /**
     * Запускает хук поведения на выполнение
     *
     * @param string $sName
     * @param array $aVars
     * @param bool $bWithGlobal Запускать дополнительно одноименный глобальный (стандартный) хук
     *
     * @return mixed
     */
    public function RunBehaviorHook($sName, $aVars = array(), $bWithGlobal = false)
    {
        return $this->Hook_RunHookBehavior($sName, $this, $aVars, $bWithGlobal);
    }

    /**
     * Добавляет хук поведения
     *
     * @param string $sName
     * @param array $aCallback
     * @param int $iPriority
     *
     * @return mixed
     */
    public function AddBehaviorHook($sName, $aCallback, $iPriority = 1)
    {
        return $this->Hook_AddHookBehavior($sName, $this, $aCallback, $iPriority);
    }

    /**
     * Удаляет хук поведения
     *
     * @param string $sName
     * @param array|null $aCallback Если null, то будут удалены все хуки
     *
     * @return mixed
     */
    public function RemoveBehaviorHook($sName, $aCallback = null)
    {
        return $this->Hook_RemoveHookBehavior($sName, $this, $aCallback);
    }

    /**
     * Обработка доступа к объекты поведения
     *
     * @param string $sName
     *
     * @return mixed
     */
    public function __get($sName)
    {
        $this->PrepareBehaviors();
        /**
         * Проверяем на получение объекта поведения
         */
        if (isset($this->_aBehaviors[$sName])) {
            return $this->_aBehaviors[$sName];
        }
    }

    /**
     * Ставим хук на вызов неизвестного метода и считаем что хотели вызвать метод какого либо модуля
     * @see Engine::_CallModule
     *
     * @param string $sName Имя метода
     * @param array $aArgs Аргументы
     * @return mixed
     */
    public function __call($sName, $aArgs)
    {
        $this->PrepareBehaviors();
        /**
         * Проверяем на вызов метода поведения
         * Пропускаем служебные методы поведения
         */
        if (!in_array(strtolower($sName), array('attach', 'detach'))) {
            foreach ($this->_aBehaviors as $oObject) {
                if (func_method_exists($oObject, $sName, 'public')) {
                    return call_user_func_array(array($oObject, $sName), $aArgs);
                }
            }
        }
        /**
         * Если метод не найден, то запускаем стандартную обработку
         */
        return Engine::getInstance()->_CallModule($sName, $aArgs);
    }
}