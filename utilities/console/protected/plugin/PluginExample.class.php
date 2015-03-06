<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

/**
 * Основной класс плагина
 */
class PluginExample extends Plugin
{
    /**
     * Переопределение стандартных классов и шаблонов
     * Сначала указывается класс (или шаблон), который нужно переопределить, а значением указывается переопределяющий класс плагина (рекомендуется называть его тем же именем).
     * Символ "_" перед классом означается автоматическую подстановку вместо него класса текущего плагина (или пути до шаблона плагина).
     *
     * @var array
     */
    protected $aInherits = array(/*
        'module' => array(
            'ModuleTopic'=>'_ModuleTopic',
            'ModuleUser'=>'_ModuleUser'
        ),
        'mapper' => array(
            'ModuleTopic_MapperTopic'=>'_ModuleTopic_MapperTopic',
        ),
        'entity' => array(
            'ModuleTopic_EntityTopic' => '_ModuleTopic_EntityTopic'
        ),
        'action' => array(
            'ActionIndex'=>'_ActionSomepage'
        ),
        'block' => array(
            'BlockBlogs'=>'_BlockBlogs'
        ),
        'event' => array(
            'ActionFoo_EventBar'=>'_ActionFoo_EventBar'
        ),
        'behavior' => array(
            'ModuleCategory_BehaviorModule'=>'_ModuleCategory_BehaviorModule'
        ),
        'template' => array(
            // Переопределяет шаблон экшена
            'actions/ActionIndex/index.tpl'=>'_actions/ActionTest/index.tpl',
            // Переопределяет шаблон 'login' компонента 'auth'
            'component.auth.login' => '_components/auth/login.tpl',
        ),
        */
    );

    /**
     * Выполняется в момент активации плагина
     *
     * @return bool
     */
    public function Activate()
    {
        /**
         * Создаем новый тип для дополнительных полей. В итоге к сущности Some можно будет через интерфейс добавлять новые поля.
         * Третий параметр true ознает перезапись параметров, если такой тип уже есть в БД
         */
        /*
        if (!$this->Property_CreateTargetType('example_some', array('entity' => 'PluginExample_ModuleMain_EntitySome', 'name' => 'Нечто'), true)) {
            return false;
        }
        */

        /**
         * Создаем новый тип для категорий. Позволяет прикрутить к сущности Some неограниченное дерево категорий управляемое из админки.
         */
        /*
        if (!$this->Category_CreateTargetType('example_some', 'Нечто', array(), true)) {
            return false;
        }
        */

        return true;
    }

    /**
     * Выполняется в момент деактивации плагина
     *
     * @return bool
     */
    public function Deactivate()
    {
        /**
         * Отключаем дополнительные поля
         */
        //$this->Property_RemoveTargetType('example_some', ModuleProperty::TARGET_STATE_NOT_ACTIVE);
        /**
         * Отключаем категории
         */
        //$this->Category_RemoveTargetType('example_some', ModuleCategory::TARGET_STATE_NOT_ACTIVE);

        return true;
    }

    /**
     * Выполняется при удалении плагина
     *
     * @return bool
     */
    public function Remove()
    {
        /**
         * Удаляем тип дополнительных полей
         */
        //$this->Property_RemoveTargetType('example_some', ModuleProperty::TARGET_STATE_REMOVE);
        /**
         * Удаляем тип категорий
         */
        //$this->Category_RemoveTargetType('example_some', ModuleCategory::TARGET_STATE_REMOVE);

        return true;
    }

    /**
     * Выполняется каждый раз при загрузке сайта, если плагин активирован
     */
    public function Init()
    {
        $this->Viewer_AppendStyle(Plugin::GetTemplateWebPath(__CLASS__) . "assets/css/main.css"); // Добавление своего CSS
        $this->Viewer_AppendScript(Plugin::GetTemplateWebPath(__CLASS__) . "assets/js/main.js"); // Добавление своего JS

        //$this->Viewer_AddMenu('blog',Plugin::GetTemplatePath(__CLASS__).'menu.blog.tpl'); // например, задаем свой вид меню
    }
}