<?php

require_once __DIR__ . '/IntellectMoneyCommon/UserSettings.php';

/**
 * Вспомогательный класс, для взаимодействия с базой данных PHPShop.
 */
final class DataBaseHelper
{
    /**
     * Класс PHPShop, который взаимодействует с базой данных.
     * @var PHPShopOrm
     */
    private $orm;

    /**
     * Вспомогательный класс, для взаимодействия с базой данных PHPShop.
     *
     * @param string $tableName Имя таблицы, к которой мы хотим подключится.
     */
    public function __construct($tableName)
    {
        $this->orm = new PHPShopOrm($tableName);
        $this->orm->debug = false;
    }

    /**
     * Получить значение настройки по ее имени.
     * 
     * @param string $name Имя настройки.
     * 
     * @return string Значение настройки, если она существует.
     */
    public function getSetting($name)
    {
        $this->orm->clean();
        $value = $this->orm->select(['`value`'], ['`key`=' => "'" . $name . "'"])['value'];
        if ($value == 'on' || $value == 'off') {
            return $value == 'on' ? true : false;
        }
        return $value;
    }

    /**
     * Устанавливает настройку модуля в базе данных.
     *
     * @param mixed $name Имя настройки.
     * @param mixed $value Новое значение настройки.
     * 
     * @return mixed 
     * Возвращает `true` если значение успешно обновлено; в противном случае возвращает `false`.
     */
    public function setSetting($name, $value)
    {
        if ($this->isSettingExists($name)) {
            $this->orm->clean();
            return $this->orm->update(['value' => $value], ['`key`=' => "'" . $name . "'"], '');
        } else {
            $this->orm->clean();
            $id = $this->orm->insert(['key' => $name, 'value' => $value], '');
            return $id != 0;
        }
    }

    /**
     * Загрузить экземляр с настройками модуля из базы данных.
     * 
     * @return \PaySystem\UserSettings
     * Возвращает экземпляр с настройками модуля.
     */
    public function loadUserSettings()
    {
        $userSettings = PaySystem\UserSettings::getInstance();

        $params = [];
        foreach ($userSettings->getNamesForAllIntegrationMethodsToSave() as $paramName) {
            $params[$paramName] = $this->getSetting($paramName);
            if ($paramName == 'resultUrl' && empty($params[$paramName])) {
                $params[$paramName] = 'https://' . $_SERVER['HTTP_HOST'] . '/success/';
            }
        }

        $userSettings->setParams($params);
        return $userSettings;
    }

    /**
     * Получение данных о заказе по его номеру.
     * 
     * @param string $uid Номер заказа.
     * 
     * @return array 
     * Возвращает данные о заказе.
     */
    public function loadOrder($uid)
    {
        $this->orm->clean();
        return $this->orm->select(['*'], ['`uid`=' => "'" . $uid . "'"]);
    }

    /**
     * Обновить заказ. 
     * 
     * @param string $uid Номер заказа.
     * @param string $state Идентификатор нового статуса заказа.
     * 
     * @return
     */
    public function updateOrder($uid, $state)
    {
        $this->orm->clean();
        return $this->orm->update(['statusi' => $state], ['`uid`=' => "'" . $uid . "'"], '');
    }

    /**
     * Выбрать все из таблицы, с которой связан экземпяр.
     * 
     * @return array
     * Возвращает все строки таблицы.
     */
    public function selectAll()
    {
        $this->orm->clean();
        return $this->orm->select(['*']);
    }

    /**
     * Получение списка логов.
     * 
     * @return array
     * Возвращает список логов.
     */
    public function getLogs()
    {
        $this->orm->clean();
        return $this->orm->select(['*'], false, ['order' => 'timestamp DESC']);
    }

    /**
     * Вставить лог в базу данных.
     * 
     * @param string $message Сообщение.
     * @param string $kind Тип сообщения.
     * 
     * @return bool
     * Возвращает `true`, если лог успешно записан; 
     * в противном случае возвращает `false`.
     */
    public function insertLog($message, $kind)
    {
        $this->orm->clean();
        $id = $this->orm->insert(['timestamp' => time(), 'message' => $message, 'kind' => $kind], '');
        return $id != 0;
    }

    /**
     * Полученние данных о методе доставки по ее идентификатору.
     * 
     * @param string $id Идентификатор доставки.
     * 
     * @return array
     * Возвращает данные о доставке.
     */
    public function loadDelivery($id)
    {
        $this->orm->clean();
        return $this->orm->select(['*'], ['`id`=' => "'" . $id . "'"]);
    }

    /**
     * Проверяет существует ли настройка в базе данных. 
     * 
     * @param string $name Имя настройки.
     * 
     * @return boolean 
     * Возвращает `true` если настройка существует; в противном случае возвращает `false`.
     */
    private function isSettingExists($name)
    {
        $this->orm->clean();
        return $this->orm->select(['count(*)'], ['`key`=' => "'" . $name . "'"])['count(*)'] != '0';
    }
}
