{**
 * Подтверждение действия
 *}
{test_heading text='Подтверждение действия'}

{capture 'test_example_content'}
    <script>
        domReady(function() {
            $('.js-my-confirm').lsConfirm({
                message: "Are you sure?"
            });

            $('.js-my-confirm-callback').lsConfirm({
                message: "Are you sure?",
                onconfirm: function () {
                    alert("Confirmed!");
                },
                oncancel: function () {
                    alert("Canceled!");
                }
            });
        });
    </script>

    {component 'button' text="Delete (url)" url="/" classes="js-my-confirm"}
    {component 'button' text="Delete (callback)" classes="js-my-confirm-callback"}
{/capture}

{capture 'test_example_code'}
<script>
    jQuery(function($) {
        $('.js-my-confirm').lsConfirm({
            message: "Are you sure?"
        });

        $('.js-my-confirm-callback').lsConfirm({
            message: "Are you sure?",
            onconfirm: function () {
                alert("Confirmed!");
            },
            oncancel: function () {
                alert("Canceled!");
            }
        });
    });
</script>

{ldelim}component 'button' text="Delete (url)" url="/" classes="js-my-confirm"{rdelim}
{ldelim}component 'button' text="Delete (callback)" classes="js-my-confirm-callback"{rdelim}
{/capture}

{test_example content=$smarty.capture.test_example_content code=$smarty.capture.test_example_code}