{**
 * Редактор
 *}

{* Название компонента *}
{$component = 'editor'}
{component_define_params params=[ 'form', 'placeholder', 'isDisabled', 'entity', 'entityScenario', 'entityField', 'escape', 'data', 'label', 'name',
    'rules', 'value', 'id', 'inputClasses', 'inputAttributes', 'inputData', 'mods', 'classes', 'attributes', 'note', 'rows', 'type', 'set', 'help', 
    'mediaTargetType', 'mediaTargetId' ]}

{* Получаем тип редактора *}
{$type = ( ( $type ) ? $type : ( Config::Get('view.wysiwyg') ) ? 'visual' : 'markup' )}
{$set = $set|default:'default'}

{* Уникальный ID *}
{$_uid = $id|default:($component|cat:mt_rand())}

{* Уникальный ID окна загрузки файлов *}
{$_mediaUid = "media{$_uid}"}


{**
 * Textarea
 *}
{function editor_textarea}
    {component 'field.textarea'
        id = $_uid
        inputAttributes = array_merge( $inputAttributes|default:[], [ 'data-editor-type' => $type, 'data-editor-set' => $set, 'data-editor-media' => $_mediaUid ] )
        rows = $rows|default:10
        params = $params}
{/function}

{* Визуальный редактор *}
{if $type == 'visual'}
    {hookb run='editor_visual' targetId=$_uid}
        {asset type='js' file="Component@editor.vendor/tinymce/js/tinymce/tinymce.min"}
        {asset type='js' file="Component@editor.vendor/tinymce/js/tinymce/jquery.tinymce.min"}
        {asset type='js' file="Component@editor.visual"}

        {editor_textarea}
    {/hookb}

{* Markup редактор *}
{else}
    {hookb run='editor_markup' targetId=$_uid}
        <script type="text/javascript">
            ls.lang.load({lang_load name="editor.markup.toolbar.b, editor.markup.toolbar.i, editor.markup.toolbar.u, editor.markup.toolbar.s, editor.markup.toolbar.url, editor.markup.toolbar.url_promt, editor.markup.toolbar.image_promt, editor.markup.toolbar.code, editor.markup.toolbar.video, editor.markup.toolbar.video_promt, editor.markup.toolbar.image, editor.markup.toolbar.cut, editor.markup.toolbar.quote, editor.markup.toolbar.list, editor.markup.toolbar.list_ul, editor.markup.toolbar.list_ol, editor.markup.toolbar.list_li, editor.markup.toolbar.title, editor.markup.toolbar.title_h4, editor.markup.toolbar.title_h5, editor.markup.toolbar.title_h6, editor.markup.toolbar.clear_tags, editor.markup.toolbar.user, editor.markup.toolbar.user_promt"});
        </script>

        {asset type='js' file="Component@editor.vendor/markitup/jquery.markitup"}
        {asset type='js' file="Component@editor.markup"}

        {asset type='css' file="Component@editor.vendor/markitup/skins/livestreet/style"}
        {asset type='css' file="Component@editor.vendor/markitup/sets/livestreet/style"}
        {asset type='css' file="Component@editor.editor"}

        {editor_textarea}

        {if $help|default:true}
            {component 'editor' template='markup-help' targetId=$_uid}
        {/if}
    {/hookb}
{/if}

{* Управление медиа-файлами *}
{component 'media'
    sMediaTargetType = $mediaTargetType
    sMediaTargetId   = $mediaTargetId
    id               = $_mediaUid
    assign           = 'mediaModal'}

{* Добавляем модальное окно (компонент media) в конец лэйаута чтобы избежать вложенных форм *}
{$sLayoutAfter = "$sLayoutAfter $mediaModal" scope='root'}
