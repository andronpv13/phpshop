<?php

/**
 * Библиотека работы с CommerceML
 * @version 2.0
 * @package PHPShopClass
 * https://v8.1c.ru/tekhnologii/obmen-dannymi-i-integratsiya/standarty-i-formaty/protokol-obmena-s-saytom/
 * https://dev.1c-bitrix.ru/api_help/sale/xml/contragents.php
 */
class PHPShopCommerceML {

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopSystem;

        $this->exchange_key = $PHPShopSystem->getSerilizeParam("1c_option.exchange_key");
    }

    /**
     * Категории
     * @param array $where условие поиска
     * @return array
     */
    function category($where) {
        $Catalog = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

        // Не выводить скрытые каталоги
        $where['skin_enabled'] = "!='1'";

        $data = $PHPShopOrm->select(array('id,name,parent_to'), $where, false, array('limit' => 10000));
        if (is_array($data))
            foreach ($data as $row) {
                if ($row['id'] != $row['parent_to']) {
                    $Catalog[$row['id']]['id'] = $row['id'];
                    $Catalog[$row['id']]['name'] = $row['name'];
                    $Catalog[$row['id']]['parent_to'] = $row['parent_to'];
                }
            }

        return $Catalog;
    }

    /**
     * Категории
     * @param integer $id ИД категории
     * @return string
     */
    function setCategories($id) {
        $xml = '<Группы>';
        $category = $this->category(array('parent_to' => '=' . $id));
        foreach ($category as $val) {
            $xml .= '<Группа>
                <Ид>' . $val['id'] . '</Ид>
		<Наименование>' . str_replace(['&', '<', '>'], '', $val['name']) . '</Наименование>';
            $parent = $this->setCategories($val['id']);
            if (!empty($parent))
                $xml .= $parent;
            else
                $xml .= '<Группы/>';
            $xml .= '</Группа>';
        }

        $xml .= '</Группы>';

        return $xml;
    }

    /**
     * Изображения товара
     * @param array $product_row
     * @return string
     */
    function getImages($product_row) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . $product_row['id']), false, array('limit' => 10000));
        $xml = null;
        if (is_array($data))
            foreach ($data as $row) {
                $xml .= '<Картинка>http://' . $_SERVER['SERVER_NAME'] . $row['name'] . '</Картинка>';
            }

        if (empty($xml))
            $xml = '<Картинка>http://' . $_SERVER['SERVER_NAME'] . $product_row['pic_big'] . '</Картинка>';

        return $xml;
    }

    /**
     * Генерация CommerceML для товаров
     * @param array $data
     * @return string
     */
    function getProducts($data) {
        global $PHPShopSystem;

        $xml = null;

        // Каталоги
        $category = $this->setCategories(0);

        // Товары
        foreach ($data as $row)
            if (is_array($row)) {

                // Убираем подтипы
                if ($row['parent_enabled'] == 1)
                    continue;

                if ($this->exchange_key == 'code') {
                    $code = $row['uid'];
                    $uid = null;
                    $id = $row['external_code'];
                }

                if ($this->exchange_key == 'uid') {
                    $code = null;
                    $uid = $row['uid'];
                    $id = $row['external_code'];
                }

                if ($this->exchange_key == 'external') {
                    $code = null;
                    $uid = null;
                    $id = $row['uid'];
                }

                $item .= '
                        <Товар>
			<Ид>' . $id . '</Ид>
                        <Код>' . $code . '</Код>
			<Артикул>' . $uid . '</Артикул>
			<Наименование>' . str_replace(['&', '<', '>'], '', $row['name']) . '</Наименование>
                        <БазоваяЕдиница Код="796 " НаименованиеПолное="Штука" МеждународноеСокращение="PCE">' . $row['ed_izm'] . '</БазоваяЕдиница>
                        <ПолноеНаименование><![CDATA[' . $row['description'] . ']]></ПолноеНаименование>
			<Группы>
				<Ид>' . $row['category'] . '</Ид>
			</Группы>
                        <Описание><![CDATA[' . $row['content'] . ']]></Описание>
			<СтавкиНалогов>
				<СтавкаНалога>
					<Наименование>НДС</Наименование>
					<Ставка>' . $PHPShopSystem->getParam('nds') . '</Ставка>
				</СтавкаНалога>
			</СтавкиНалогов>
			<ЗначенияРеквизитов>
				<ЗначениеРеквизита>
					<Наименование>ТипНоменклатуры</Наименование>
					<Значение>Товар</Значение>
				</ЗначениеРеквизита>
				<ЗначениеРеквизита>
					<Наименование>Вес</Наименование>
					<Значение>' . $row['weight'] . '</Значение>
				</ЗначениеРеквизита>
			</ЗначенияРеквизитов>
                        ' . $this->getImages($row) . '
		</Товар>
                ';
            }

        $items = ' <Каталог СодержитТолькоИзменения="false">
	<Ид>1</Ид>
        <ИдКлассификатора>1</ИдКлассификатора>
	<Наименование>Основной каталог товаров</Наименование>
		<Товары>
' . $item . '
		</Товары>
	</Каталог>';

        $xml = '<?xml version="1.0" encoding="windows-1251"?>
<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . PHPShopDate::get(time(), false, true) . 'T' . date("H:i:s") . '">
    <Классификатор>
    <Ид>1</Ид>
    <Наименование>Классификатор (Основной каталог товаров)</Наименование>
    <Владелец>
       <Ид>1</Ид>
       <Наименование>' . $PHPShopSystem->getParam('name') . '</Наименование>
       <ОфициальноеНаименование>' . $PHPShopSystem->getParam('company') . '</ОфициальноеНаименование>
       <ИНН>' . $PHPShopSystem->getParam('nds') . '</ИНН>
       <КПП>' . $PHPShopSystem->getParam('kpp') . '</КПП>
    </Владелец>
	' . $category . '
    </Классификатор>
    ' . $items . '
</КоммерческаяИнформация>';
        return $xml;
    }

    /**
     * Генерация CommerceML для заказа
     * @param array $data
     * @return string
     */
    function getOrders($data) {
        global $PHPShopSystem;

        $xml = null;
        if (is_array($data))
            foreach ($data as $row)
                if (is_array($row)) {

                    $PHPShopOrder = new PHPShopOrderFunction($row['id']);
                    $this->update_status[] = $row['id'];

                    $num = 0;
                    $id = $row['id'];
                    $uid = $row['uid'];

                    if (!empty($row['user']))
                        $user = $row['user'];
                    elseif (!empty($row['ozonseller_order_data']))
                        $user = 'Ozon';
                    elseif (!empty($row['wbseller_order_data']))
                        $user = 'WB';
                    elseif (!empty($row['yandex_order_id']))
                        $user = 'Яндекс.Маркет';
                    elseif (!empty($row['megamarket_order_id']))
                        $user = 'МегаМаркет';

                    $order = unserialize($row['orders']);
                    $status = unserialize($row['status']);
                    $sum = $PHPShopOrder->returnSumma($order['Cart']['sum'], $order['Person']['discount']);

                    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
                    $delivery = str_replace(['&', '<', '>'], '', $PHPShopDelivery->getCity());
                    $delivery_id = $PHPShopDelivery->getParam('external_code');

                    if (empty($delivery))
                        $delivery = 'Доставка';

                    if (empty($delivery_id))
                        $delivery_id = 'ORDER_DELIVERY';

                    $item = '<Товар>
		                   <Ид>' . $delivery_id . '</Ид>
		                   <Наименование>' . $delivery . '</Наименование>
				   <ЦенаЗаЕдиницу>' . $order['Cart']['dostavka'] . '</ЦенаЗаЕдиницу>
				   <Количество>1</Количество>
				   <Сумма>' . $order['Cart']['dostavka'] . '</Сумма>
				   <Единица>шт</Единица>
                                   <ЗначенияРеквизитов>
                                     <ЗначениеРеквизита>
                                         <Наименование>ВидНоменклатуры</Наименование>
                                         <Значение>Услуга</Значение>
                                     </ЗначениеРеквизита>
                                     <ЗначениеРеквизита>
                                        <Наименование>ТипНоменклатуры</Наименование>
                                       <Значение>Услуга</Значение>
                                     </ЗначениеРеквизита>
                                   </ЗначенияРеквизитов>
			     </Товар>';

                    if (is_array($order['Cart']['cart']))
                        foreach ($order['Cart']['cart'] as $val) {

                            $num = $val['num'];
                            $sum = $PHPShopOrder->returnSumma($val['price'] * $num, $order['Person']['discount']);

                            if ($this->exchange_key == 'code') {
                                $code = $val['uid'];
                                $uid = null;
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            if ($this->exchange_key == 'uid') {
                                $code = null;
                                $uid = $val['uid'];
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            if ($this->exchange_key == 'external') {
                                $code = null;
                                $uid = null;
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['id']])['external_code'];
                            }

                            // Подтип
                            if (!empty($val['parent'])) {
                                $id = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['external_code'], ['id' => '=' . $val['parent']])['external_code'] . '#' . $id;
                            }

                            $item .= '<Товар>
				<Ид>' . $id . '</Ид>
                                <Код>' . $code . '</Код>
				<Штрихкод></Штрихкод>
				<Артикул>' . $uid . '</Артикул>
				<Наименование>' . str_replace(['&', '<', '>'], '', $val['name']) . '</Наименование>
				<ЦенаЗаЕдиницу>' . $val['price'] . '</ЦенаЗаЕдиницу>
				<Количество>' . $val['num'] . '</Количество>
				<Сумма>' . $sum . '</Сумма>
				<Единица>шт</Единица>
			</Товар>';
                        }

                    if (empty($row['fio']))
                        $row['fio'] = $row['org_name'];

                    if (empty($row['fio']) and ! empty($order['Person']['name_person']))
                        $row['fio'] = $order['Person']['name_person'];
                    elseif (empty($data['fio']) and !empty($row['user'])){
                        PHPShopObj::loadClass('user');
                        $PHPShopUser = new PHPShopUser($row['user']);
                        $row['fio'] = $PHPShopUser->getParam('name');
                    }

                    // Адрес доставки
                    $adr_info = null;
                    if ($row['city'])
                        $adr_info .= "город: " . $row['city'];
                    if ($row['index'])
                        $adr_info .= ", индекс: " . $row['index'];
                    if ($row['street'])
                        $adr_info .= ", улица: " . $row['street'];
                    if ($row['house'])
                        $adr_info .= ", дом: " . $row['house'];
                    if ($row['porch'])
                        $adr_info .= ", подъезд: " . $row['porch'];
                    if ($row['door_phone'])
                        $adr_info .= ", код домофона: " . $row['door_phone'];
                    if ($row['flat'])
                        $adr_info .= ", квартира: " . $row['flat'];
                    if ($row['delivtime'])
                        $adr_info .= ", время доставки: " . $row['delivtime'];
                    if ($row['dop_info'])
                        $adr_info .= ', ' . str_replace(['&', '<', '>'], '', $row['dop_info']);
                    
                    $xml .= '
	<Документ>
                <Ид>' . $row['id'] . '</Ид>
		<Номер>' . $row['uid'] . '</Номер>
		<Дата>' . PHPShopDate::get($row['datas'], false, true)  . '</Дата>
                <Время>' . date("H:i:s") . '</Время>
                <Комментарий>' . html_entity_decode($status['maneger']) . '[Номер документа на сайте: ' . $row['uid'] . ']</Комментарий>
		<ХозОперация>Заказ товара</ХозОперация>
		<Роль>Продавец</Роль>
		<Валюта>' . $PHPShopSystem->getDefaultValutaIso() . '</Валюта>
		<Сумма>' . $row['sum'] . '</Сумма>
                <Контрагенты>
		   <Контрагент>
                     <Ид>' . $user . '</Ид>
		      <Наименование>' . html_entity_decode($row['fio']) . '</Наименование>
		      <ПолноеНаименование>' . html_entity_decode($row['org_name']) . '</ПолноеНаименование>
		      <ИНН>' . $row['org_inn'] . '</ИНН>
		      <КПП>' . $row['org_kpp'] . '</КПП>
		      <Роль>Покупатель</Роль>
                      <АдресРегистрации>
                        <Представление>' . html_entity_decode($adr_info) . '</Представление>
                        <АдресноеПоле>
                          <Тип>Город</Тип>
                          <Значение>' . html_entity_decode($row['city']) . '</Значение>
                        </АдресноеПоле>
                        <АдресноеПоле>
                          <Тип>Улица</Тип>
                          <Значение>' . html_entity_decode($row['street']) . '</Значение>
                        </АдресноеПоле>
                        <АдресноеПоле>
                          <Тип>Дом</Тип>
                          <Значение>' . html_entity_decode($row['house']) . '</Значение>
                        </АдресноеПоле>
                        <АдресноеПоле>
                          <Тип>Квартира</Тип>
                          <Значение>' . html_entity_decode($row['flat']) . '</Значение>
                        </АдресноеПоле>
                      </АдресРегистрации>
                        <Контакты>
			       <Контакт>
					<Тип>Электронная почта</Тип>
					<Значение>' . $PHPShopOrder->getMail() . '</Значение>
				</Контакт>
				<Контакт>
					<Тип>Телефон рабочий</Тип>
					<Значение>' . $row['tel'] . '</Значение>
				</Контакт>
			</Контакты>
		   </Контрагент>
                </Контрагенты>
		<Товары>
                  ' . $item . '
		</Товары>
                <ЗначенияРеквизитов>
                  <ЗначениеРеквизита>
                     <Наименование>Метод оплаты</Наименование>
                     <Значение>' . $PHPShopOrder->getOplataMetodName() . '</Значение>
                  </ЗначениеРеквизита>
                  <ЗначениеРеквизита>
		      <Наименование>Адрес доставки</Наименование>
		      <Значение>' . html_entity_decode($adr_info) . '</Значение>
		  </ЗначениеРеквизита>
        </ЗначенияРеквизитов>
	</Документ>';
                }

        $xml = '<?xml version="1.0" encoding="windows-1251"?>
<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . PHPShopDate::get(time(), false, true) . 'T' . date("H:i:s") . '">
	' . $xml . '
</КоммерческаяИнформация>';

        return $xml;
    }

}

?>