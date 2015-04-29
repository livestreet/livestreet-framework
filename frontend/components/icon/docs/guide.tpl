{test_heading text='Использование'}

{capture 'test_example_content'}
    {component 'icon' icon='trash'}
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' icon='trash'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Иконки на темном фоне'}

<p>Иконкам, которые будут размещаться на темном фоне, необходимо добавить мод-ор <code>white</code></p>

{capture 'test_example_content'}
    <div style="background: #f44336; padding: 3px 5px; display: inline-block; border-radius: 3px;">
        {component 'icon' mods='white' icon='star'}
    </div>
{/capture}

{capture 'test_example_code'}
{ldelim}component 'icon' mods='white' icon='trash'{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}


{test_heading text='Список иконок'}

{$icons = [ 'glass', 'music', 'search', 'envelope', 'heart', 'star', 'star-empty', 'user', 'film', 'th-large', 'th', 'th-list', 'ok', 'remove', 'zoom-in', 'zoom-out', 'off', 'signal', 'cog', 'trash', 'home', 'file', 'time', 'road', 'download-alt', 'download', 'upload', 'inbox', 'play-circle', 'repeat', 'refresh', 'list-alt', 'lock', 'flag', 'headphones', 'volume-off', 'volume-down', 'volume-up', 'qrcode', 'barcode', 'tag', 'tags', 'book', 'bookmark', 'print', 'camera', 'font', 'bold', 'italic', 'text-height', 'text-width', 'align-left', 'align-center', 'align-right', 'align-justify', 'list', 'indent-left', 'indent-right', 'facetime-video', 'picture', 'pencil', 'map-marker', 'adjust', 'tint', 'edit', 'share', 'check', 'move', 'step-backward', 'fast-backward', 'backward', 'play', 'pause', 'stop', 'forward', 'fast-forward', 'step-forward', 'eject', 'chevron-left', 'chevron-right', 'plus-sign', 'minus-sign', 'remove-sign', 'ok-sign', 'question-sign', 'info-sign', 'screenshot', 'remove-circle', 'ok-circle', 'ban-circle', 'arrow-left', 'arrow-right', 'arrow-up', 'arrow-down', 'share-alt', 'resize-full', 'resize-small', 'plus', 'minus', 'asterisk', 'exclamation-sign', 'gift', 'leaf', 'fire', 'eye-open', 'eye-close', 'warning-sign', 'plane', 'calendar', 'random', 'comment', 'magnet', 'chevron-up', 'chevron-down', 'retweet', 'shopping-cart', 'folder-close', 'folder-open', 'resize-vertical', 'resize-horizontal']}

{if sort($icons)}
    <table class="ls-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Icon</th>
            </tr>
        </thead>
        <tbody>
            {foreach $icons as $icon}
                <tr>
                    <td>{$icon}</td>
                    <td>{component 'icon' icon=$icon}</td>
                </tr>
            {/foreach}
        </tbody>
    </table>
{/if}