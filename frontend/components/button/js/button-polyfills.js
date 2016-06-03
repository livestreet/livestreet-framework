/**
 * Polyfills
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
 */

/**
 * Атрибут form
 * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button#attr-form
 */
jQuery(function ($) {
    // http://html5test.com/
    var element = document.createElement('div');
    document.body.appendChild(element);
    element.innerHTML = '<form id="form"></form><input form="form">';

    if (element.lastChild.form != element.firstChild) {
        $(document).on('click', 'button[form]', function () {
            $('#' + $(this).attr('form')).submit();
        });
    }

    document.body.removeChild(element);
});