{**
 * Загрузка и управление файлами
 *}

{$component = 'ls-uploader'}

{block 'uploader_options'}
    {$mods = $smarty.local.mods}
    {$classes = $smarty.local.classes}
    {$attributes = $smarty.local.attributes}
    {$show = $smarty.local.show|default:true}
{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes}" {cattr list=$attributes}>
    {* @hook Начало основного блока загрузчика *}
    {hook run='uploader_begin'}

    {block 'uploader_content'}
        {* Drag & drop зона *}
        {component 'field' template='upload-area'
            classes      = 'js-uploader-area'
            inputClasses = 'js-uploader-file'
            inputName    = 'filedata'}

        {* @hook Хук после зоны загрузки *}
        {hook run='uploader_area_after'}

        {* Враппер *}
        <div class="{$component}-wrapper clearfix">
            {* Сайдбар *}
            <div class="{$component}-aside js-uploader-aside is-empty">
                {* Блок отображаемый когда нет активного файла *}
                {component 'blankslate'
                    text    = {lang name='uploader.info.empty'}
                    classes = "{$component}-aside-empty js-uploader-aside-empty"}

                {* Блоки *}
                <div class="{$component}-aside-blocks js-uploader-blocks">
                    {block 'uploader_aside'}
                        {include './uploader-block.info.tpl'}
                    {/block}
                </div>
            </div>

            {* Основное содержимое *}
            <div class="{$component}-content js-uploader-content clearfix">
                {* @hook Начало контента *}
                {hook run='uploader_content_begin'}

                {* Фильтр *}
                {component 'actionbar' classes="{$component}-filter js-uploader-filter" items=[
                    [
                        'buttons' => [
                            [
                                text => {lang 'uploader.filter.uploaded'},
                                classes => 'active js-uploader-filter-item',
                                attributes => [ 'data-filter' => 'uploaded' ]
                            ],
                            [
                                text => {lang 'uploader.filter.all'},
                                classes => 'js-uploader-filter-item',
                                attributes => [ 'data-filter' => 'all' ]
                            ]
                        ]
                    ]
                ]}

                {* Сообщение о пустом списке *}
                {component 'blankslate'
                    visible=false
                    text='Нет загруженных файлов'
                    mods='no-background'
                    classes="{$component}-list-blankslate js-uploader-list-blankslate"}

                {* Список файлов *}
                <ul class="{$component}-file-list js-uploader-list"></ul>

                {component 'more'
                	attributes = [ style => 'display: none' ]
                    classes    = 'js-uploader-list-more'
                    ajaxParams = [ 'page' => 2 ]}

                {* @hook Конец контента *}
                {hook run='uploader_content_end'}
            </div>
        </div>
    {/block}

    {* @hook Конец основного блока загрузчика *}
    {hook run='uploader_end'}
</div>