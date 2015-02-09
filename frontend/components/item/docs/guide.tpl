{**
 * Использование
 *}

<p>TODO</p>

{test_heading text='Использование'}

<p>TODO</p>

{capture 'test_example_content'}
    {component 'item'
        title='Lorem ipsum dolor sit amet'
        desc='Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus vero esse necessitatibus sed atque nobis laudantium, nemo pariatur natus modi.'
        image=[
            'path' => $oUserCurrent->getProfileAvatarPath(100)
        ]}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'item'
    title='Lorem ipsum...'
    desc='Lorem ipsum...'
    image=[
        'path' => $oUserCurrent->getProfileAvatarPath(100)
    ]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{**
 * Группированные списки
 *}

{test_heading text='Группированные списки'}

<p>TODO</p>

{capture 'test_example_content'}
    {component 'item' template='group' items=[
        [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, non!' ],
        [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, non! consectetur adipisicing elit. Repellat, non!', 'title' => 'Lorem ipsum dolor sit amet' ],
        [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, non! consectetur adipisicing elit. Repellat, non!', 'title' => 'Lorem ipsum dolor sit amet', image => [
            'path' => $oUserCurrent->getProfileAvatarPath(100)
        ]],
        [ 'desc' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellat, non! consectetur adipisicing elit. Repellat, non!', 'title' => 'Lorem ipsum dolor sit amet', image => [
            'path' => $oUserCurrent->getProfileAvatarPath(100)
        ]]
    ]}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}