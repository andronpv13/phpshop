<?
function pbrf_get($data, $type, $blank) {
	//Получаем данные
	$res = pbrf_get_data($data, $type, $blank);
    //Обработка ошибок
    get_error($res);
    //Вывод бланка
    get_blank($type, $res);
}

function pbrf_get_data($data, $type, $blank) {
    global $PHPShopModules;

    //Данные system modules
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));
    $pbrf_system = $PHPShopOrm->select();
	//ключ
    $access_token = $pbrf_system['key']; // '81435c7caecdb08e67e6a6d99f699fc6';

	//функция к которой делаем запрос
    $url = 'http://pbrf.ru/'.$type.'.'.$blank;
    //Смена кодировки
    $data = iconv_array_win1251_to_utf8($data);

    //Подготовка данных для передачи
    $post = array(
        'access_token' => $access_token,
        'data' => json_encode($data)
    );

    //Инициализируем библиотеку
    $ch = curl_init();
    //Устанавливаем адрес куда будем отправлять запрос
    curl_setopt($ch, CURLOPT_URL, $url);
    //Указываем, что полученные данные не выводить сразу на экран
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //Устанавливаем метод запроса POST
    curl_setopt($ch, CURLOPT_POST, true);
    //Передаем подготовленные данные
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    //Передаем полученный ответ переменной
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}




function iconv_array_win1251_to_utf8($data) {
	if(isset($data)) {
		foreach ($data as $key => $value) {

			if($key=='object') {
				foreach ($value as $k => $v) {
					$data[ $key ][$k]['name'] = iconv ('windows-1251', 'utf-8', $v['name']);
				}
			}
			else {
				$data[ $key ] = iconv ('windows-1251', 'utf-8', $value);
			}
		}
	}
	
	return $data;
}

function get_blank($type, $res) {
    // Если PDF
    if($type=='pdf'):
        $url_pdf = $res;
        //Скачиваем файл
        header('Content-type: application/pdf');  
        header("Content-Disposition: attachment; filename=\"$url_pdf\"");   
        readfile($url_pdf);
    endif;

    // Если на Печать
    if($type=='print'):
        //Кнопка печать
        $orint_html = '<div align="right" class="nonprint">
            <button onclick="window.print()">
                <img border=0 align=absmiddle hspace=3 vspace=3 src="/phpshop/admpanel/img/action_print.gif">Распечатать
            </button> 
            <br><br>
        </div>';
        //Смена кодировки
        $res = iconv ('utf-8', 'windows-1251', $res);
        $orint_html = iconv ('utf-8', 'windows-1251', $orint_html);
        //Выводим бланк
        echo $orint_html.$res;
        //Стоп загрузке..
        exit();
    endif;
}

function get_error($res) {
    $error = json_decode($res, true);
    if($error['error']!='') {
        echo '<b style="color:red;">'.$error['message'].'</b>';
        exit();
    }
}

?>