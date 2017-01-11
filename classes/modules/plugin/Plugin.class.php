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
 * Модуль управления плагинами
 *
 * @package framework.modules
 * @since 1.0
 */
class ModulePlugin extends Module
{
    /**
     * Файл описания плагина
     *
     * @var string
     */
    const PLUGIN_XML_FILE = 'plugin.xml';
    /**
     * Список плагинов
     *
     * @var array
     */
    protected $aPluginsList = array();
    /**
     * Список engine-rewrite`ов (модули, мапперы, экшены, сущности, шаблоны, блоки, евенты, поведения)
     * Определяет типы объектов, которые может переопределить/унаследовать плагин
     *
     * @var array
     */
    protected $aDelegates = array(
        'module'   => array(),
        'mapper'   => array(),
        'action'   => array(),
        'entity'   => array(),
        'template' => array(),
        'block'    => array(),
        'event'    => array(),
        'behavior' => array(),
    );
    /**
     * Стек наследований
     *
     * @var array
     */
    protected $aInherits = array();

    /**
     * Инициализация модуля
     *
     */
    public function Init()
    {

    }

    /**
     * Перенаправление вызовов на модули, экшены, сущности
     *
     * @param  string $sType
     * @param  string $sFrom
     * @param  string $sTo
     * @param  string $sSign
     */
    public function Delegate($sType, $sFrom, $sTo, $sSign = __CLASS__)
    {
        /**
         * Запрещаем неподписанные делегаты
         */
        if (!is_string($sSign) or !strlen($sSign)) {
            return null;
        }
        if (!in_array($sType, array_keys($this->aDelegates)) or !$sFrom or !$sTo) {
            return null;
        }

        $this->aDelegates[$sType][trim($sFrom)] = array(
            'delegate' => trim($sTo),
            'sign'     => $sSign
        );
    }

    /**
     * Добавляет в стек наследника класса
     *
     * @param string $sFrom
     * @param string $sTo
     * @param string $sSign
     */
    public function Inherit($sFrom, $sTo, $sSign = __CLASS__)
    {
        if (!is_string($sSign) or !strlen($sSign)) {
            return null;
        }
        if (!$sFrom or !$sTo) {
            return null;
        }

        $this->aInherits[trim($sFrom)]['items'][] = array(
            'inherit' => trim($sTo),
            'sign'    => $sSign
        );
        $this->aInherits[trim($sFrom)]['position'] = count($this->aInherits[trim($sFrom)]['items']) - 1;
    }

    /**
     * Сбрасывает текущее положение в цепочке наследования на начало
     *
     * @param string $sFrom
     *
     * @return bool
     */
    public function ResetInheritPosition($sFrom)
    {
        $sFrom = trim($sFrom);
        if (!isset($this->aInherits[$sFrom]['position'])) {
            return false;
        }
        $this->aInherits[$sFrom]['position'] = count($this->aInherits[$sFrom]['items']) - 1;
        return $this->aInherits[$sFrom]['position'];
    }

    /**
     * Получает следующего родителя у наследника.
     * ВНИМАНИЕ! Данный метод нужно вызвать только из __autoload()
     *
     * @param string $sFrom
     * @return string
     */
    public function GetParentInherit($sFrom)
    {
        if (!isset($this->aInherits[$sFrom]['items']) or count($this->aInherits[$sFrom]['items']) <= 1 or $this->aInherits[$sFrom]['position'] < 1) {
            return $sFrom;
        }
        $this->aInherits[$sFrom]['position']--;
        return $this->aInherits[$sFrom]['items'][$this->aInherits[$sFrom]['position']]['inherit'];
    }

    /**
     * Возвращает список наследуемых классов
     *
     * @param string $sFrom
     * @return null|array
     */
    public function GetInherits($sFrom)
    {
        if (isset($this->aInherits[trim($sFrom)])) {
            return $this->aInherits[trim($sFrom)]['items'];
        }
        return null;
    }

    /**
     * Возвращает последнего наследника в цепочке
     *
     * @param $sFrom
     * @return null|string
     */
    public function GetLastInherit($sFrom)
    {
        if (isset($this->aInherits[trim($sFrom)])) {
            return $this->aInherits[trim($sFrom)]['items'][count($this->aInherits[trim($sFrom)]['items']) - 1];
        }
        return null;
    }

    /**
     * Возвращает делегат модуля, экшена, сущности.
     * Если делегат не определен, пытается найти наследника, иначе отдает переданный в качестве sender`a параметр
     *
     * @param  string $sType
     * @param  string $sFrom
     * @return string
     */
    public function GetDelegate($sType, $sFrom)
    {
        if (isset($this->aDelegates[$sType][$sFrom]['delegate'])) {
            return $this->aDelegates[$sType][$sFrom]['delegate'];
        } elseif ($aInherit = $this->GetLastInherit($sFrom)) {
            return $aInherit['inherit'];
        }
        return $sFrom;
    }

    /**
     * @param string $sType
     * @param string $sFrom
     * @return array|null
     */
    public function GetDelegates($sType, $sFrom)
    {
        if (isset($this->aDelegates[$sType][$sFrom]['delegate'])) {
            return array($this->aDelegates[$sType][$sFrom]['delegate']);
        } else {
            if ($aInherits = $this->GetInherits($sFrom)) {
                $aReturn = array();
                foreach (array_reverse($aInherits) as $v) {
                    $aReturn[] = $v['inherit'];
                }
                return $aReturn;
            }
        }
        return null;
    }

