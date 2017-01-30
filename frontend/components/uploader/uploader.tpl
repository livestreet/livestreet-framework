{**
 * Загрузка и управление файлами
 *}

{$component = 'ls-uploader'}

{block 'uploader_options'}
    {component_define_params params=[ 'show', 'useFilter', 'mods', 'classes', 'attributes' ]}

    {$show = $show|default:true}
    {$useFilter = $useFilter|default:true}
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
        <div class="{$component}-wrapper ls-clearfix">
            {* Сайдбар *}
            <div class="{$component}-aside js-uploader-aside is-empty">
                {* Блок отображаемый когда нет активного файла *}
                {component 'blankslate'
                    text    = {lang name='uploader.info.empty'}
                    classes = "{$component}-aside-empty js-uploader-aside-empty"}

                {* Блоки *}
                <div class="{$component}-aside-blocks js-uploader-blocks">
                    {block 'uploader_aside'}
                        {component 'uploader' template='block.info'}
                    {/block}
                </div>
            </div>

            {* Основное содержимое *}
            <div class="{$component}-content js-uploader-content ls-clearfix">
                {* @hook Начало контента *}
                {hook run='uploader_content_begin'}

                {* Фильтр *}
                {if $useFilter}
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
                {/if}

                {* Сообщение о пустом списке *}
                {component 'blankslate'
                    visible=false
                    text={lang 'uploader.attach.empty'}
                    mods='no-background'
                    classes="{$component}-list-blankslate js-uploader-list-blankslate"}

                {* Список файлов *}
                <ul class="{$component}-file-list js-uploader-list"></ul>

                {component 'pagination' template='ajax'
                    mods='small'
                    attributes = [ style => 'display: none' ]
                    classes    = 'js-uploader-list-pagination'}

                {* @hook Конец контента *}
                {hook run='uploader_content_end'}
            </div>
        </div>
    {/block}

    {* @hook Конец основного блока загрузчика *}
    {hook run='uploader_end'}
</div>