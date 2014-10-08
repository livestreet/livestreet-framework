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
 * Сущность версии плагина
 *
 * @package framework.modules
 * @since 2.0
 */
class ModulePluginManager_EntityVersion extends EntityORM
{

    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            $this->setDateUpdate(date("Y-m-d H:i:s"));
        }
        return $bResult;
    }

}