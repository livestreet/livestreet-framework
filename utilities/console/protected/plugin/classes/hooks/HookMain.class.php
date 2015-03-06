<?php

/**
 * Класс обработчиков хуков
 */
class PluginExample_HookMain extends Hook
{
    /*
     * Регистрация событий на хуки
     */
    public function RegisterHook()
    {
        /**
         * Хук 'topic_edit_after' вызывается после редактирование топика.
         * В качестве обработчика назначается метод HookTopicEditAfter
         */
        $this->AddHook('topic_edit_after', 'HookTopicEditAfter');

        /**
         * Хук на инициализацию текущего экшена (выполняется перед выполнением текущего экшена)
         * Четвертый параметр 1000 - это приоритет выполнение среди остальных навешанных хуков (чем больше, тем раньше будет выполнен обработчик)
         */
        //$this->AddHook('init_action','HookInitAction', __CLASS__, 1000);
    }

    /**
     * Обработчик хука
     */
    public function HookTopicEditAfter($aParams)
    {
        /**
         * Получаем топик из параметров
         */
        $oTopic = $aParams['oTopic'];

    }
}