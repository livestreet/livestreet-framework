<?php

class PluginExample_ModuleMain extends Module
{

    protected $oMapper = null;

    /**
     * Инициализация модуля. Это обязательный метод
     */
    public function Init()
    {
        /**
         * Создаем объект маппера PluginExample_ModuleMain_MapperMain
         */
        $this->oMapper = Engine::GetMapper(__CLASS__);
    }
}