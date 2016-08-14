{test_heading text='Список иконок'}

<p>Полный список иконок можно посмотреть на странице шрифта <a href="http://fontawesome.io/icons/">FontAwesome</a></p>


{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'icon' icon='puzzle-piece'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' icon='puzzle-piece'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Иконки на темном фоне'}

<p>Иконкам, которые будут размещаться на темном фоне, необходимо добавить модификатор <code>inverse</code></p>

{capture 'test_example_content'}
    <div style="background: #f44336; padding: 3px 6px; display: inline-block; border-radius: 3px;">
        {component 'icon' mods='inverse' icon='html5'}
    </div>
    <div style="background: #3c763d; padding: 3px 6px; display: inline-block; border-radius: 3px;">
        {component 'icon' mods='inverse' icon='css3'}
    </div>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='inverse' icon='star'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Размеры'}

<p>Для увеличение размера иконки относительно ее контейнера, необходимо добавить модификатор <code>lg</code> (увеличение на 33%), <code>2x</code>, <code>3x</code>, <code>4x</code> или <code>5x</code></p>

{capture 'test_example_content'}
    <p>{component 'icon' mods='lg' icon='camera-retro'} lg</p>
    <p>{component 'icon' mods='2x' icon='camera-retro'} 2x</p>
    <p>{component 'icon' mods='3x' icon='camera-retro'} 3x</p>
    <p>{component 'icon' mods='4x' icon='camera-retro'} 4x</p>
    <p>{component 'icon' mods='5x' icon='camera-retro'} 5x</p>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='lg' icon='camera-retro'{rdelim}
{ldelim}component 'icon' mods='2x' icon='camera-retro'{rdelim}
{ldelim}component 'icon' mods='3x' icon='camera-retro'{rdelim}
{ldelim}component 'icon' mods='4x' icon='camera-retro'{rdelim}
{ldelim}component 'icon' mods='5x' icon='camera-retro'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Фиксированная ширина'}

<p>Используйте модификатор <code>fw</code> чтобы установить иконке фиксированную ширину. Выравнивание иконки при этом устанавливается по центру. Будет полезно при использовании иконок в навигации.</p>

