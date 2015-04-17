{$sizes = [
    'default' => 100,
    'small' => 64,
    'xsmall' => 48,
    'xxsmall' => 24,
    'text' => 24
]}

<p>Аватар</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    {foreach $sizes as $size}
        {component 'avatar' image=$oUserCurrent->getProfileAvatarPath($size@value) size=$size@key}
    {/foreach}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Avatar name'}

{capture 'test_example_content'}
    {foreach $sizes as $size}
        {component 'avatar' name='username' image=$oUserCurrent->getProfileAvatarPath($size@value) size=$size@key}
    {/foreach}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Avatar name inline'}

{capture 'test_example_content'}
    {foreach $sizes as $size}
        {component 'avatar' name='username' mods='inline' url='/' image=$oUserCurrent->getProfileAvatarPath($size@value) size=$size@key}
    {/foreach}
{/capture}

{capture 'test_example_code'}
...
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='List'}

{capture 'test_example_content'}
    {$items = []}

    {section 'avatars' start=0 loop=10}
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