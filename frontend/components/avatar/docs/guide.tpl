<p>Аватар</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'avatar' image=$oUserCurrent->getProfileAvatarPath(100)}
    {component 'avatar' image=$oUserCurrent->getProfileAvatarPath(100) size='small'}
    {component 'avatar' image=$oUserCurrent->getProfileAvatarPath(100) size='xsmall'}
    {component 'avatar' image=$oUserCurrent->getProfileAvatarPath(100) size='inline'}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Avatar name'}

{capture 'test_example_content'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100)}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='small'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='xsmall'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='inline'}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Avatar name inline'}

{capture 'test_example_content'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) mods='inline' url='/'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='small' mods='inline' url='/'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='xsmall' mods='inline' url='/'}
    {component 'avatar' template='name' name='username' image=$oUserCurrent->getProfileAvatarPath(100) size='inline' mods='inline' url='/'}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='List'}

{capture 'test_example_content'}
    {$items = []}

    {section 'pagination' start=0 loop=10}
        {$items[] = [
            name => 'username username username',
            image => $oUserCurrent->getProfileAvatarPath(100),
            url => '/'
        ]}
    {/section}

    {component 'avatar' template='list' items=$items}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}