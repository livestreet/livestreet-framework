<p>Увеличение и просмотр изображений.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        domReady(function() {
            $('.js-my-lightbox').lsLightbox();
        });
    </script>

    <a href="{$oUserCurrent->getProfileFotoPath()}" class="js-my-lightbox">
        <img src="{$oUserCurrent->getProfileAvatarPath(100)}">
    </a>
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-lightbox').lsLightbox();
    });
</script>

<a href="large_image.jpg" class="js-my-lightbox">
    <img src="small_image.jpg">
</a>
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}