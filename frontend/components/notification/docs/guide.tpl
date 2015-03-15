{test_heading text='Использование'}

{capture 'test_example_content'}
    <script>
        $(function ($) {
            $('.js-show-notification').on('click', function () {
                ls.notification.show("Новое сообщение", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti, vero!");
            });
            $('.js-show-notification-title').on('click', function () {
                ls.notification.show("Новое сообщение");
            });
            $('.js-show-notification-message').on('click', function () {
                ls.notification.show(null, "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti, vero!");
            });
            $('.js-show-notification-error').on('click', function () {
                ls.notification.error("Новое сообщение", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti, vero!");
            });
            $('.js-show-notification-info').on('click', function () {
                ls.notification.info("Новое сообщение", "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti, vero!");
            });
        })
    </script>

    {component 'button' text='Показать' classes='js-show-notification'}
    {component 'button' text='Только заголовок' classes='js-show-notification-title'}
    {component 'button' text='Только сообщение' classes='js-show-notification-message'}
    <br>
    <br>
    {component 'button' text='Ошибка' classes='js-show-notification-error'}
    {component 'button' text='Информация' classes='js-show-notification-info'}
{/capture}

{capture 'test_example_code'}
<script>
    $(function ($) {
        // Иниц-ия
        ls.notification.init();

        $('.js-show-notification').on('click', function () {
            ls.notification.show("Новое сообщение", "Lorem ipsum...");
        });
        $('.js-show-notification-title').on('click', function () {
            ls.notification.show("Новое сообщение");
        });
        $('.js-show-notification-message').on('click', function () {
            ls.notification.show(null, "Lorem ipsum...");
        });
        $('.js-show-notification-error').on('click', function () {
            ls.notification.error("Новое сообщение", "Lorem ipsum...");
        });
        $('.js-show-notification-info').on('click', function () {
            ls.notification.info("Новое сообщение", "Lorem ipsum...");
        });
    })
</script>

{ldelim}component 'button
    text='Показать'
    classes='js-show-notification'{rdelim}

{ldelim}component 'button
    text='Только заголовок'
    classes='js-show-notification-title'{rdelim}

{ldelim}component 'button
    text='Только сообщение'
    classes='js-show-notification-message'{rdelim}

{ldelim}component 'button
    text='Ошибка'
    classes='js-show-notification-error'{rdelim}

{ldelim}component 'button
    text='Информация'
    classes='js-show-notification-info'{rdelim}

{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}