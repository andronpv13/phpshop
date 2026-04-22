<?php

function rules_checked($a, $b) {
    $array = explode("-", $a);
    return $array[$b];
}

function tab_rules($row, $autofill = false) {
    global $PHPShopGUI;
    
    $status = unserialize($row['status']);
    $PHPShopGUI->checkbox_old_style = true;

    $dis = '<table id="rules" class="table table-striped table-bordered text-center ' . $autofill . ' ">
                           <tr>
                            <th class="text-center">'.__('Раздел').'</th>
                            <th class="text-center">'.__('Обзор').' <br><input id="select_rules_view" type="checkbox"></th>
                            <th class="text-center">'.__('Редактирование').' <br><input id="select_rules_edit" type="checkbox"></th>
                            <th class="text-center">'.__('Создание').' <br><input id="select_rules_creat" type="checkbox"></th>
                            <th class="text-center">'.__('Дополнительно').' <br><input id="select_rules_option" type="checkbox"></th>
                           </tr>
                            <tr>
                            <td>'.__('Настройка системы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('system_rul_1', 1, false, rules_checked($status['system'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('system_rul_2', 1, false, rules_checked($status['system'], 1)) . '</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                           <tr>
                                <td>'.__('Товары').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('catalog_rul_1', 1, false, rules_checked($status['catalog'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('catalog_rul_2', 1, false, rules_checked($status['catalog'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('catalog_rul_3', 1, false, rules_checked($status['catalog'], 2)) . '</td>
                                 <td>' . $PHPShopGUI->setCheckbox('catalog_rul_5', 1, 'Управление правами', rules_checked($status['catalog'], 4)) . ' ' . $PHPShopGUI->setCheckbox('catalog_rul_4', 1, 'Все товары', rules_checked($status['catalog'], 3)) . '</td>
                            </tr>
                            <tr>
                                <td>'.__('Заказы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('order_rul_1', 1, false, rules_checked($status['order'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('order_rul_2', 1, false, rules_checked($status['order'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('order_rul_3', 1, false, rules_checked($status['order'], 2)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('order_rul_4', 1, 'Все заказы', rules_checked($status['order'], 3)) . $PHPShopGUI->setCheckbox('order_rul_5', 1, 'Управление правами', rules_checked($status['order'], 4)).'</td>
                            </tr>
                            <tr>
                                <td>'.__('Администраторы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('users_rul_1', 1, false, rules_checked($status['users'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('users_rul_2', 1, false, rules_checked($status['users'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('users_rul_3', 1, false, rules_checked($status['users'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                           <tr>
                                <td>'.__('Пользователи').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('shopusers_rul_1', 1, false, rules_checked($status['shopusers'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('shopusers_rul_2', 1, false, rules_checked($status['shopusers'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('shopusers_rul_3', 1, false, rules_checked($status['shopusers'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>'.__('Новости').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('news_rul_1', 1, false, rules_checked($status['news'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('news_rul_2', 1, false, rules_checked($status['news'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('news_rul_3', 1, false, rules_checked($status['news'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                           <tr>
                                <td>'.__('Отчеты').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('report_rul_1', 1, false, rules_checked($status['report'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('report_rul_2', 1, false, rules_checked($status['report'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('report_rul_3', 1, false, rules_checked($status['report'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                           <td>'.__('Страницы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('page_rul_1', 1, false, rules_checked($status['page'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('page_rul_2', 1, false, rules_checked($status['page'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('page_rul_3', 1, false, rules_checked($status['page'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Текстовые блоки').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('menu_rul_1', 1, false, rules_checked($status['menu'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('menu_rul_2', 1, false, rules_checked($status['menu'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('menu_rul_3', 1, false, rules_checked($status['menu'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                             <tr>
                                <td>'.__('Отзывы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('gbook_rul_1', 1, false, rules_checked($status['gbook'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('gbook_rul_2', 1, false, rules_checked($status['gbook'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('gbook_rul_3', 1, false, rules_checked($status['gbook'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Баннеры').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('banner_rul_1', 1, false, rules_checked($status['banner'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('banner_rul_2', 1, false, rules_checked($status['banner'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('banner_rul_3', 1, false, rules_checked($status['banner'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Слайдер').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('slider_rul_1', 1, false, rules_checked($status['slider'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('slider_rul_2', 1, false, rules_checked($status['slider'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('slider_rul_3', 1, false, rules_checked($status['slider'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Рейтинг').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('rating_rul_1', 1, false, rules_checked($status['rating'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('rating_rul_2', 1, false, rules_checked($status['rating'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('rating_rul_3', 1, false, rules_checked($status['rating'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Экспорт и импорт данных').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('exchange_rul_1', 1, false, rules_checked($status['exchange'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('exchange_rul_2', 1, false, rules_checked($status['exchange'], 1)) . '</td>
                                <td></td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Скидки и статусы').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('discount_rul_1', 1, false, rules_checked($status['discount'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('discount_rul_2', 1, false, rules_checked($status['discount'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('discount_rul_3', 1, false, rules_checked($status['discount'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Валюты и склады').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('currency_rul_1', 1, false, rules_checked($status['currency'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('currency_rul_2', 1, false, rules_checked($status['currency'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('currency_rul_3', 1, false, rules_checked($status['currency'], 2)) . '</td>
                                <td>-</td>
                            </tr> 
                            <tr>
                            <td>'.__('Доставка').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('delivery_rul_1', 1, false, rules_checked($status['delivery'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('delivery_rul_2', 1, false, rules_checked($status['delivery'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('delivery_rul_3', 1, false, rules_checked($status['delivery'], 2)) . '</td>
                                <td>-</td>
                            </tr> 
                            <tr>
                            <td>'.__('Витрины').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('servers_rul_1', 1, false, rules_checked($status['servers'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('servers_rul_2', 1, false, rules_checked($status['servers'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('servers_rul_3', 1, false, rules_checked($status['servers'], 2)) . '</td>
                                <td>-</td>
                            </tr> 
                            <tr>
                                <td>'.__('Модули').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('modules_rul_1', 1, false, rules_checked($status['modules'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('modules_rul_2', 1, false, rules_checked($status['modules'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('modules_rul_3', 1, false, rules_checked($status['modules'], 2)) . '</td>
                                <td>-</td>
                            </tr>
                            <tr>
                            <td>'.__('Обновление ПО').'</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>' . $PHPShopGUI->setCheckbox('update_rul_1', 1, 'Установка обновлений', rules_checked($status['update'], 0)) . '</td>
                            </tr> 
                            <tr>
                            <td>'.__('Доступ по API').'</td>
                                <td>' . $PHPShopGUI->setCheckbox('api_rul_1', 1, false, rules_checked($status['api'], 0)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('api_rul_2', 1, false, rules_checked($status['api'], 1)) . '</td>
                                <td>' . $PHPShopGUI->setCheckbox('api_rul_3', 1, false, rules_checked($status['api'], 2)) . '</td>
                                <td >'.$row['token'].'</td>
                            </tr> 
       </table>';
    return $dis;
}
?>