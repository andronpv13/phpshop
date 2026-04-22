<?

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.smspircompany.smspircompany_message"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules;
    $PHPShopModules->getUpdate();
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (!isset($_POST['cascade_enabled_new'])) {
        $_POST['cascade_enabled_new'] = 0;
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=smspircompany');
    return $action;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;

    $PHPShopGUI->includeJava.='
    <style>
      .module_section .tab-pane > .btn {
        margin: 3px;
        color: #fff;
      }
      
      .module_section textarea {
        margin-top: 10px;
      }
      
    </style>
    <script type="text/javascript">

        $.fn.extend({
            insertAtCaret: function(myValue){
                return this.each(function(i) {
                    if (document.selection) {
                        // Для браузеров типа Internet Explorer
                        this.focus();
                        var sel = document.selection.createRange();
                        sel.text = myValue;
                        this.focus();
                    }
                    else if (this.selectionStart || this.selectionStart == "0") {
                        // Для браузеров типа Firefox и других Webkit-ов
                        var startPos = this.selectionStart;
                        var endPos = this.selectionEnd;
                        var scrollTop = this.scrollTop;
                        this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                        this.focus();
                        this.selectionStart = startPos + myValue.length;
                        this.selectionEnd = startPos + myValue.length;
                        this.scrollTop = scrollTop;
                    } else {
                        this.value += myValue;
                        this.focus();
                    }
                })
            }
        });
        
        
        function snippetAdd(area,snippet){
          $("#"+area).insertAtCaret(snippet);
        }    

    </script>';

    // Выборка
    $data = $PHPShopOrm->select();
    extract($data);

    // Содержание закладки 1
    $Tab1 =$PHPShopGUI->setText("Сервер API (по умолчанию phpshop.pir.company): ", false)
        .$PHPShopGUI->setInput('text','domen_api_new',$domen_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("Логин API:", false)
        .$PHPShopGUI->setInput('text','login_api_new',$login_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("Пароль API:", false)
        .$PHPShopGUI->setInput('password','password_api_new',$password_api,false,'','');

    $Tab1.=$PHPShopGUI->setText("Телефон администратора магазина для получение уведомлений (можно указать несколько телефонов, перечислив их через запятую):", false)
        .$PHPShopGUI->setInput('text','admin_phone_new',$admin_phone,false,'','','','','(в формате: 71234567890)');

    $Tab1.=$PHPShopGUI->setText("Имя отправителя (по умолчанию PirCompany). Для использования в смс уникального имени отправителя необходимо согласование с вашим менеджером.:", false)
        .$PHPShopGUI->setInput('text','sender_new',$sender,false,'','','','','');

    $Tab1.=$PHPShopGUI->setLink("https://phpshop.pir.company/ru/reg.html", 'Перейти на сервис и зарегистрироваться', '_blank', 'margin:5px;display:inline-block;');

    $Tab1.=$PHPShopGUI->setLink("https://phpshop.pir.company/ru/cabinet.html", 'Перейти на сервис и  авторизоваться', '_blank', 'margin:5px;display:inline-block;');

    // Содержание закладки 3
    $Info = '<div>
        <div class="panel panel-default">
          <div class="panel-heading"><b>Сервис</b></div>
          <div class="panel-body">
              <p>Для начала работы с установленным модулем "PIR.Company: SMS-уведомления" необходимо пройти <a href="https://phpshop.pir.company/ru/reg.html" target="_blank">регистрацию</a>
              на сервисе PIR.Company и дождаться активации аккаунта, которая происходит с понедельника по пятницу с 9.00 до 18.00 по
              московскому времени.</p>
              <p>Стоимость смс с именем отправителя тарифицируется - 2,40 руб. Стоимость смс без имени отправителя - 2,60 руб.</p>
              <p>Для использования в смс уникального имени отправителя необходимо согласование с вашим менеджером сервиса PIR.Company.</p>
              <p>Для отправки SMS-уведомлений используется сервис <a href="http://pir.company" target="_blank">PIR.Company.</a></p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>Шаблон "Новый заказ" для администратора</b></div>
          <div class="panel-body">
              <p>Настраивается для администратора сайта. Шаблон сообщения будет приходить после оформления нового заказа.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>Шаблон "Новый заказ" для заказчика</b></div>
          <div class="panel-body">
              <p>Настраивается для уведомления заказчика о совершенном заказе.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>Шаблон "Изменение статуса заказа"</b></div>
          <div class="panel-body">
              <p>Настраивается для уведомления заказчика об изменении статуса заказа.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>Кнопки в шаблонах перед текстовым полем - <button type="button" class="btn btn-sm btn-primary">кнопка</button></b></div>
          <div class="panel-body">
              <p>Данные кнопки вставляют в текстовое поле необходимую переменную для формирования итогового сообщения перед отправкой уведомления. Чтобы вставить переменную в текстовое поле, просто поставьте курсор в нужное место поля и кликните по кнопке с нужным названием.</p>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading"><b>Каскадная отправка</b></div>
          <div class="panel-body">
               <p>Алгоритм работы:</p>
                <ol>
                    <li>Номер абонента проверяется на наличие мессенджера Viber.</li>
                    <li>Если мессенджер Viber активен, отправляется Viber сообщение.</li>
                    <li>Если мессенджер не подключен, абоненту отправляется sms-сообщение.</li>
                </ol>
                <p>Объем Viber-сообщения до 1000 символов.<br>Есть возможность использования картинок и кнопки активности.</p>
          </div>
        </div>
        <div class="well"><b>Стоимость Viber-сообщения с именем отправителя - 1,50 руб.<br>Стоимость смс с именем отправителя - 2,40 руб.</b></div>
      </div>
    ';
    
    $Tab3 = $PHPShopGUI->setInfo($Info, '', '95%');

    // Содержание закладки 4
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);
    
    // Содержание закладки 5
    $Tab5.= $PHPShopGUI->setInfo('<div><p>Студия <a href="http://www.webvk.ru" target="_blank">WEBVK</a></p><p>E-mail: <a href="mailto:mail@webvk.ru">mail@webvk.ru</a></p></div>', '', '99%');

    
    // Содержание закладки 7
    $Tab7 = $PHPShopGUI->setInfo('
      <div>
      <p><b>Служба поддержки</b></p>

      <p>График работы: с 10:00 до 19:00 (ежедневно, кроме выходных)</p>
      
      <p>Тел.: <a href="tel:8-800-550-17-89">8-800-550-13-86</a></p>
      <p><a href="mailto:support@pir.company">support@pir.company</a></p>
      </div>
    ', '', '95%');

    $TabCascade = $PHPShopGUI->setText("Сервер API каскадного режима (по умолчанию phpshop.pir.company):", false)
        .$PHPShopGUI->setInput('text','cascade_domen_api_new',$cascade_domen_api,false,'','');

    $TabCascade .= $PHPShopGUI->setText("Имя отправителя (по умолчанию media-gorod). Для использования уникального имени отправителя необходимо согласование с вашим менеджером.:", false)
        .$PHPShopGUI->setInput('text','cascade_sender_new',$cascade_sender,false,'','','','','');

    $TabCascade .= $PHPShopGUI->setCheckbox('cascade_enabled_new', '1', 'Включить каскадный режим', $cascade_enabled);

    $TabCascadeInfo = $PHPShopGUI->setPanel('<b>Описание</b>', '
        <p>Каскадная отправка.</p>
        <p>Алгоритм работы:</p>
        <ol>
            <li>Номер абонента проверяется на наличие мессенджера Viber.</li>
            <li>Если мессенджер Viber активен, отправляется Viber сообщение.</li>
            <li>Если мессенджер не подключен, абоненту отправляется sms-сообщение.</li>
        </ol>
        <p>Объем Viber-сообщения до 1000 символов.<br>Есть возможность использования картинок и кнопки активности.</p>
        <br>
        <p>Стоимость Viber-сообщения с именем отправителя - 1.50 руб.<br>Стоимость смс с именем отправителя - 2,40 руб.</p>
    ');

    $TabCascade .= $TabCascadeInfo;

    $bodyTabTemplateMessage  = '<ul class="nav nav-tabs">';
    $bodyTabTemplateMessage .=      '<li class="active"><a href="#sms-templates" data-toggle="tab">SMS</a></li>';
    $bodyTabTemplateMessage .=      '<li><a href="#viber-template" data-toggle="tab">VIBER</a></li>';
    $bodyTabTemplateMessage .= '</ul>';

    $bodyTabTemplateMessage .= '<div class="tab-content">';
    $bodyTabTemplateMessage .=      '<div class="tab-pane fade in active" id="sms-templates">';
    $bodyTabTemplateMessage .=          templates('sms', $order_template_admin_sms, $order_template_sms, $change_status_order_template_sms);
    $bodyTabTemplateMessage .=      '</div>';
    $bodyTabTemplateMessage .=      '<div class="tab-pane" id="viber-template">';
    $bodyTabTemplateMessage .=          templates('viber', $order_template_admin_viber, $order_template_viber, $change_status_order_template_viber);
    $bodyTabTemplateMessage .=      '</div>';
    $bodyTabTemplateMessage .= '</div>';

    $tabTemplateMessage = $PHPShopGUI->setPanel('<b>Шаблоны сообщений</b>', $bodyTabTemplateMessage);
    
    // Вывод формы закладки
    $PHPShopGUI->setTab(
        array("Основное", $Tab1, true),
        array("Каскадный режим", $TabCascade),
        array("Шаблоны сообщений", '<div class="module_section">' . $tabTemplateMessage . '</div>', true),
        array("Инструкция", $Tab3),
        array("О Модуле", $Tab4),
        array("Разработчик", $Tab5),
        array("Поддержка", $Tab7)
    );

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter=
            $PHPShopGUI->setInput("hidden","newsID",$id,"right",70,"","but").
            $PHPShopGUI->setInput("submit","saveID","ОК","right",70,"","but","actionUpdate");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Шаблон 'Новый заказ' для администратора
function templates($provider, $templateAdminOrder_value, $templateClientOrder_value, $templateClientOrderChangeStatus_value) {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();
    extract($data);

    $template = $PHPShopGUI->setText("<h3>Шаблон 'Новый заказ' для администратора: </h3>",false)
        .$PHPShopGUI->setInput("button","","Название интернет-магазина","","","snippetAdd('order_template_admin_".$provider."_new','Интернет-магазин - @NameShop@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Идентификатор заказа","","","snippetAdd('order_template_admin_".$provider."_new','Оформлен новый заказ №@OrderNum@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","ФИО заказчика","","","snippetAdd('order_template_admin_".$provider."_new','Имя - @UserFio@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Телефон заказчика","","","snippetAdd('order_template_admin_".$provider."_new','Телефон - @UserPhone@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","E-mail заказчика","","","snippetAdd('order_template_admin_".$provider."_new','E-mail - @UserMail@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Доставка","","","snippetAdd('order_template_admin_".$provider."_new','Способ доставки - @UserDelivery@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Страна","","","snippetAdd('order_template_admin_".$provider."_new','Страна - @UserCountry@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Регион","","","snippetAdd('order_template_admin_".$provider."_new','Регион - @UserState@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Город","","","snippetAdd('order_template_admin_".$provider."_new','Город - @UserCity@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Почтовый индекс","","","snippetAdd('order_template_admin_".$provider."_new','Почтовый индекс - @UserIndex@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Улица","","","snippetAdd('order_template_admin_".$provider."_new','Улица - @UserStreet@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Дом","","","snippetAdd('order_template_admin_".$provider."_new','Дом - @UserHouse@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Подъезд","","","snippetAdd('order_template_admin_".$provider."_new','Подъезд - @UserPorch@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Код домофона","","","snippetAdd('order_template_admin_".$provider."_new','Код домофона - @UserDoorPhone@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Квартира","","","snippetAdd('order_template_admin_".$provider."_new','Квартира - @UserFlat@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Время доставки","","","snippetAdd('order_template_admin_".$provider."_new','Время доставки - @UserDelivtime@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Доп. инфо","","","snippetAdd('order_template_admin_".$provider."_new','Доп. инфо - @UserDopInfo@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Состав заказа","","","snippetAdd('order_template_admin_".$provider."_new','СОСТАВ ЗАКАЗА:".'\r\n'."@Order@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Отдельно общая сумма заказа","","","snippetAdd('order_template_admin_".$provider."_new','Общая сумма заказа: @CommonSumOrder@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('order_template_admin_'.$provider.'_new',$templateAdminOrder_value,false,'',500);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>Текст кнопки. Не более 19 символов. При добавлении, обязательно чтоб была указана ссылка кнопки в параметре "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_admin_viber_button_text_new',$order_template_admin_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка кнопки. При добавлении, обязательно чтоб было указано название кнопки в параметре "Текст кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_admin_viber_button_url_new',$order_template_admin_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка на изображение. В случае добавления изображения, также необходимо заполнить параметры "Текст кнопки" и "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setFile($order_template_admin_viber_image_url, 'order_template_admin_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    // Содержание закладки 2
    $template .= $PHPShopGUI->setText("<h3>Шаблон 'Новый заказ' для заказчика: </h3>",false)
        .$PHPShopGUI->setInput("button","","Название интернет-магазина","","","snippetAdd('order_template_".$provider."_new','Интернет-магазин - @NameShop@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Идентификатор заказа","","","snippetAdd('order_template_".$provider."_new','Ваш номер заказа - @OrderNum@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Статус заказа","","","snippetAdd('order_template_".$provider."_new','Статус Вашего заказа - @OrderStatus@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setInput("button","","Состав заказа","","","snippetAdd('order_template_".$provider."_new','СОСТАВ ЗАКАЗА:".'\r\n'."@Order@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('order_template_'.$provider.'_new',$templateClientOrder_value,false,'',150);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>Текст кнопки. Не более 19 символов. При добавлении, обязательно чтоб была указана ссылка кнопки в параметре "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_viber_button_text_new',$order_template_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка кнопки. При добавлении, обязательно чтоб было указано название кнопки в параметре "Текст кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','order_template_viber_button_url_new',$order_template_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка на изображение. В случае добавления изображения, также необходимо заполнить параметры "Текст кнопки" и "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setFile($order_template_viber_image_url, 'order_template_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    $template .= $PHPShopGUI->setText("<h3>Шаблон 'Изменение статуса заказа' для заказчика: </h3>",false)
        .$PHPShopGUI->setInput("button","","Название интернет-магазина","","","snippetAdd('change_status_order_template_".$provider."_new','Интернет-магазин - @NameShop@')","btn-sm btn-primary","","","")
        .$PHPShopGUI->setInput("button","","Идентификатор заказа","","","snippetAdd('change_status_order_template_".$provider."_new','Ваш номер заказа - @OrderNum@')","btn-sm btn-primary","","","")
        .$PHPShopGUI->setInput("button","","Статус заказа","","","snippetAdd('change_status_order_template_".$provider."_new','Статус Вашего заказа изменен на - @OrderStatus@')","btn-sm btn-primary","",
            "","")
        .$PHPShopGUI->setTextarea('change_status_order_template_'.$provider.'_new',$templateClientOrderChangeStatus_value,false,'',150);

    if ($provider == 'viber') {
        $template .= $PHPShopGUI->setText('<h5>Текст кнопки. Не более 19 символов. При добавлении, обязательно чтоб была указана ссылка кнопки в параметре "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','change_status_order_template_viber_button_text_new',$change_status_order_template_viber_button_text,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка кнопки. При добавлении, обязательно чтоб было указано название кнопки в параметре "Текст кнопки"</h5>',false);
        $template .= $PHPShopGUI->setInput('text','change_status_order_template_viber_button_url_new',$change_status_order_template_viber_button_url,false,'','');
        $template .= $PHPShopGUI->setText('<h5>Ссылка на изображение. В случае добавления изображения, также необходимо заполнить параметры "Текст кнопки" и "Ссылка кнопки"</h5>',false);
        $template .= $PHPShopGUI->setFile($change_status_order_template_viber_image_url, 'change_status_order_template_viber_image_url_new',array('load' => false, 'server' => 'file', 'url' => false));
    }

    return $template;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'],'actionStart');


?>


