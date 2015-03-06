<?php

/*
 * Описание настроек плагина для интерфейса редактирования в админке
 */
$config['$config_scheme$'] = array(
    'per_page'           => array(
        /*
         * тип: integer, string, array, boolean, float
         */
        'type'        => 'integer',
        /*
         * отображаемое имя параметра, ключ языкового файла
         */
        'name'        => 'config.per_page.name',
        /*
         * отображаемое описание параметра, ключ языкового файла
         */
        'description' => 'config.per_page.description',
        /*
         * валидатор (не обязательно)
         */
        'validator'   => array(
            /*
             * тип валидатора, существующие типы валидаторов движка:
             * Boolean, Compare, Date, Email, Number, Regexp, Required, String, Tags, Type, Url, Array (специальный валидатор, см. документацию)
             */
            'type'   => 'Number',
            /*
             * параметры, которые будут переданы в валидатор
             */
            'params' => array(
                'min'         => 1,
                'max'         => 50,
                /*
                 * разрешить только целое число
                 */
                'integerOnly' => true,
                /*
                 * не допускать пустое значение
                 */
                'allowEmpty'  => false,
            ),
        ),
    ),
);

/**
 * Описание разделов для настроек
 * Каждый раздел группирует определенные параметры конфига
 */
$config['$config_sections$'] = array(
    /**
     * Настройки раздела
     */
    array(
        /**
         * Название раздела
         */
        'name'         => 'config_sections.one',
        /**
         * Список параметров для отображения в разделе
         */
        'allowed_keys' => array(
            'per_page',
        ),
    ),
);

return $config;