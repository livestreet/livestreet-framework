{**
 * Вывод категорий на странице создания нового объекта
 *}

{* TODO: Конфликт со спец параметром params компонентов *}
{$params = $smarty.local.params}
{$categoriesSelected = $smarty.local.categoriesSelected}
{$categories = $smarty.local.categories}

{* Получаем id выделеных категорий *}
{$formField = {field_get_value form=$_aRequest name=$params.form_field}}

{if $params.form_fill_current_from_request && $formField}
    {$selected = $formField}
{elseif $categoriesSelected}
    {$selected = []}

    {foreach $categoriesSelected as $category}
        {$selected[] = $category->getId()}
    {/foreach}
{/if}

{* Формируем список категорий для select'а *}
{$items = []}

{if ! $params.validate_require}
    {$items[] = [ 'value' => '', 'text' => '&mdash;' ]}
{/if}

{foreach $categories as $category}
    {$entity = $category.entity}
    {$items[] = [ 'value' => $entity->getId(), 'text' => $entity->getTitle(), 'level' => $category.level ]}
{/foreach}

{* Селект *}
{component 'field' template='select' name="{$params.form_field}[]" items=$items label={lang 'field.category.label'} selectedValue=$selected isMultiple=$params.multiple}