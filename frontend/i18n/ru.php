<?php

return array(
	/**
	 * Валидация данных
	 */
	'validate'=>array(
		'empty_error'=>'Необходимо заполнить поле %%field%%',
		'string'=>array(
			'too_long' => 'Поле %%field%% слишком длинное (максимально допустимо %%max%% символов)',
			'too_short' => 'Поле %%field%% слишком короткое (минимально допустимо %%min%% символов)',
			'no_lenght' => 'Поле %%field%% неверной длины (необходимо %%length%% символов)',
		),
		'email'=>array(
			'not_valid' => 'Поле %%field%% не соответствует формату email адреса',
		),
		'number'=>array(
			'must_integer' => 'Поле %%field%% должно быть целым числом',
			'must_number' => 'Поле %%field%% должно быть числом',
			'too_small' => 'Поле %%field%% слишком маленькое (минимально допустимо число %%min%%)',
			'too_big' => 'Поле %%field%% слишком большое (максимально допустимо число %%max%%)',
		),
		'type'=>array(
			'error' => 'Поле %%field%% должно иметь тип %%type%%',
		),
		'date'=>array(
			'format_invalid' => 'Поле %%field%% имеет неверный формат даты',
		),
		'boolean'=>array(
			'invalid' => 'Поле %%field%% должно быть %%true%% или %%false%%',
		),
		'required'=>array(
			'must_be' => 'Поле %%field%% должно иметь значение %%value%%',
			'cannot_blank' => 'Поле %%field%% не может быть пустым',
		),
		'url'=>array(
			'not_valid' => 'Поле %%field%% не соответствует формату URL адреса',
		),
		'captcha'=>array(
			'not_valid' => 'Поле %%field%% содержит неверный код',
		),
		'compare'=>array(
			'must_repeated' => 'Поле %%field%% должно повторять %%compare_field%%',
			'must_not_equal' => 'Поле %%field%% не должно повторять %%compare_value%%',
			'must_greater' => 'Поле %%field%% должно быть больше чем %%compare_value%%',
			'must_greater_equal' => 'Поле %%field%% должно быть больше или равно %%compare_value%%',
			'must_less' => 'Поле %%field%% должно быть меньше чем %%compare_value%%',
			'must_less_equal' => 'Поле %%field%% должно быть меньше или равно %%compare_value%%',
			'invalid_operator' => 'У поля %%field%% неверный оператор сравнения %%operator%%',
		),
		'regexp'=>array(
			'not_valid' => 'Поле %%field%% неверное',
			'invalid_pattern' => 'У поля %%field%% неверное регулярное выражение',
		),
		'tags'=>array(
			'count_more' => 'Поле %%field%% содержит слишком много тегов (максимально допустимо %%count%%)',
			'empty' => 'Поле %%field%% не содержит тегов, либо содержит неверные теги (размер тега допустим от %%min%% до %%max%% символов)',
		),
		'enum'=>array(
			'invalid' => 'В перечислении %%field%% указан некорректный тип данных',
			'not_allowed' => 'В перечислении %%field%% значение "%%value%%" не является разрешенным',
		),
		'method'=>array(
			'invalid' => 'У поля %%field%% неверно задан метод валидации',
			'error' => 'Поле %%field%% содержит неверное значение',
		),
	),
	/**
	 * Постраничность
	 */
	'pagination'=>array(
		'next'=>'следующая',
		'previos'=>'предыдущая',
		'last'=>'последняя',
		'next'=>'первая',
		'first'=>'следующая',
		'page_list'=>'Страницы',
		'page_with_number'=>'Страница %%number%%',
		'notices' => array(
			'first' => 'Вы на первой странице!',
			'last'  => 'Вы на последней странице!'
		)
	),
);