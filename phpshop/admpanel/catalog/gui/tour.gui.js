$().ready(function() {

    // Instance the tour
    var tour = new Tour({
        storage: window.sessionStorage,
        debug: true,
        template: '<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; Назад</button> <button class="btn btn-sm btn-default" data-role="next">Далее &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Пауза</button> </div> <button class="btn btn-sm btn-default" data-role="end">Завершить</button> </div> </div>',
        steps: [
            {
                element: ".navbar-action .navbar-brand",
                title: "Обучение",
                content: 'Инструкция по заполнению основных полей для создания нового каталога',
                placement: 'right'
            },
            {
                element: "[name=category_new]",
                title: "Категория",
                content: 'Выберите категорию размещения нового товара',
                placement: 'top'
            },
            {
                element: '[name=name_new]',
                title: "Наименование",
                content: 'Укажите наименование каталога',
                placement: 'top'
            },
            {
                element: '[name=skin_enabled_new]',
                title: "Опции вывода",
                content: 'Укажите опции вывода каталога',
                placement: 'right'
            },
            {
                element: '[name=num_cow_new]',
                title: "Товары",
                content: 'Укажите количество товаров для вывода на одну страницу. Если включен режим динамической пагинации, то эта опция не имеет визуального эффекта.',
                placement: 'right',
            },
            {
                element: '[name=num_new]',
                title: "Сортировка",
                content: 'Укажите порядок сортировки товаров в этом каталоге.',
                placement: 'right',
            },
            {
                element: '.link-thumbnail',
                title: "Изображение",
                content: 'Укажите иконку каталога для навигации',
                placement: 'top',
                onNext: function() {
                    $('[data-id="Описание"]').tab('show');
                }

            },
            {
                element: '[data-id="Описание"]',
                title: "Краткое описание",
                content: 'Заполните поле описания каталога, оно будет выводится в начале списка товаров в каталоге. Обычно, описание каталога не более 500 символов.<p></p> Выбор визуального редактора для помощи заполнения HTML данных выполняется в <a href="?path=system#4" target="_blank">Общих настройках</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Заголовки"]').tab('show');
                }
            },
            {
                element: '[data-id="Заголовки"]',
                title: "Заголовки",
                content: 'Мета-заголовки необходимы для SEO-оптимизации сайта. Можно выбрать автоматической, шаблонный и ручной режимы настройки для конкретного каталога.<p></p> Общая формула генерации мета-заголовков управляется в <a href="?path=system.seo" target="_blank">SEO настройках</a>',
                placement: 'bottom',
                onNext: function() {
                    $('[data-id="Характеристики"]').tab('show');
                }
            },
            {
                element: '[data-id="Характеристики"]',
                title: "Характеристики",
                content: 'Для отображения характеристик в каталоге необходимо объединить характеристики в группы и выбрать эти группы у каталога. Характеристики из выбранных груп появятся в товарах указанных каталогов. <p></p> Характеристики редактируются в разделе <a href="?path=sort" target="_blank">Характеристики</a>',
                placement: 'bottom'
            },
            {
                element: 'button[name="saveID"] > .glyphicon-floppy-saved',
                title: "Сохранение",
                content: 'Сохранение нового каталога происходит по нажатию на кнопку <kbd>Создать и редактировать</kbd>',
                placement: 'bottom'
            },
            {
                element: '.go2front',
                title: "Результат",
                content: 'Можно сразу посмотреть как выглядит страница на сайте',
                placement: 'left'
            }
        ],
        onEnd: function() {
            $('[data-id="Основное"]').tab('show');
        },
    });

    // Initialize the tour
    tour.init();

    // Запуск тура
    $(".presentation").on('click', function(event) {
        event.preventDefault();
        tour.goTo(0);
        tour.restart();
    });

    if (typeof video != 'undefined') {
        tour.goTo(0);
        tour.restart();
    }
});