<?php

/**
 * Библиотека работы с массивами данных
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopClass
 */
class PHPShopArray {

    /**
     * @var string имя БД
     */
    var $objBase;

    /**
     * Массив условий выборки
     * @var array 
     */
    var $objSQL = false;

    /**
     * Лимит 
     * @var int 
     */
    var $limit = 10000;

    /**
     * @var bool режим отладки
     */
    var $debug = false;
    var $cache = true;
    var $ignor_select = false;
    var $objArray = null;

    /**
     * вывод ошибок mysql
     * @var bool 
     */
    var $mysql_error = false;

    /**
     * @var int многомерный [1] одномерный массив [2] или [3] простой массив
     */
    var $objType = 1;

    /**
     * Дополнительные аргументы
     * @var array 
     */
    var $args = array();

    /**
     * Сортировка выборки
     * @var array
     */
    var $order = array();

    /**
     * Память
     * @var string имя ячейки памяти
     */
    var $memory = null;

    /**
     * Режим игнорирования полей в аргументах [NEW]
     * @var bool 
     */
    var $ignor = false;

    function __construct() {

        // Лимит из конфига
        if (!empty($GLOBALS['SysValue']['my']['array_limit']))
            $this->limit = $GLOBALS['SysValue']['my']['array_limit'];

        $this->objArg = func_get_args();

        // Дополнительные аргументы
        if (is_array($this->args) and count($this->args) > 0)
            $this->objArg = array_merge($this->objArg, $this->args);

        $this->objArgNum = func_num_args();
        $this->setArray();

        if ($this->memory)
            $_SESSION['Memory'][$this->memory] = $this->objArray;
    }

    /**
     * Создание массива выбранных элементов из БД
     * @param mixed $param имя параметра через запятую
     */
    function setArray() {

        if ($this->objArgNum > 0) {
            foreach ($this->objArg as $v) {
                $select[] = $v;
            }
        } else
            $select[] = "*";

        if ($this->ignor) {
            $this->ignor_select = $select;
            unset($select);
            $select[] = "*";
        }

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->mysql_error = $this->mysql_error;
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $this->cache;
        $data = $PHPShopOrm->select($select, $this->objSQL, $this->order, array('limit' => $this->limit));

        if ($select[0] == "*") {

            if (is_array($data)) {

                if (count($data) == 1)
                    $data_true[] = $data;
                else
                    $data_true = $data;

                foreach ($data_true as $val)
                    $array[$val['id']] = $val;
            }
        } else if (is_array($data))
            foreach ($data as $k => $objRow) {
                switch ($this->objType) {
                    case(1):
                        foreach ($this->objArg as $val)
                            $_array[$val] = $objRow[$val];
                        $array[$objRow[$this->objArg[0]]] = $_array;
                        break;

                    case(2):
                        $array[$objRow[$this->objArg[0]]] = $objRow[$this->objArg[1]];
                        break;

                    case(3):
                        foreach ($this->objArg as $val)
                            $array[$val] = $objRow[$val];
                        break;
                }
            }

        // Игнорирование полей   
        if (is_array($this->ignor_select) and count($this->ignor_select) > 0) {
            foreach ($array as $k => $v)
                foreach ($v as $key => $val)
                    if (in_array($key, $this->ignor_select)) {
                        unset($array[$k][$key]);
                    }
        }

        if (!empty($array))
            $this->objArray = $array;
    }

    /**
     * Выдача общего массива
     * @return array
     */
    function getArray() {
        return $this->objArray;
    }

    /**
     * Добавить параметр
     * @param string $param имя параметра
     * @param mixed $value значение параметра
     */
    function setParam($param, $value) {
        if (strstr($param, '.')) {
            $param = explode(".", $param);
            $this->objArray[$param[0]][$param[1]] = $value;
        } else
            $this->objArray[$param] = $value;
    }

    /**
     * Выдача элемента массива
     * @param string $param имя параметра
     * @return string
     */
    function getParam($param) {
        if (strstr($param, '.')) {
            $param = explode(".", $param);
            if (isset($this->objArray[$param[0]][$param[1]]))
                return $this->objArray[$param[0]][$param[1]];
        } else
            return $this->objArray[$param];
    }

    /**
     * Преобразование в ключевой массив по первому параметру при указании метода
     * <code>
     * // example:
     * $PHPShopDeliveryArray = new PHPShopDeliveryArray();
     * $PHPShopDeliveryArray -> getKey('PID.name',true);
     * </code>
     * @param string $param имя параметра
     * @param bool $type при совпадении ключей создается многомерный массив, иначе берется FIFO
     * @return array
     */
    function getKey($param, $type = false) {
        $param = explode(".", $param);
        $array = $this->objArray;
        $newArray=null;
        if (is_array($array))
            foreach ($array as $val)
                foreach ($val as $key => $v)
                    if ($key == $param[1]) {
                        if (empty($type)) {
                            $newArray[$val[$param[0]]] = $v;
                        } else {
                            if (empty($newArray[$val[$param[0]]]))
                                $newArray[$val[$param[0]]][] = $v;
                            else
                                $newArray[$val[$param[0]]][] = $v;
                        }
                    }
        return $newArray;
    }

    /**
     * Подсчет элементов  в массиве
     * @return int
     */
    function getNum() {
        return count($this->objArray);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

}

?>