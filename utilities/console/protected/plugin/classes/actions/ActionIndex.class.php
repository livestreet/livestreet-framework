<?php

/**
 * Класс экшена
 */
class PluginExample_ActionIndex extends ActionPlugin
{

    /**
     * Инициализация экшена
     */
    public function Init()
    {
        $this->SetDefaultEvent('index');
    }

    /**
     * Регистрируем евенты
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('index', 'EventIndex');

    }


    /**
     * Обработка евента index
     */
    protected function EventIndex()
    {
        /**
         * Устанавливает шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Завершение работы экшена
     */
    public function EventShutdown()
    {
        /**
         * Здесь можно прогрузить в шаблон какие-то общие переменные для всех евентов
         */
    }
}