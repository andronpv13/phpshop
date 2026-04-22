<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="windows-1251">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>@title@ - @server@</title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <style>
            html {
                position: relative;
                min-height: 100%;
            }
            body {
                margin-bottom: 60px;
            }
            .footer {
                position: absolute;
                bottom: 0;
                width: 100%;
                height: 60px;
                background-color: #f5f5f5;
            }
            .container {
                width: auto;
                max-width: 680px;
                padding: 0 15px;
            }
            .container .text-muted {
                margin: 20px 0;
            }
            a .glyphicon{
                padding-right: 3px;
            }
        </style>
    </head>
    <body role="document">
        <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
        <div class="container">
            <div class="page-header">
                <h1 class="text-danger"><span class="glyphicon glyphicon-exclamation-sign"></span> @title@</h1>
            </div>

            <p>Выполните инструкции по устранению ошибки или обратитесь в <a class="btn btn-info btn-xs"href="https://help.phpshop.ru" target="_blank" title="Техническая поддержка"><span class="glyphicon glyphicon-user"></span>техническую поддержку</a></p>

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><span class="glyphicon glyphicon-alert"></span> Возможные причины</h3>
                </div>
                <div class="panel-body">

                    <ol>
                        <li>Скрипт магазина PHPShop установлен на домен <mark>@server@</mark>, отличный от указанного в файле лицензии <mark>@file@</mark>.</li>
                        <li>Истек срок выданной лицензии (тестовый период 14 дней @date@).</li>
                        <li>Лицензия применена к версии магазина PHPShop, отличной от указанной в лицензии.</li>
                        <li>Нарушена целостность лицензии.</li>
                        <li>Превышено максимальное количество дополнительных витрин магазина.</li>
                    </ol>

                    <h4>Решение проблемы</h4>
                    <ol>
                        <li>Проверить лицензию на домен через <a href="https://www.phpshop.ru/order/?from=@server@">форму проверки лицензий</a>.</li>
                        <li>Удалить файл лицензии <mark>@file@</mark>.</li>
                        <li>Ознакомиться с <a href="https://help.phpshop.ru/knowledgebase/article/291">инструкцией решения проблем в лицензиях</a></li>
                        <li>Оформить <a href="https://www.phpshop.ru/order/?from=@server@#buy">заказ на покупку</a> постоянной лицензии PHPShop.</li>
                        <li>Оформить <a href="https://www.phpshop.ru/order/?from=@server@#showcase">заказ на покупку</a> лицензии для дополнительных витрин PHPShop.</li>
                        <li>Оформить <a href="https://www.phpshop.ru/order/?from=@server@#rent">заказ на аренду</a> лицензии PHPShop на своем хостинге</li>
                        <li>Продлить <a href="https://www.phpshop.ru/order/?from=@server@">техническую поддержку</a> и получить новую лицензию для PHPShop.</li>
                        </li>
                    </ol>

                </div>
            </div>

            <div class="text-muted">Переход на страницу разработчика произойдет автоматически через <span id="time">30</span> секунд.</div>

        </div>
        <footer class="footer">
            <div class="container">
                <p class="text-muted text-center">
                    Перейти <a href="https://www.phpshop.ru" target="_blank" title="Разработчик"><span class="glyphicon glyphicon-home"></span>PHPShop</a> или воспользоваться <a href="https://help.phpshop.ru" target="_blank" title="Техническая поддержка"><span class="glyphicon glyphicon-user"></span>технической поддержкой</a>
                </p>
            </div>
        </footer>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script>
            var countdown = $('#time'), timer;
            function startCountdown() {
                var startFrom = $('#time').text();
                timer = setInterval(function() {
                    countdown.text(--startFrom);
                    if (startFrom <= 0) {
                        clearInterval(timer);
                        window.location.replace('https://www.phpshop.ru/docs/error.html?SERVER_NAME=@server@');
                    }
                }, 1000);
            }
            $(document).ready(function() {
                startCountdown();
            });
        </script>
    </body>
</html>