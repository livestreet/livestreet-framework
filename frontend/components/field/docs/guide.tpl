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
            $('.js-my-datepicker').lsFieldDate();
            $('.js-my-datetimepicker').lsFieldDatetime();
            $('.js-my-timepicker').lsFieldTime();
        });
    </script>

    {component 'field'
        template = 'date'
        label  = 'Date'
        inputClasses = 'js-my-datepicker'}

    {component 'field'
        template = 'datetime'
        label  = 'Date Time'
        inputClasses = 'js-my-datetimepicker'}

    {component 'field'
        template = 'time'
        label  = 'Time'
        inputClasses = 'js-my-timepicker'}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-datepicker').lsFieldDate();
        $('.js-my-datetimepicker').lsFieldDatetime();
        $('.js-my-timepicker').lsFieldTime();
    });
</script>

{ldelim}component 'field'
    template = 'date'
    label  = 'Date'
    inputClasses = 'js-my-datepicker'{rdelim}

{ldelim}component 'field'
    template = 'datetime'
    label  = 'Date Time'
    inputClasses = 'js-my-datetimepicker'{rdelim}

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