<?php

abstract class ModuleMigration_EntityAbstract extends Entity
{
    /**
     * Выполняется при обновлении версии
     */
    public function up()
    {

    }

    /**
     * Выполняется при откате версии
     */
    public function down()
    {

    }

    /**
     * Транслирует на базу данных запросы из указанного файла
     * @see ModuleDatabase::ExportSQL
     *
     * @param  string $sFilePath Полный путь до файла с SQL
     * @return array
     */
    protected function exportSQL($sFilePath)
    {
        return $this->Database_ExportSQL($sFilePath);
    }

    /**
     * Выполняет SQL
     * @see ModuleDatabase::ExportSQLQuery
     *
     * @param string $sSql Строка SQL запроса
     * @return array
     */
    protected function exportSQLQuery($sSql)
    {
        return $this->Database_ExportSQLQuery($sSql);
    }

    /**
     * Проверяет наличие таблицы в БД
     * @see ModuleDatabase::isTableExists
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * <pre>
     * prefix_topic
     * </pre>
     * @return bool
     */
    protected function isTableExists($sTableName)
    {
        return $this->Database_isTableExists($sTableName);
    }

    /**
     * Проверяет наличие поля в таблице
     * @see ModuleDatabase::isFieldExists
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * @param string $sFieldName Название поля в таблице
     * @return bool
     */
    protected function isFieldExists($sTableName, $sFieldName)
    {
        return $this->Database_isFieldExists($sTableName, $sFieldName);
    }

    /**
     * Добавляет новый тип в поле enum(перечисление)
     * @see ModuleDatabase::addEnumType
     *
     * @param string $sTableName Название таблицы, необходимо перед именем таблицы добавлять "prefix_", это позволит учитывать произвольный префикс таблиц у пользователя
     * @param string $sFieldName Название поля в таблице
     * @param string $sType Название типа
     */
    protected function addEnumType($sTableName, $sFieldName, $sType)
    {
        $this->Database_addEnumType($sTableName, $sFieldName, $sType);
    }
}