    /**
     * Возвращает цепочку делегатов
     *
     * @param string $sType
     * @param string $sTo
     * @return array
     */
    public function GetDelegationChain($sType, $sTo)
    {
        $sRootDelegater = $this->GetRootDelegater($sType, $sTo);
        return $this->collectAllDelegatesRecursive($sType, array($sRootDelegater));
    }

    /**
     * Возвращает делегируемый класс
     *
     * @param string $sType
     * @param string $sTo
     * @return string
     */
    public function GetRootDelegater($sType, $sTo)
    {
        $sItem = $sTo;
        $sItemDelegater = $this->GetDelegater($sType, $sTo);
        while (empty($sRootDelegater)) {
            if ($sItem == $sItemDelegater) {
                $sRootDelegater = $sItem;
            }
            $sItem = $sItemDelegater;
            $sItemDelegater = $this->GetDelegater($sType, $sItemDelegater);
        }
        return $sRootDelegater;
    }

    /**
     * Составляет цепочку делегатов
     *
     * @param string $sType
     * @param string $aDelegates
     * @return array
     */
    public function collectAllDelegatesRecursive($sType, $aDelegates)
    {
        foreach ($aDelegates as $sClass) {
            if ($aNewDelegates = $this->GetDelegates($sType, $sClass)) {
                $aDelegates = array_merge($this->collectAllDelegatesRecursive($sType, $aNewDelegates), $aDelegates);
            }
        }
        return $aDelegates;
    }

    /**
     * Возвращает делегирующий объект по имени делегата
     *
     * @param  string $sType Объект
     * @param  string $sTo Делегат
     * @return string
     */
    public function GetDelegater($sType, $sTo)
    {
        static $aCache;

        $sCacheKey = $sType . '+' . $sTo;

        if (!isset($aCache[$sCacheKey])) {
            $aDelegateMapper = array();
            foreach ($this->aDelegates[$sType] as $kk => $vv) {
                if ($vv['delegate'] == $sTo) {
                    $aDelegateMapper[$kk] = $vv;
                }
            }
            if (is_array($aDelegateMapper) and count($aDelegateMapper)) {
                $aKeys = array_keys($aDelegateMapper);
                return $aCache[$sCacheKey] = array_shift($aKeys);
            }
            foreach ($this->aInherits as $k => $v) {
                $aInheritMapper = array();
                foreach ($v['items'] as $kk => $vv) {
                    if ($vv['inherit'] == $sTo) {
                        $aInheritMapper[$kk] = $vv;
                    }
                }
                if (is_array($aInheritMapper) and count($aInheritMapper)) {
                    return $aCache[$sCacheKey] = $k;
                }
            }
            $aCache[$sCacheKey] = $sTo;
        }
        return $aCache[$sCacheKey];
    }

    /**
     * Возвращает подпись делегата модуля, экшена, сущности.
     *
     * @param  string $sType
     * @param  string $sFrom
     * @return string|null
     */
    public function GetDelegateSign($sType, $sFrom)
    {
        if (isset($this->aDelegates[$sType][$sFrom]['sign'])) {
            return $this->aDelegates[$sType][$sFrom]['sign'];
        }
        if ($aInherit = $this->GetLastInherit($sFrom)) {
            return $aInherit['sign'];
        }
        return null;
    }

    /**
     * Возвращает true, если установлено правило делегирования
     * и класс является базовым в данном правиле
     *
     * @param  string $sType
     * @param  string $sFrom
     * @return bool
     */
    public function isDelegater($sType, $sFrom)
    {
        if (isset($this->aDelegates[$sType][$sFrom]['delegate'])) {
            return true;
        } elseif ($aInherit = $this->GetLastInherit($sFrom)) {
            return true;
        }
        return false;
    }

    /**
     * Возвращает true, если устано
     *
     * @param  string $sType
     * @param  string $sTo
     * @return bool
     */
    public function isDelegated($sType, $sTo)
    {
        /**
         * Фильтруем маппер делегатов/наследников
         * @var array
         */
        $aDelegateMapper = array();
        foreach ($this->aDelegates[$sType] as $kk => $vv) {
            if ($vv['delegate'] == $sTo) {
                $aDelegateMapper[$kk] = $vv;
            }
        }
        if (is_array($aDelegateMapper) and count($aDelegateMapper)) {
            return true;
        }
        foreach ($this->aInherits as $k => $v) {
            $aInheritMapper = array();
            foreach ($v['items'] as $kk => $vv) {
                if ($vv['inherit'] == $sTo) {
                    $aInheritMapper[$kk] = $vv;
                }
            }
            if (is_array($aInheritMapper) and count($aInheritMapper)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Возвращает список объектов, доступных для делегирования
     *
     * @return array
     */
    public function GetDelegateObjectList()
    {
        return array_keys($this->aDelegates);
    }

    /**
     * Возвращает полный список всех делегатов
     *
     * @return array
     */
    public function GetDelegatesAll()
    {
        return $this->aDelegates;
    }

    /**
     * Возвращает полый список всех наследований
     *
     * @return array
     */
    public function GetInheritsAll()
    {
        return $this->aInherits;
    }
}