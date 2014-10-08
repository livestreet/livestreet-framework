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
 * Абстракция хука, от которой наследуются все хуки
 * Дает возможность создавать обработчики хуков в каталоге /hooks/
 *
 * @package framework.engine
 * @since 1.0
 */
abstract class Hook extends LsObject
{
    /**
     * Добавляет обработчик на хук
     * @see ModuleHook::AddExecHook
     *
     * @param string $sName Название хука на который вешается обработчик
     * @param string $sCallBack Название метода обработчика
     * @param null|string $sClassNameHook Название класса обработчика, по умолчанию это текущий класс хука
     * @param int $iPriority Приоритет обработчика хука, чем выше число, тем больше приоритет - хук обработчик выполнится раньше остальных
     */
    protected function AddHook($sName, $sCallBack, $sClassNameHook = null, $iPriority = 1)
    {
        if (is_null($sClassNameHook)) {
            $sClassNameHook = get_class($this);
        }
        $this->Hook_AddExecHook($sName, $sCallBack, $iPriority, array('sClassName' => $sClassNameHook));
    }

    /**
     * Добавляет делегирующий обработчик на хук. Актуален для хуков на выполнение методов модулей.
     * После него другие обработчики не выполняются, а результат метода моуля заменяется на рузультат обработчика.
     *
     * @param $sName    Название хука на который вешается обработчик
     * @param $sCallBack    Название метода обработчика
     * @param null $sClassNameHook Название класса обработчика, по умолчанию это текущий класс хука
     * @param int $iPriority Приоритет обработчика хука
     */
    protected function AddDelegateHook($sName, $sCallBack, $sClassNameHook = null, $iPriority = 1)
    {
        if (is_null($sClassNameHook)) {
            $sClassNameHook = get_class($this);
        }
        $this->Hook_AddDelegateHook($sName, $sCallBack, $iPriority, array('sClassName' => $sClassNameHook));
    }

    /**
     * Обязательный метод в хуке - в нем происходит регистрация обработчиков хуков
     *
     * @abstract
     */
    abstract public function RegisterHook();
}