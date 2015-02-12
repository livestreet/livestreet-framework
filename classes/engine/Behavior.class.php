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
 * Абстракция поведения, от которой наследуются все поведения
 * Поведения предназначены для удобного изменения функционала другого объекта (модуля, сущности и т.п.)
 * В основном поведения добавляют новые свойства и методы (функционируют через магические вызовы)
 * Концептуальное отличие от наследования через плагины в том, что целевой объект сам "выбирает" какой функционал получить, а не наоборот
 *
 * @package framework.engine
 * @since 2.0
 */
abstract class Behavior extends LsObject
{

    /**
     * Исходный объект, к которому добавлено поведение
     *
     * @var LsObject|null
     */
    protected $oObject;
    /**
     * Параметры, которые указали при добавлении поведения
     * Здесь можно определить дефолтные параметры, которые затем смержатся
     *
     * @var array
     */
    protected $aParams = array();
    /**
     * Список хуков, которые отслеживает поведение
     * <pre>
     * array(
     *    'hook_name_1' => 'method',
     *    'hook_name_2' => array($oObject,'method'), // callback
     *    'hook_name_3' => array('method',100), // with priority
     *    'hook_name_4' => array(array($oObject,'method'),100), // with callback and priority
     * )
     * </pre>
     *
     * @var array
     */
    protected $aHooks = array();

    /**
     * Конструктор, инициализирует параметры
     *
     * @param array $aParams
     */
    public function __construct($aParams = array())
    {
        parent::__construct();
        if ($aParams) {
            $this->aParams = array_merge($this->aParams, $aParams);
        }
    }

    /**
     * Инициализация поведения, выполняется автоматически после добавления (Attach) поведения
     * Данный метод можно переопределить внутри конкретного поведения
     */
    protected function Init()
    {

    }

    /**
     * Добавляет поведение к объекту
     *
     * @param LsObject $oObject
     */
    public function Attach($oObject)
    {
        $this->oObject = $oObject;
        foreach ($this->aHooks as $sName => $mParams) {
            list($aCallback, $iPriority) = $this->ParseHookParams($mParams);
            $this->oObject->AddBehaviorHook($sName, $aCallback, $iPriority);
        }
        $this->Init();
    }

    /**
     * Удаляет поведение у текущего объекта
     */
    public function Detach()
    {
        if ($this->oObject) {
            foreach ($this->aHooks as $sName => $mParams) {
                list($aCallback,) = $this->ParseHookParams($mParams);
                $this->oObject->RemoveBehaviorHook($sName, $aCallback);
            }
            $this->oObject = null;
        }
    }

    /**
     * Вспомогательный метод для определения коллбека из параметров
     *
     * @param array|string $mParams
     *
     * @return array
     */
    protected function ParseHookParams($mParams)
    {
        $iPriority = 1;
        if (is_string($mParams)) {
            $aCallback = array($this, $mParams);
        } elseif (is_object($mParams[0])) {
            $aCallback = $mParams;
        } elseif (is_string($mParams[0])) {
            $aCallback = array($this, $mParams[0]);
            if (isset($mParams[1])) {
                $iPriority = $mParams[1];
            }
        } else {
            $aCallback = $mParams[0];
            if (isset($mParams[1])) {
                $iPriority = $mParams[1];
            }
        }
        return array($aCallback, $iPriority);
    }

    /**
     * Возвращает параметр по его имени
     *
     * @param string $sName
     *
     * @return mixed
     */
    public function getParam($sName)
    {
        return isset($this->aParams[$sName]) ? $this->aParams[$sName] : null;
    }

    /**
     * Устанавливает значение параметра
     *
     * @param string $sName
     * @param mixed $mValue
     */
    public function setParam($sName, $mValue)
    {
        $this->aParams[$sName] = $mValue;
    }

    /**
     * Возвращает все параметры
     *
     * @return array
     */
    public function getParams()
    {
        return $this->aParams;
    }
}