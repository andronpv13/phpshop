$().ready(function() {

    // Instance the tour
    var tour = new Tour({
        storage: window.sessionStorage,
        debug: false,
        template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; Назад</button> <button class="btn btn-sm btn-default" data-role="next">Далее &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Пауза</button> </div> <button class="btn btn-sm btn-default" data-role="end">Завершить</button> </div> </div>',
        steps: [
            {
                element: ".navbar-action .navbar-brand",
                title: "Обучение",
                content: 'Инструкция по заполнению основных полей для создания нового товара',
                placement: 'right'
            },
            {
                element: '[name=name_new]',
                title: "Название",
                content: 'Укажите название товара',
                placement: 'top'
            },
            {
                element: '[name=uid_new]',
                title: "Артикул",
                content: 'Укажите уникальный артикул товара. Артикул необходим для синхронизации товаров с другими программами по ведению номенклатурной базы (1С, Мой Склад)',
                placement: 'right'
            },
            {
                element: "[name=category_new]",
                title: "Категория",
                content: 'Выберите категорию размещения нового товара',
                placement: 'top'
            },

            {
                element: '[name=enabled_new]',
                title: "Опции вывода",
                content: 'Укажите опции: вывод в каталоге, cпецпредложение или новинка',
                placement: 'top'
            },
            {
                element: '[name=price_new]',
                title: "Цены",
                content: 'Укажите основную цену и дополнительные цены. Дополнительные цены могут активироваться для конкретных пользовательских статусов (оптовик, партнер). <p></p> Настройка статусов пользователей выполняется в разделе <a href="?path=shopusers.status" target="_blank">Статусы и скидки покупателей</a>',
                placement: 'right',
                onNext: function() {
                    $('[data-id="Изображение"]').tab('show');
                }

            },
            {
                element: '[data-id="Изображение"]',
                title: "Изображение",
                content: 'Откройте кликом папку выбора изображений товара для последующей их загрузки и обработки (изменение размера) на сервере. Поддерживается <b>пакетная загрузка изображений</b>.<p></p> Настройка параметров обработки изображений (маленькая и большая) выполняется в <a href="?path=system.image" target="_blank">Настройках изображений</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Описание"]').tab('show');
                }
            },
            {
                element: '[data-id="Описание"]',
                title: "Краткое описание",
                content: 'Заполните поле краткого описания товара, оно будет выводится в списке товаров у каталога. Обычно, краткое описание не более 500 символов.<p></p> Выбор визуального редактора для помощи заполнения HTML данных выполняется в <a href="?path=system#4" target="_blank">Общих настройках</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Подробно"]').tab('show');
                }
            },
            {
                element: '[data-id="Подробно"]',
                title: "Подробное описание",
                content: 'Заполните поле подробного описания товара, оно будет выводится в персональной карточке товара. Подробное описание товара не имеет лимита на количество символов. Вы можете вставлять в описания дополнительные визуальные элементы (картинки, видео).<p></p> Выбор визуального редактора для помощи заполнения HTML данных выполняется в <a href="?path=system#4" target="_blank">Общих настройках</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Дополнительно"]').tab('show');
                }
            },
            {
                element: '[data-id="Дополнительно"]',
                title: "Документы и заголовки",
                content: 'В этом разделе можно прикрепить к товару ссылки на страницы с описаниями и дополнительные файлы (инструкции, архивы, видео). При включеном режиме поддержки цифровых товаров (продажа контента), дополнительные файлы будут доступны покупателям только после оплаты заказа в своем личном кабинете. <p></p> Режим продажи контента активируется в <a href="?path=system" target="_blank">Общих настройках</a>. <p></p> Мета-заголовки необходимы для SEO-оптимизации сайта. Можно выбрать автоматической, шаблонный и ручной режимы настройки для конкретного товара.<p></p> Общая формула генерации мета-заголовков управляется в <a href="?path=system.seo" target="_blank">SEO настройках</a>.',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Характеристики"]').tab('show');
                }
            },
            {
                element: '[data-id="Характеристики"]',
                title: "Характеристики",
                content: 'Для отображения характеристик у товаров необходимо объединить характеристики в группы и выбрать эти группы у товара. Характеристики из выбранных груп появятся в товарах указанных каталогов. <p></p> Характеристики редактируются в разделе <a href="?path=sort" target="_blank">Характеристики</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Заголовки"]').tab('show');
                }
            },
            {
                element: 'button[name="saveID"] > .glyphicon-floppy-saved',
                title: "Сохранение",
                content: 'Сохранение нового товара происходит по нажатию на кнопку <kbd>Создать и редактировать</kbd>',
                placement: 'bottom'
            },
            {
                element: '.go2front',
                title: "Результат",
                content: 'Можно сразу посмотреть как выглядит страница на сайте',
                placement: 'left'
            },
            {
                element: '.setscreen',
                title: "Изменить размер",
                content: 'Можно увеличить или уменьшить размер экрана панели управления',
                placement: 'left'
            }
            


        ], onEnd: function() {
            $('[data-id="Основное"]').tab('show');
        },
    });

    // Initialize the tour
    tour.init();

    // Запуск тура
    $(".presentation").on('click', function(event) {
        event.preventDefault();
        //tour.goTo(0);
        tour.restart();
    });

    if (typeof video != 'undefined') {
        tour.goTo(0);
        tour.restart();
    }

});