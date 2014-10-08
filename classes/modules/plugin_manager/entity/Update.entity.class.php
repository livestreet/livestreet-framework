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
 * От этого класса необходимо наследовать классы апдейтов/миграций плагина
 *
 * @package framework.modules
 * @since 2.0
 */
abstract class ModulePluginManager_EntityUpdate extends EntityORM {
	/**
	 * Выполняется при обновлении версии
	 */
	public function up() {

	}
	/**
	 * Выполняется при откате версии
	 */
	public function down() {

	}
	/**
	 * Транслирует на базу данных запросы из указанного файла
	 * @see ModuleDatabase::ExportSQL
	 *
	 * @param  string $sFilePath	Полный путь до файла с SQL
	 * @return array
	 */
	protected function exportSQL($sFilePath) {
		return $this->Database_ExportSQL($sFilePath);
	}
	/**
	 * Выполняет SQL
	 * @see ModuleDatabase::ExportSQLQuery
	 *
	 * @param string $sSql	Строка SQL запроса
	 * @return array
	 */
	protected function exportSQLQuery($sSql) {
		return $this->Database_ExportSQLQuery($sSql);
	}
	/**
	 * Проверяет наличие таблицы в БД
	 * @see ModuleDatabase::isTableExists
	 *
	 * @param string $sTableName	Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
	 * <pre>
	 * prefix_topic
	 * </pre>
	 * @return bool
	 */
	protected function isTableExists($sTableName) {
		return $this->Database_isTableExists($sTableName);
	}
	/**
	 * Проверяет наличие поля в таблице
	 * @see ModuleDatabase::isFieldExists
	 *
	 * @param string $sTableName	Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
	 * @param string $sFieldName	Название поля в таблице
	 * @return bool
	 */
	protected function isFieldExists($sTableName,$sFieldName) {
		return $this->Database_isFieldExists($sTableName,$sFieldName);
	}
	/**
	 * Добавляет новый тип в поле enum(перечисление)
	 * @see ModuleDatabase::addEnumType
	 *
	 * @param string $sTableName	Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
	 * @param string $sFieldName	Название поля в таблице
	 * @param string $sType			Название типа
	 */
	protected function addEnumType($sTableName,$sFieldName,$sType) {
		$this->Database_addEnumType($sTableName,$sFieldName,$sType);
	}
}