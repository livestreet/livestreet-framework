<p>Позволяет загружать и управлять файлами.</p>

{test_heading text='Использование'}

<p>TODO</p>

{test_heading text='Прикрепление файлов'}

<p>Пример использования:</p>

{capture 'test_example_code'}
$('.js-uploader-attach').lsUploaderAttach({
    urls: {
        // Загрузка файла
        upload: aRouter['ajax'] + 'media/upload/',
        // Подгрузка файлов
        load: aRouter['ajax'] + 'media/load-gallery/',
        // Удаление файла
        remove: aRouter['ajax'] + 'media/remove-file/',
        // Обновление св-ва
        update_property: aRouter['ajax'] + 'media/save-data-file/',
        // Кол-во загруженных файлов
        count: aRouter['ajax'] + 'media/count/',
        // Генерация временного хэша
        generate_target_tmp: aRouter['ajax'] + 'media/generate-target-tmp/'
    }
});
{/capture}

{test_code code=$smarty.capture.test_example_code}

{capture 'test_example_code'}
{ldelim}component 'uploader' template='attach'
    classes='js-uploader-attach'
    count=15
    attributes=[
        'data-param-target_id' => 53,
        'data-param-target_type' => 'topic'
    ]{rdelim}
{/capture}

{test_code code=$smarty.capture.test_example_code}
