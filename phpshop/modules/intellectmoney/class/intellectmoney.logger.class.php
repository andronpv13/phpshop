<?php

require_once __DIR__ . '/intellectmoney.database.helper.php';

/**
 * Класс для работы с логами.
 */
final class IMLogger
{
    /**
     * Класс, для работы с базой данных.
     * @var \DataBaseHelper
     */
    private static $database;

    /**
     * Получить все логи.
     * 
     * @return array
     * Возвращает массив с логами.
     */
    public static function GetLogs()
    {
        self::Initialize();

        return self::$database->getLogs();
    }

    /**
     * Запись в лог сообщения.
     * 
     * @param string $message Сообщение.
     * 
     * @return bool
     * Возвращает `true`, если лог успешно записан;
     * `false` в противном случае.
     */
    public static function Log($message)
    {
        self::Initialize();

        return self::$database->insertLog($message, 'info');
    }

    /**
     * Запись в лог ошибки.
     * 
     * @param string $message Сообщение.
     * 
     * @return bool
     * Возвращает `true`, если лог успешно записан;
     * `false` в противном случае.
     */
    public static function Error($message)
    {
        self::Initialize();

        return self::$database->insertLog($message, 'danger');
    }

    /**
     * Запись в лог чего-то хорошего.
     * 
     * @param string $message Сообщение.
     * 
     * @return bool
     * Возвращает `true`, если лог успешно записан;
     * `false` в противном случае.
     */
    public static function Good($message)
    {
        self::Initialize();

        return self::$database->insertLog($message, 'success');
    }

    /**
     * Инициализация класса.
     */
    private static function Initialize()
    {
        if(empty(self::$database))
        {
            self::$database = new DataBaseHelper('phpshop_modules_intellectmoney_logs');
        }
    }
}