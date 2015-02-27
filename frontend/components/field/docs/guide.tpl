<p>Поля форм.</p>

{**
 * Общие параметры
 *}
{test_heading text='Общие параметры'}

<p>Параметры используемые большинством полей:</p>

<table class="table">
    <thead>
        <tr>
            <th>Параметр</th>
            <th>Тип</th>
            <th>По&nbsp;умолчанию</th>
            <th>Описание</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><code>name</code></td>
            <td>string</td>
            <td>null</td>
            <td>Атрибут <code>name</code> input'а</td>
        </tr>
        <tr>
            <td><code>label</code></td>
            <td>string</td>
            <td>null</td>
            <td>Описание поля</td>
        </tr>
        <tr>
            <td><code>note</code></td>
            <td>string</td>
            <td>null</td>
            <td>Вспомогательный текст</td>
        </tr>
        <tr>
            <td><code>value</code></td>
            <td>string</td>
            <td>null</td>
            <td>Атрибут <code>value</code> input'а</td>
        </tr>
        <tr>
            <td><code>placeholder</code></td>
            <td>string</td>
            <td>null</td>
            <td>Атрибут <code>placeholder</code> input'а</td>
        </tr>
        <tr>
            <td><code>useValue</code></td>
            <td>boolean</td>
            <td>true</td>
            <td>Использовать атрибут value у input'а или нет</td>
        </tr>
        <tr>
            <td><code>isDisabled</code></td>
            <td>boolean</td>
            <td>false</td>
            <td>Атрибут <code>disabled</code> input'а</td>
        </tr>
        <tr>
            <td><code>inputAttributes</code></td>
            <td>array</td>
            <td>null</td>
            <td>Список атрибутов input'а</td>
        </tr>
        <tr>
            <td><code>inputClasses</code></td>
            <td>string</td>
            <td>null</td>
            <td>Список классов input'а</td>
        </tr>
        <tr>
            <td><code>rules</code></td>
            <td>array</td>
            <td>null</td>
            <td>Список правил валидации</td>
        </tr>
    </tbody>
</table>


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
{test_heading text='Date'}

{capture 'test_example_content'}
    {component 'field'
        template = 'date'
        label  = 'Date'}

    {component 'field'
        template = 'date'
        useTime = true
        label  = 'Date Time'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'field' template='date' label='Date'{rdelim}
{ldelim}component 'field' template='date' label='Date Time' useTime=true{rdelim}
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