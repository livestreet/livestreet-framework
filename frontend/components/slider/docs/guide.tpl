<p>Слайдер изображений.</p>

{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        domReady(function() {
            $('.js-my-slider').lsSlider();
        });
    </script>

    {component 'slider' classes='js-my-slider' images=[
        [ src => $oUserCurrent->getProfileFotoPath() ],
        [ src => $oUserCurrent->getProfileFotoPath() ],
        [ src => $oUserCurrent->getProfileFotoPath() ]
    ]}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-slider').lsSlider();
    });
</script>

{ldelim}component 'slider' classes='js-my-slider' images=[
    [ src => 'image1.jpg' ],
    [ src => 'image2.jpg' ],
    [ src => 'image3.jpg' ]
]{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}