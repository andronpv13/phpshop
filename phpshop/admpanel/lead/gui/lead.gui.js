// Переопределение функции
var TABLE_EVENT = true;
var ajax_path = "./lead/ajax/";

$().ready(function () {


    // datetimepicker
    if ($(".date").length) {
        $.fn.datetimepicker.dates['ru'] = locale;
        $(".date").datetimepicker({
            format: 'dd-mm-yyyy',
            weekStart: 1,
            language: 'ru',
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }

    // Настройка bootstrap-select
    $('.selectpicker').selectpicker({
        dropdownAlignRight: true
    });


    // Таблица данных
    if (typeof ($.cookie('data_length')) == 'undefined')
        var data_length = [10, 25, 50, 75, 100, 500];
    else
        var data_length = [parseInt($.cookie('data_length')), 10, 25, 50, 75, 100, 500];

    if ($('#data').html()) {
        var table = $('#data').dataTable({
            "ajax": {
                "type": "GET",
                "url": ajax_path + 'lead.ajax.php' + window.location.search,
                "dataSrc": function (json) {
                    $('#stat_sum').text(json.sum);
                    $('#stat_num').text(json.num);
                    $('#select_all').prop('checked', false);
                    return json.data;
                }
            },

            "processing": true,
            "serverSide": true,
            "paging": true,
            "ordering": false,
            "order": [[3, "desc"]],
            "info": false,
            "searching": false,
            "lengthMenu": data_length,
            "language": locale.dataTable,
            "stripeClasses": ['data-row', 'data-row'],
            "aoColumnDefs": [{
                    'bSortable': false,
                    'aTargets': ['sorting-hide']
                }]
        });

    }

    if ($('#kanban').length) {

        var KanbanTest = new jKanban({
            element: "#kanban",
            gutter: "5px",
            widthBoard: "191px",
            dragBoards: false,
            click: function (el) {
                window.location.href = el.getAttribute('data-link') + el.getAttribute('data-uid') + '&return=lead.kanban';
            },
            dropEl: function (el, target, source, sibling) {

                // Смена статуса
                var data = [];
                var id = el.getAttribute('data-uid');
                var parent = target.parentElement.getAttribute('data-uid');
                var link = el.getAttribute('data-link');
                var date = 0;

                if (sibling !== null)
                    date = Number(sibling.getAttribute('data-date')) + 1;
                else if (el.previousSibling !== null)
                    date = Number(el.previousSibling.getAttribute('data-date')) - 1;

                //console.log(date);

                data.push({name: 'editID', value: '1'});
                data.push({name: 'saveID', value: '1'});
                data.push({name: 'rowID', value: id});

                if (date > 10000)
                    data.push({name: 'date_new', value: date});

                // Заказы
                if (el.getAttribute('data-user') != "null") {
                    data.push({name: 'statusi_new', value: parent});
                    data.push({name: 'actionList[editID]', value: 'actionUpdate.order.edit'});
                }
                // Звонок
                else {
                    data.push({name: 'status_new', value: parent});
                    data.push({name: 'actionList[saveID]', value: 'actionSave.modules.edit'});
                }

                data.push({name: 'ajax', value: '1'});

                $.ajax({
                    mimeType: 'text/html; charset=' + locale.charset,
                    url: link + id,
                    data: data,
                    type: 'post',
                    dataType: "json",
                    async: false,
                    success: function (json) {

                        if (json['success'] == 1) {
                            showAlertMessage(locale.save_done);
                        } else
                            showAlertMessage(locale.save_false, true);
                    }
                });
            },
            buttonClick: function (el, boardId) {
                var formItem = document.createElement("form");
                formItem.setAttribute("class", "itemform");
                formItem.innerHTML =
                        '<div><textarea class="form-control form-kanban" rows="2" autofocus></textarea></div><p class="btn-group pull-right" role="group"><button type="button" id="CancelBtn" class="btn btn-default btn-xs">' + locale.cancel + '</button><button type="submit" class="btn btn-default btn-xs">' + locale.ok + '</button></p>';

                KanbanTest.addForm(boardId, formItem);
                formItem.addEventListener("submit", function (e) {
                    e.preventDefault();
                    var text = e.target[0].value;
                    formItem.parentNode.removeChild(formItem);

                    // Новое напомнинание
                    var data = [];

                    var parent = $('[data-id=' + boardId + ']').attr('data-uid');
                    data.push({name: 'ajax', value: '1'});
                    data.push({name: 'saveID', value: '1'});
                    data.push({name: 'status_new', value: parent});
                    data.push({name: 'message_new', value: escape(text)});
                    data.push({name: 'actionList[saveID]', value: 'actionInsert.menu.create'});

                    $.ajax({
                        mimeType: 'text/html; charset=' + locale.charset,
                        url: '?path=lead.kanban&action=new',
                        data: data,
                        type: 'post',
                        dataType: "json",
                        async: false,
                        success: function (json) {

                            if (json['success'] == 1) {
                                showAlertMessage(locale.save_done);

                                KanbanTest.addElement(boardId, {
                                    title: '<div class="text-muted">' + json['date'] + '<span class="glyphicon glyphicon-bookmark pull-right"></span></div><div>Событие ' + json['id'] + '</div><span class="text-muted">' + text + '</span>',
                                    id: "item-id-" + json['id'],
                                    uid: json['id'],
                                    link: '?path=lead.kanban&id=',
                                    user: null
                                });

                            } else
                                showAlertMessage(locale.save_false, true);
                        }
                    });


                });
                document.getElementById("CancelBtn").onclick = function () {
                    formItem.parentNode.removeChild(formItem);
                };
            },
            addItemButton: true,
            boards: []
        });

        $.ajax({
            mimeType: 'text/html; charset=' + locale.charset,
            url: './lead/ajax/kanban.ajax.php',
            type: 'post',
            dataType: "json",
            async: false,
            success: function (json) {
                KanbanTest.addBoards(json);

                $.each(json, function (index, value) {

                    // Цвет статуса
                    $('[data-id=' + value['id'] + '] .kanban-board-header').css('background-color', value['color']);

                    // ID статуса
                    $('[data-id=' + value['id'] + ']').attr('data-uid', value['uid']);
                });

            }
        });

        // Верхняя прокрутка
        $(".kanban-wrapper-container").css('width', $(".kanban-container").css('width'));
        $("#kanban-wrapper").scroll(function () {
            $("#kanban")
                    .scrollLeft($("#kanban-wrapper").scrollLeft());
        });
        $("#kanban").scroll(function () {
            $("#kanban-wrapper")
                    .scrollLeft($("#kanban").scrollLeft());
        });

    }
});