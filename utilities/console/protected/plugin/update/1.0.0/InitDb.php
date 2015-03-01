<?php

/**
 * Класс миграции БД
 */
class PluginExample_Update_InitDb extends ModulePluginManager_EntityUpdate
{
    /**
     * Выполняется при обновлении версии
     */
    public function up()
    {
        if (!$this->isTableExists('prefix_tablename')) {
            /**
             * При активации выполняем SQL дамп
             */
            $this->exportSQL(Plugin::GetPath(__CLASS__) . '/data/install.sql');
        }
    }

    /**
     * Выполняется при откате версии
     */
    public function down()
    {
        $this->exportSQL(Plugin::GetPath(__CLASS__) . '/data/uninstall.sql');
    }
}