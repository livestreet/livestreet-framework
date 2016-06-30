<p>Поля форм.</p>

{**
 * Общие параметры
 *}
{test_heading text='Общие параметры'}

<p>Параметры используемые большинством полей:</p>

{plugin_docs_params params=[
    [ 'name',            'string',  'null',  'Атрибут <code>name</code> input\'а' ],
    [ 'label',           'string',  'null',  'Описание поля' ],
    [ 'note',            'string',  'null',  'Вспомогательный текст' ],
    [ 'value',           'string',  'null',  'Атрибут <code>value</code> input\'а' ],
    [ 'placeholder',     'string',  'null',  'Атрибут <code>placeholder</code> input\'а' ],
    [ 'useValue',        'boolean', 'true',  'Использовать атрибут value у input\'а или нет' ],
    [ 'isDisabled',      'boolean', 'false', 'Атрибут <code>disabled</code> input\'а' ],
    [ 'inputAttributes', 'array',   'null',  'Список атрибутов input\'а' ],
    [ 'inputClasses',    'string',  'null',  'Список классов input\'а' ],
    [ 'rules',           'array',   'null',  'Список правил валидации' ]
]}


{**
 * Text
 *}
{test_heading text='Text'}

{capture 'test_example_content'}
    {component 'field'
        template = 'text'
        label = 'Label'
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='text'
    label = 'Label'
    note  = 'Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * E-mail
 *}
{test_heading text='E-mail'}

{capture 'test_example_content'}
    {component 'field'
        template = 'email'
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='email' note='Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Date
 *}
{test_heading text='Date & time'}

{capture 'test_example_content'}
    <script>
        jQuery(function($) {
            $('.js-my-datepicker').lsFieldDate({ language: LANGUAGE });
            $('.js-my-timepicker').lsFieldTime();
        });
    </script>

    {component 'field'
        template = 'date'
        label  = 'Date'
        inputClasses = 'js-my-datepicker'}

    {component 'field'
        template = 'time'
        label  = 'Time'
        inputClasses = 'js-my-timepicker'}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-datepicker').lsFieldDate({ language: LANGUAGE });
        $('.js-my-timepicker').lsFieldTime();
    });
</script>

{ldelim}component 'field'
    template = 'date'
    label  = 'Date'
    inputClasses = 'js-my-datepicker'{rdelim}

{ldelim}component 'field'
    template = 'time'
    label  = 'Time'
    inputClasses = 'js-my-timepicker'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Geo
 *}
{test_heading text='Geo'}

<p>Поле для выбора местоположения. В jquery-виджете необходимо указать ссылки возвращающие список регионов и городов. У компонента в параметре <code>countries</code> указывается список доступных стран.</p>

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-field-geo').lsFieldGeo({
            urls: {
                regions: aRouter.ajax + 'geo/get/regions/',
                cities: aRouter.ajax + 'geo/get/cities/'
            }
        });
    });
</script>

{ldelim}component 'field' template='geo'
    classes   = 'js-my-field-geo'
    label     = 'Place'
    countries = $countries{rdelim}
{/capture}

{test_code content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Textarea
 *}
{test_heading text='Textarea'}

{capture 'test_example_content'}
    {component 'field'
        template = 'textarea'
        rows = 5
        label = 'Label'
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='textarea'
    rows = 5
    label = 'Label'
    note  = 'Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Select
 *}
{test_heading text='Select'}

