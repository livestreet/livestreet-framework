{**
 * Информация об активном файле
 *}

{$component_info = 'ls-uploader-info'}
{$property_bind = 'js-uploader-info-property'}

{capture 'block_content'}
	{* Информация о файле *}
	<div class="{$component_info}-block">
		{* Основная информация о файле *}
		<div class="{$component_info}-base">
			{* Превью *}
			<img src="" alt="" class="{$component_info}-base-image {$property_bind}" data-name="image" width="100" height="100">

			{* Информация *}
			<ul class="{$component_info}-base-properties">
				<li><strong class="{$component_info}-property-name word-wrap {$property_bind}" data-name="name"></strong></li>
				<li class="{$component_info}-property-date {$property_bind}" data-name="date"></li>
				<li><span class="{$component_info}-property-size {$property_bind}" data-name="size"></span></li>
			</ul>
		</div>

		{* Информация о изображении *}
		{component 'uploader' template='block.info-group'
			type             = '1'
			properties       = [[ 'name' => 'dimensions', 'label' => {lang name='uploader.info.types.image.dimensions'} ]]
			propertiesFields = [[ 'name' => 'title', 'label' => {lang name='uploader.info.types.image.title'} ]]}

		{* @hook Конец блока с информацией о файле *}
		{hook run='uploader_info_end'}
	</div>
{/capture}

{component 'uploader' template='block'
	classes="{$component_info} js-uploader-info"
	content=$smarty.capture.block_content}