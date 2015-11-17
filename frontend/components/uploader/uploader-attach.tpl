{**
 * Прикрепление файлов
 *
 * @param number $count Кол-во загруженных файлов
 * @param array  $i18n  Лок-ия
 *}

{* Название компонента *}
{$component = 'ls-uploader-attach'}

{* Генерируем копии локальных переменных, *}
{* чтобы их можно было изменять в дочерних шаблонах *}
{foreach [ 'count', 'i18n', 'mods', 'classes', 'attributes' ] as $param}
    {assign var="$param" value=$smarty.local.$param}
{/foreach}

{* Лок-ия *}
{$i18n = array_merge([
    title => {lang 'uploader.attach.title'},
    upload => {lang 'uploader.attach.upload'},
    count => {lang 'uploader.attach.count'},
    empty => {lang 'uploader.attach.empty'}
], $i18n|default:[])}

{block 'uploader_attach_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}>
    {* Хидер *}
    <div class="{$component}-header fieldset-header">
        <h2 class="{$component}-title fieldset-title">
            {$i18n.title}
        </h2>
    </div>

    {* Контент *}
    <div class="{$component}-body fieldset-body">
        {component 'button' type='button' text=$i18n.upload classes='js-uploader-attach-button'}

        <span class="{$component}-file-counter js-uploader-attach-file-counter">
            {if $count}
                {lang "uploader.attach.count" count=$count plural=true}
            {else}
                {$i18n.empty}
            {/if}
        </span>
    </div>

    {* Модальное окно с загрузчиком *}
    {component 'uploader' template='modal'
        choosable = false
        uploader  = [ useFilter => false ]
        title     = $i18n.title
        classes   = 'js-uploader-attach-modal'}
</div>