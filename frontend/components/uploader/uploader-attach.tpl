{**
 * Прикрепление файлов
 *
 * @param number $count Кол-во загруженных файлов
 * @param array  $i18n  Лок-ия
 *}

{* Название компонента *}
{$component = 'ls-uploader-attach'}

{component_define_params params=[ 'count', 'i18n', 'mods', 'classes', 'attributes' ]}

{* Лок-ия *}
{$i18n = array_merge([
    title  => 'uploader.attach.title',
    upload => 'uploader.attach.upload',
    count  => 'uploader.attach.count',
    empty  => 'uploader.attach.empty'
], $i18n|default:[])}

{block 'uploader_attach_options'}{/block}

<div class="{$component} {cmods name=$component mods=$mods} {$classes} fieldset" {cattr list=$attributes}>
    {* Хидер *}
    <div class="{$component}-header fieldset-header">
        <h2 class="{$component}-title fieldset-title">
            {lang $i18n.title}
        </h2>
    </div>

    {* Контент *}
    <div class="{$component}-body fieldset-body">
        {component 'button' type='button' text={lang $i18n.upload} classes='js-uploader-attach-button'}

        <span class="{$component}-file-counter js-uploader-attach-file-counter">
            {lang name=$i18n.count empty=$i18n.empty count=$count plural=true}
        </span>
    </div>

    {* Модальное окно с загрузчиком *}
    {component 'uploader' template='modal'
        choosable = false
        uploader  = [ useFilter => false ]
        title     = {lang $i18n.title}
        classes   = 'js-uploader-attach-modal'}
</div>