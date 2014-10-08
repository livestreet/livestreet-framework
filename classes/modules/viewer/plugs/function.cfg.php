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
 * Плагин для смарти
 * Позволяет получать данные из конфига
 *
 * @param   array $aParams
 * @param   Smarty $oSmarty
 * @return  string
 */
function smarty_function_cfg($aParams, &$oSmarty)
{
    if (isset($aParams['_default_short'])) {
        $aParams['name'] = $aParams['_default_short'];
    }
    if (empty($aParams['name'])) {
        trigger_error("Config: missing 'name' parametr", E_USER_WARNING);
        return;
    }
    if (!isset($aParams['instance'])) {
        $aParams['instance'] = Config::DEFAULT_CONFIG_INSTANCE;
    }

    $mReturn = Config::Get($aParams['name'], $aParams['instance']);
    /**
     * Небольшой хак для замены http на https
     */
    $aHttpsKeys = array(
        'path.skin.web',
        'path.framework.frontend.web',
        'path.framework.libs_vendor.web',
        'path.root.web',
        'path.skin.assets.web',
        'path.framework.web'
    );
    if (in_array($aParams['name'], $aHttpsKeys) and is_string($mReturn) and Router::GetIsSecureConnection()) {
        $mReturn = preg_replace('#^http://#i', 'https://', $mReturn);
    }
    /**
     * Возвращаем значение из конфигурации
     */
    return $mReturn;
}