{capture 'test_example_content'}
    {component 'field'
        template = 'select'
        label = 'Label'
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'
        items = [
            [ 'value' => '1', 'text' => 'Item 1' ],
            [ 'value' => '2', 'text' => 'Item 2' ],
            [ 'value' => '3', 'text' => 'Item 3' ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='select'
    label = 'Login'
    note  = 'Lorem ipsum...'
    items = [
        [ 'value' => '1', 'text' => 'Item 1' ],
        [ 'value' => '2', 'text' => 'Item 2' ],
        [ 'value' => '3', 'text' => 'Item 3' ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Autocomplete
 *}
{test_heading text='Autocomplete'}

{capture 'test_example_content'}
    <script>
        $(function ($) {
            $(".js-field-autocomplete-ajax").lsFieldAutocomplete({
                max_selected_options: 3,
                urls: {
                    load: aRouter.ajax + 'autocompleter/user/'
                },
                params: {
                    extended: true
                }
            });

            $(".js-field-autocomplete").lsFieldAutocomplete();
        });
    </script>

    {component 'field'
        template = 'autocomplete'
        label = 'Ajax'
        isMultiple = true
        placeholder = 'Выберите получателей'
        inputClasses='js-field-autocomplete-ajax'
        note = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}

    {component 'field'
        template = 'autocomplete'
        label = 'Label'
        isMultiple = true
        placeholder = 'Выберите получателей'
        items = [
            [ 'value' => '1', 'text' => 'Item 1' ],
            [ 'value' => '2', 'text' => 'Item 2' ],
            [ 'value' => '3', 'text' => 'Item 3' ]
        ]
        inputClasses='js-field-autocomplete'
        note = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}
{/capture}

{capture 'test_example_code'}
<script>
    $(function ($) {
        $(".js-field-autocomplete").lsFieldAutocomplete({
            max_selected_options: 3,
            urls: {
                load: aRouter.ajax + 'autocompleter/user/'
            },
            params: {
                extended: true
            }
        });
    })
</script>

{ldelim}component 'field' template = 'autocomplete'
    label = 'Ajax'
    isMultiple = true
    placeholder = 'Выберите получателей'
    inputClasses = 'js-field-autocomplete-ajax'
    note = 'Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Radio / Checkbox
 *}
{test_heading text='Radio / Checkbox'}

{capture 'test_example_content'}
    {component 'field'
        template = 'radio'
        label = 'Label'
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}

    {component 'field'
        template = 'checkbox'
        label = 'Label'
        checked = true
        note  = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='radio'
    label = 'Label'
    note  = 'Lorem ipsum...'{rdelim}

{ldelim}component 'field' template='checkbox'
    checked = true
    label = 'Label'
    note  = 'Lorem ipsum...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Hidden
 *}
{test_heading text='Hidden'}

{capture 'test_example_code'}
{ldelim}component 'field' template='text' name='input_name' value='value'{rdelim}
{/capture}

{test_code code=$smarty.capture.test_example_code}


{**
 * CSRF Token
 *}
{test_heading text='CSRF Token'}

{capture 'test_example_code'}
{ldelim}component 'field' template='hidden.security-key'{rdelim}
{/capture}

{test_code code=$smarty.capture.test_example_code}


{**
 * Upload area
 *}
{test_heading text='Upload area'}

<p>Зона загрузки файлов.</p>

{capture 'test_example_content'}
    {component 'field' template='upload-area'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='upload-area'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Captcha
 *}
{test_heading text='Captcha'}

<p>TODO</p>


{**
 * File
 *}
{test_heading text='File'}

<p>TODO</p>


{**
 * Image
 *}
{test_heading text='Image'}

<p>TODO</p>


{**
 * Category
 *}
{test_heading text='Category'}

<p>TODO</p>


{**
 * Image ajax
 *}
{test_heading text='Image ajax'}

<p>TODO</p>



{test_heading text='Передача параметров в форму'}

<p>По умолчанию каждое поле ищет свое значение в глобальной переменной <code>$_aRequest</code>. Ключ в этом массиве является именем нужного поля, а значение - значением поля. В примере ниже, в текстовом поле с именем <code>login</code> выведется текст <code>vasya</code></p>

{capture 'test_example_content'}
    {$_aRequest = [
        login => 'vasya'
    ]}

    {component 'field.text' name='login' label='Login'}
{/capture}

{capture 'test_example_code'}
// Переменная $_aRequest задается на бэкенде
{ldelim}$_aRequest = [
    login => 'vasya'
]{rdelim}

{ldelim}component 'field.text' name='login' label='Login'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}



<p>При желании можно передать значения полей в своей переменной, но в этом случае придется передать массив со значениями в каждое поле:</p>

{capture 'test_example_code'}
// Переменная $myForm задается на бэкенде
{ldelim}$myForm = [
    login => 'vasya'
]{rdelim}

{ldelim}component 'field.text' form=$myForm name='login' label='Login'{rdelim}
{/capture}

{test_code code=$smarty.capture.test_example_code}



{test_heading text='Передача параметров в checkbox/radio'}

<p>Значение для checkbox'а может быть как булевым значением так и массивом.</p>

{capture 'test_example_content'}
    {$_aRequest = [
        my_checkbox => true
    ]}

    {component 'field.checkbox' name='my_checkbox' label='My Checkbox'}
{/capture}

{capture 'test_example_code'}
// Переменная $_aRequest задается на бэкенде
{ldelim}$_aRequest = [
    // true - checkbox отмечен, false - не отмечен
    my_checkbox => true
]{rdelim}

{ldelim}component 'field.checkbox' name='my_checkbox' label='My Checkbox'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}




<p>В случае если значение является массивом, отмечены будут только те checkbox'ы (с одинаковым атрибутом name), чьи значения (атрибут value) находятся в массиве:</p>

{capture 'test_example_content'}
    {$_aRequest = [
        my_checkbox => [ 'checkbox1', 'checkbox3' ]
    ]}

    {component 'field.checkbox' name='my_checkbox' value='checkbox1' label='My Checkbox 1'}
    {component 'field.checkbox' name='my_checkbox' value='checkbox2' label='My Checkbox 2'}
    {component 'field.checkbox' name='my_checkbox' value='checkbox3' label='My Checkbox 3'}
{/capture}

{capture 'test_example_code'}
// Переменная $_aRequest задается на бэкенде
{ldelim}$_aRequest = [
    my_checkbox => [ 'checkbox1', 'checkbox3' ]
]{rdelim}

{ldelim}component 'field.checkbox' name='my_checkbox' value='checkbox1' label='...'{rdelim}
{ldelim}component 'field.checkbox' name='my_checkbox' value='checkbox2' label='...'{rdelim}
{ldelim}component 'field.checkbox' name='my_checkbox' value='checkbox3' label='...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}



<p>Для выделение radio input'а (из группы с одинаковым атрибутом name) необходимо передать значение атрибута value:</p>

{capture 'test_example_content'}
    {$_aRequest = [
        my_radio => 'radio2'
    ]}

    {component 'field.radio' name='my_radio' value='radio1' label='My Radio 1'}
    {component 'field.radio' name='my_radio' value='radio2' label='My Radio 2'}
    {component 'field.radio' name='my_radio' value='radio3' label='My Radio 3'}
{/capture}

{capture 'test_example_code'}
// Переменная $_aRequest задается на бэкенде
{ldelim}$_aRequest = [
    my_radio => 'radio2'
]{rdelim}

{ldelim}component 'field.radio' name='my_radio' value='radio1' label='...'{rdelim}
{ldelim}component 'field.radio' name='my_radio' value='radio2' label='...'{rdelim}
{ldelim}component 'field.radio' name='my_radio' value='radio3' label='...'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}