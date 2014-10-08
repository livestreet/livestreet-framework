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
 * Бекенд служебного файлового кеша для ORM
 * Используется для хранения схемы БД
 *
 * @package framework.modules
 * @since 2.0
 */
class ModuleCache_EntityBackendFileOrm extends ModuleCache_EntityBackendFile {
	/**
	 * Возвращает каталог для кеша
	 *
	 * @return string
	 */
	protected function GetCacheDir() {
		return Config::Get('sys.cache.dir').'/database/';
	}
}