{capture 'test_example_content'}
    {component 'nav'
        activeItem = 'favourites'
        mods = 'stacked'
        classes = 'user-nav'
        items = [
            [
                'icon' => [ 'mods' => 'fw', 'icon' => 'user' ],
                'name' => 'whois',
                'text' => 'Профиль',
                'url' => $oUserCurrent->getUserWebPath()
            ],
            [
                'icon' => [ 'mods' => 'fw', 'icon' => 'star' ],
                'name' => 'favourites',
                'text' => 'Избранное',
                'url' => $oUserCurrent->getUserWebPath('favourites')
            ],
            [
                'icon' => [ 'mods' => 'fw', 'icon' => 'envelope' ],
                'name' => 'talk',
                'text' => 'Сообщения',
                'url' => {router page='talk'}
            ],
            [
                'icon' => [ 'mods' => 'fw', 'icon' => 'cogs' ],
                'name' => 'cogs',
                'text' => 'Настройки',
                'url' => {router page='settings'}
            ]
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'nav'
    activeItem = 'favourites'
    mods = 'stacked'
    classes = 'user-nav'
    items = [
        [
            'icon' => [ 'mods' => 'fw', 'icon' => 'user' ],
            'name' => 'whois',
            'text' => 'Профиль',
            'url' => {ldelim}$oUserCurrent->getUserWebPath(){rdelim}
        ],
        [
            'icon' => [ 'mods' => 'fw', 'icon' => 'star' ],
            'name' => 'favourites',
            'text' => 'Избранное',
            'url' => {ldelim}$oUserCurrent->getUserWebPath('favourites'){rdelim}
        ],
        [
            'icon' => [ 'mods' => 'fw', 'icon' => 'envelope' ],
            'name' => 'talk',
            'text' => 'Сообщения',
            'url' => {ldelim}router page='talk'{rdelim}
        ],
        [
            'icon' => [ 'mods' => 'fw', 'icon' => 'cogs' ],
            'name' => 'cogs',
            'text' => 'Настройки',
            'url' => {ldelim}router page='settings'{rdelim}
        ]
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Иконки в списках'}

<p>Используйте класс <code>fa-ul</code> (для списка) и модификатор <code>li</code> для быстрой замены маркеров на иконки в списках.</p>
{component 'alert' text='TODO: организовать как-нибудь' mods='success'}

{capture 'test_example_content'}
    <ul class="fa-ul">
        <li>
            {component 'icon' mods='li' icon='check-square'}
            Иконки
        </li>
        <li>
            {component 'icon' mods='li' icon='check-square'}
            могут использоваться
        </li>
        <li>
            {component 'icon' mods='li spinner' icon='spin'}
            как маркеры
        </li>
        <li>
            {component 'icon' mods='li' icon='square'}
            в списках
        </li>
    </ul>
{/capture}

{capture 'test_example_code'}
<ul class="fa-ul">
    <li>
        {ldelim}component 'icon' mods='li' icon='check-square'{rdelim}
        Иконки
    </li>
    <li>
        {ldelim}component 'icon' mods='li' icon='check-square'{rdelim}
        могут использоваться
    </li>
    <li>
        {ldelim}component 'icon' mods='li spinner' icon='spin'{rdelim}
        как маркеры
    </li>
    <li>
        {ldelim}component 'icon' mods='li' icon='square'{rdelim}
        в списках
    </li>
</ul>
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Обводка и обтекание'}

<p>Используйте модификатор <code>border</code> и <code>pull-right</code> или <code>pull-left</code> например для оформления цитат.</p>

{capture 'test_example_content'}
    {component 'icon' mods='border pull-left 3x' icon='quote-left'}
    <p>Есть два подхода к программированию. Первый — сделать программу настолько простой, чтобы в ней очевидно не было ошибок. А второй — сделать её настолько сложной, чтобы в ней не было очевидных ошибок. <em>Tony Hoare.</em></p>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='border pull-left 3x' icon='quote-left'{rdelim}
<p>Есть два подхода к программированию. Первый — сделать программу настолько простой, чтобы в ней очевидно не было ошибок. А второй — сделать её настолько сложной, чтобы в ней не было очевидных ошибок. <em>Tony Hoare.</em></p>
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Анимированные иконки'}

<p>Используйте модификатор <code>spin</code> чтобы иконка вращалась, <code>pulse</code> для пульсированного вращения в 8 шагов. Отлично работает с иконками <code>spinner</code>, <code>refresh</code> и <code>cog</code></p>

{capture 'test_example_content'}
    {component 'icon' mods='spin fw 3x' icon='spinner'}
    {component 'icon' mods='spin fw 3x' icon='circle-o-notch'}
    {component 'icon' mods='spin fw 3x' icon='refresh'}
    {component 'icon' mods='spin fw 3x' icon='cog'}
    {component 'icon' mods='pulse fw 3x' icon='spinner'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='spin fw 3x' icon='spinner'{rdelim}
{ldelim}component 'icon' mods='spin fw 3x' icon='circle-o-notch'{rdelim}
{ldelim}component 'icon' mods='spin fw 3x' icon='refresh'{rdelim}
{ldelim}component 'icon' mods='spin fw 3x' icon='cog'{rdelim}
{ldelim}component 'icon' mods='pulse fw 3x' icon='spinner'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Поворот и отражение'}

<p>Для произвольного вращения иконки используйте модификатор <code>rotate-*</code>. Для отражения иконок по вертикали или горизонтали используйте модификаторы <code>flip-vertical</code> и <code>flip-horizontal</code></p>

{capture 'test_example_content'}
    <div style="font-size: 1.5em">
        {component 'icon' icon='shield'} normal<br>
        {component 'icon' mods='rotate-90' icon='shield'} rotate-90<br>
        {component 'icon' mods='rotate-180' icon='shield'} rotate-180<br>
        {component 'icon' mods='rotate-270' icon='shield'} rotate-270<br>
        {component 'icon' mods='flip-horizontal' icon='shield'} flip-horizontal<br>
        {component 'icon' mods='flip-vertical' icon='shield'} flip-vertical
    </div>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' icon='shield'{rdelim} normal
{ldelim}component 'icon' mods='rotate-90' icon='shield'{rdelim} rotate-90
{ldelim}component 'icon' mods='rotate-180' icon='shield'{rdelim} rotate-180
{ldelim}component 'icon' mods='rotate-270' icon='shield'{rdelim} rotate-270
{ldelim}component 'icon' mods='flip-horizontal' icon='shield'{rdelim} flip-horizontal
{ldelim}component 'icon' mods='flip-vertical' icon='shield'{rdelim} flip-vertical
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Комбинирование иконок'}

{capture 'test_example_content'}
    <div style="font-size: 1em">
        <p>
            {component 'icon.stack' icon='square-o' in='twitter' mods='flip-horizontal'}
            <code>twitter</code> внутри <code>square-o</code>
        </p>
        <p>
            {component 'icon.stack' icon='circle' in=[ 'icon' => 'flag', 'mods' => 'inverse '] mods = '2x'}
            <code>flag</code> внутри <code>circle</code>
        </p>
        <p>
            {component 'icon.stack' icon='square' in=[ 'icon' => 'terminal', 'mods' => 'inverse' ] mods = '3x'}
            <code>terminal</code> внутри <code>square</code>
        </p>
        <p>
            {component 'icon.stack' icon=[ 'icon' => 'ban', 'attributes' => [ 'style' => 'color: #d9534f' ] ] in='camera' mods = '4x'}
            <code>camera</code> внутри <code>ban</code>
        </p>
        <p>
            {component 'icon.stack' icon=[ 'icon' => 'circle-o-notch', 'mods' => 'flip-vertical', 'attributes' => [ 'style' => 'color: #3c763d' ] ] in=[ 'icon' => 'question', 'mods' => 'spin', 'attributes' => [ 'style' => 'color: #8a6d3b' ] ] mods = '5x'}
            <code>question</code> внутри <code>circle-o-notch</code>
        </p>
    </div>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon.stack'
    icon = 'square-o'
    in = 'twitter'
    mods = 'flip-horizontal'{rdelim}

{ldelim}component 'icon.stack'
    icon = 'circle'
    in = [ 'icon' => 'flag', 'mods' => 'inverse ']
    mods = '2x'{rdelim}

{ldelim}component 'icon.stack'
    icon = 'square'
    in = [ 'icon' => 'terminal', 'mods' => 'inverse' ]
    mods = '3x'{rdelim}

{ldelim}component 'icon.stack'
    icon = [
        'icon' => 'ban',
        'attributes' => [ 'style' => 'color: #d9534f' ]
    ]
    in = 'camera'
    mods = '4x'{rdelim}

{ldelim}component 'icon.stack'
    icon = [
        'icon' => 'circle-o-notch',
        'mods' => 'flip-vertical',
        'attributes' => [ 'style' => 'color: #3c763d' ]
    ]
    in = [
        'icon' => 'question',
        'mods' => 'spin',
        'attributes' => [ 'style' => 'color: #8a6d3b' ]
    ]
    mods = '5x'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}