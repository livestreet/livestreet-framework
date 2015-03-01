<?php

/**
 * Класс модуля
 * Модуль с поддержкой ORM (если ORM не нужен, то модуль нужно наследовать от Module)
 */
class PluginExample_ModuleMain extends ModuleORM
{
    /**
     * Инициализация модуля.
     */
    public function Init()
    {
        parent::Init();
        /**
         * Здесь можно выполнить дополнительную логику при инициализации модуля
         */
    }
}