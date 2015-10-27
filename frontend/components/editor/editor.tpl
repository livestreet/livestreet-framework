{**
 * Редактор
 *}

{* Название компонента *}
{$component = 'editor'}

{* Получаем тип редактора *}
{$type = ( ( $smarty.local.type ) ? $smarty.local.type : ( Config::Get('view.wysiwyg') ) ? 'visual' : 'markup' )}
{$set = $smarty.local.set|default:'default'}

{* Уникальный ID *}
{$_uid = $smarty.local.id|default:($component|cat:rand(0, 10e10))}

{* Уникальный ID окна загрузки файлов *}
{$_mediaUid = "media{$_uid}"}


{**
 * Textarea
 *}
{function editor_textarea}
    {component 'field' template='textarea'
        name            = $smarty.local.name
        value           = $smarty.local.value
        label           = $smarty.local.label
        mods            = $smarty.local.mods
        classes         = $smarty.local.classes
        id              = $_uid
        attributes      = $smarty.local.attributes
        rules           = $smarty.local.rules
        entityField     = $smarty.local.entityField
        entity          = $smarty.local.entity
        inputClasses    = "{$smarty.local.classes} {$smarty.local.inputClasses}"
        inputAttributes = array_merge( $smarty.local.attributes|default:[], [ 'data-editor-type' => $type, 'data-editor-set' => $set, 'data-editor-media' => $_mediaUid ] )
        note            = $smarty.local.note
        rows            = $smarty.local.rows|default:10}
{/function}

{* Визуальный редактор *}
{if $type == 'visual'}
    {hookb run='editor_visual'}
        {asset type='js' file="Component@editor.vendor/tinymce/js/tinymce/tinymce.min"}
        {asset type='js' file="Component@editor.vendor/tinymce/js/tinymce/jquery.tinymce.min"}
        {asset type='js' file="Component@editor.visual"}

        {editor_textarea}
    {/hookb}

{* Markup редактор *}
{else}
    {hookb run='editor_markup'}

        <script type="text/javascript">
            ls.lang.load({lang_load name="editor.markup.toolbar.b, editor.markup.toolbar.i, editor.markup.toolbar.u, editor.markup.toolbar.s, editor.markup.toolbar.url, editor.markup.toolbar.url_promt, editor.markup.toolbar.image_promt, editor.markup.toolbar.code, editor.markup.toolbar.video, editor.markup.toolbar.video_promt, editor.markup.toolbar.image, editor.markup.toolbar.cut, editor.markup.toolbar.quote, editor.markup.toolbar.list, editor.markup.toolbar.list_ul, editor.markup.toolbar.list_ol, editor.markup.toolbar.list_li, editor.markup.toolbar.title, editor.markup.toolbar.title_h4, editor.markup.toolbar.title_h5, editor.markup.toolbar.title_h6, editor.markup.toolbar.clear_tags, editor.markup.toolbar.user, editor.markup.toolbar.user_promt"});
        </script>

        {asset type='js' file="Component@editor.vendor/markitup/jquery.markitup"}
        {asset type='js' file="Component@editor.markup"}

        {asset type='css' file="Component@editor.vendor/markitup/skins/livestreet/style"}
        {asset type='css' file="Component@editor.vendor/markitup/sets/livestreet/style"}
        {asset type='css' file="Component@editor.editor"}

        {editor_textarea}

        {if $smarty.local.help|default:true}
            {component 'editor' template='markup-help' targetId=$_uid}
        {/if}
    {/hookb}
{/if}

{* Управление медиа-файлами *}
{component 'media'
    sMediaTargetType = $smarty.local.mediaTargetType
    sMediaTargetId   = $smarty.local.mediaTargetId
    id               = $_mediaUid
    assign           = 'mediaModal'}

{* Добавляем модальное окно (компонент media) в конец лэйаута чтобы избежать вложенных форм *}
{$sLayoutAfter = "$sLayoutAfter $mediaModal" scope='root'}