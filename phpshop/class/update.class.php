<?php

/**
 * Библиотека обновления файлов
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopClass
 */
class PHPShopUpdate {

    var $local_update = true;
    var $_endPoint;
    var $_log;
    var $_timeLimit = 600;
    var $_backup_path = '../../backup/';
    var $_test_file = 'index.php';
    var $base_update_enabled = false;

    public function __construct() {

        include_once('../lib/zip/pclzip.lib.php');
        $this->path = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/';
        set_time_limit($this->_timeLimit);
    }

    /**
     * Обновление БД модулей
     */
    public function updateModules() {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modules']);
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 100));

        if (is_array($data)) {
            foreach ($data as $row) {

                // Информация по модулю из XML
                $info = xml2array("../modules/" . $row['path'] . "/install/module.xml", false, true);

                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base'][$row['path']][$row['path'] . '_system']);
                $PHPShopOrm->mysql_error = false;
                $data_mod = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));

                // Есть обновление
                if (!empty($data_mod['version']) and $info['version'] > $data_mod['version']) {
                    $PHPShopModules->path = $row['path'];
                    $PHPShopModules->getUpdate($data_mod['version']);

                    if ($PHPShopOrm->update(array('version_new' => $info['version'])))
                        $this->log("Обновление базы данных модуля \"" . $info['name'] . "\" до версии " . $info['version'] . " выполнено", 'success');
                }
            }
        }
    }

    /**
     *  Локальный запуск.
     */
    public function islocal() {
        if ($this->local_update or ( $_SERVER["SERVER_ADDR"] == "127.0.0.1" and getenv("COMSPEC")))
            return true;
    }

    /**
     *  Создание папки
     */
    public function mkdir($path) {
        if (@mkdir($this->path . $path))
            return true;
        else
            return false;
    }

    /**
     *  Удаление файла
     */
    public function delete($path = null) {

        if (!$path)
            return false;

        if (@unlink($this->path . $path))
            return true;
        else
            return false;
    }

    /**
     *  Проверка работы с zip 
     */
    public function isReady() {

        if (!$this->islocal()) {

            $archive = new PclZip($this->path . 'test_update.zip');
            $v_list = $archive->add($this->path . $this->_test_file, PCLZIP_OPT_REMOVE_PATH, $this->path);

            if ($v_list == 0) {
                $this->log('Не удаётся создать файл для тестирования Zip обновления, нет прав на изменение папок и файлов. Используйте ручное обновление из <a href="https://docs.phpshop.ru/ustanovka-i-obnovlenie/obnovlenie-phpshop#obnovlenie-v-ruchnom-rezhime-iz-arkhiva" target="_blank" class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-cloud-download"></span> архива</a>.', 'warning', 'remove');
                return false;
            }

            if (!$this->delete('test_update.zip')) {
                $this->log("Не удалось удалить файл для тестирования Zip обновления. Обновление через панель управления не может быть выполнено!", 'danger', 'remove');
                return false;
            } else
                return true;
        } else
            return true;
    }

    /**
     *  Распаковка архива
     */
    public function installFiles($file = 'temp/update.zip', $status = 'обновления', $path = '../../') {

        $archive = new PclZip($this->_backup_path . $file);
        if ($archive->extract(PCLZIP_OPT_PATH, $path, PCLZIP_CB_PRE_EXTRACT, 'preExtractCallBack')) {
            $this->log("Файлы " . $status . " распакованы");
            return true;
        } else {
            $this->log("Не удаётся распаковать файлы " . $status, 'warning', 'remove');
            return false;
        }
    }

    /**
     * Очистка временных файлов /temp/
     */
    public function cleanTemp() {

        $this->delete('backup/temp/config_update.txt');
        $this->delete('backup/temp/upd_conf.txt');
        $this->delete('backup/temp/update.sql');
        $this->delete('backup/temp/restore.sql');
        $this->delete('backup/temp/upload_backup.sql.gz');

        if ($this->delete('backup/temp/update.zip'))
            $this->log("Временные файлы обновления удалены");
        else
            $this->log("Не удаётся удалить временные файлы", 'warning', 'remove');
    }

    /**
     * Обновление БД
     */
    public function installBD() {
        global $PHPShopGUI;

        if (file_exists("dumper/backup/update.sql")) {
            $this->_log .= $PHPShopGUI->setProgress(__('Обновление базы данных...'), 'install-update-bd');
            $this->log("Обновление базы данных выполнено", 'success hide install-update-bd');
            $this->log("Не удается обновить базу данных", 'danger hide install-update-bd-danger');
            return false;
        }
    }

    /**
     * Обновление config.ini
     */
    public function installConfig($config = false) {
        global $PHPShopBase;

        if (!is_array($config))
            $config = parse_ini_file_true($this->_backup_path . "temp/config_update.txt", 1);

        $SysValue = parse_ini_file_true($PHPShopBase->iniPath, 1);

        // Новый config.ini
        if (is_array($config)) {
            foreach ($config as $k => $v) {
                if (is_array($config[$k])) {
                    foreach ($config[$k] as $key => $value) {
                        $SysValue[$k][$key] = $value;
                    }
                }
            }
        }


        $s = null;

        if (is_array($SysValue))
            foreach ($SysValue as $k => $v) {

                $s .= "[$k]\n";
                foreach ($v as $key => $val) {
                    if (!is_array($val))
                        $s .= "$key = \"$val\";\n";
                }

                $s .= "\n";
            }

        if (!empty($s)) {
            if ($f = fopen($this->path . "phpshop/inc/config.ini", "w")) {

                if (!empty($s) and strstr($s, 'phpshop')) {
                    fwrite($f, $s);
                    //$this->log("Конфигурационный файл обновлен");
                }

                fclose($f);
            } else
                $this->log("Не удаётся обновить файл конфигурации phpshop/inc/config.ini. Нет прав на изменение файла.", 'warning', 'remove');
        } else
            $this->log("Не удаётся обновить файл конфигурации phpshop/inc/config.ini. Ошибка парсинга файла.", 'warning', 'remove');
    }

    /**
     *  Создание резервной копии файлов
     */
    public function backupFiles() {

        // Создание папки
        $this->mkdir('backup/backups/' . $GLOBALS['SysValue']['upload']['version']);

        if ($this->base_update_enabled) {
            if (!copy($this->_backup_path . "temp/restore.sql", $this->_backup_path . 'backups/' . $GLOBALS['SysValue']['upload']['version'] . '/restore.sql'))
                $this->log("Не удаётся скопировать бекап базы в backup/backups/" . $GLOBALS['SysValue']['upload']['version'], 'warning', 'remove');
        }

        if ($this->base_update_enabled)
            copy($this->_backup_path . "temp/restore.sql", $this->_backup_path . 'backups/' . $GLOBALS['SysValue']['upload']['version'] . '/restore.sql');


        $archive = new PclZip($this->_backup_path . 'backups/' . $GLOBALS['SysValue']['upload']['version'] . '/files.zip');
        $map = parse_ini_file_true($this->_backup_path . "temp/upd_conf.txt", 1);
        $zip_files = null;

        if (is_array($map)) {
            foreach ($map as $k => $v) {

                if (!empty($v['files'])) {

                    if (strstr($v['files'], ';')) {
                        $files = explode(";", $v['files']);

                        if (is_array($files)) {
                            foreach ($files as $file) {
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $file))
                                    $zip_files .= $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $file . ',';
                            }
                        }
                    }
                    elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $v['files']))
                        $zip_files .= $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/' . $k . '/' . $v['files'] . ',';
                }
            }
        }

        if (!empty($zip_files)) {
            $v_list = $archive->create($zip_files, PCLZIP_OPT_REMOVE_PATH, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/');
            if ($v_list == 0) {
                $this->log("Не удаётся создать бекап файлов перед обновлением. Error : " . $archive->errorInfo(true), 'warning', 'remove');
                return false;
            }

            $this->log("Резервная копия файлов создана");
        }
    }

    /**
     * Анализ карты обновления
     */
    public function map() {

        // Обновление БД присутствует
        if ($this->base_update_enabled) {

            if (!copy($this->_backup_path . "temp/update.sql", "dumper/backup/update.sql")) {
                $this->log("Не удаётся скопировать обновление базы данных update.sql", 'warning', 'remove');
                return false;
            }
        }

        // Анализ файл конфига апдейта
        if (!$this->map = parse_ini_file_true($this->_backup_path . "temp/upd_conf.txt", 1)) {
            $this->log("Не удаётся провести анализ конфига обновлений", 'warning', 'remove');
            return false;
        }
    }

    public function downloadFile($path, $url) {

        $newfname = $path;

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $file = @fopen($url, 'rb', false, stream_context_create($arrContextOptions));

        if ($file) {
            $newf = fopen($newfname, 'wb');
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
            return true;
        }
    }

    /**
     * Загрузка обновления
     */
    public function load() {

        if (!$this->downloadFile($this->path . '/backup/temp/upd_conf.txt', $this->url . 'upd_conf.txt')) {
            $this->log("Ошибка загрузки файла конфигураций обновления", 'warning', 'remove');
            return false;
        }

        if ($this->downloadFile($this->path . '/backup/temp/update.sql', $this->url . 'update.sql')) {
            $this->log("Загружен файл обновления базы данных. Требуется обновление базы данных.");
            $this->downloadFile($this->path . '/backup/temp/restore.sql', $this->url . 'restore.sql');
            $this->base_update_enabled = true;
        }

        if ($this->downloadFile($this->path . '/backup/temp/config_update.txt', $this->url . 'config_update.txt')) {
            $this->log("Загружен конфигурационный файл");
        }

        if ($this->downloadFile($this->path . '/backup/temp/update.zip', $this->url . 'update.zip')) {
            $this->log("Загружен архив файлов для обновления");
        }

        $this->log("Загрузка файла конфигураций обновления выполнена полностью");
    }

    /**
     *  Проверка наличия обновления
     */
    public function checkUpdate() {

        $update_enable = xml2array(UPDATE_PATH, "update", true);

        if ($update_enable) {
            $this->update_status = $update_enable['status'];
            $this->version = $update_enable['name'];
            if ($this->update_status != 'no_update') {

                $this->url = $update_enable['url'];
                $this->content = $update_enable['content']['item'];

                $this->btn_class = 'btn btn-primary btn-sm navbar-btn update-start';
            } elseif ($update_enable['status'] == 'passive') {
                $this->btn_class = 'btn btn-default btn-sm navbar-btn disabled';
            } elseif ($update_enable['status'] == 'no_update') {
                $this->btn_class = 'btn btn-default btn-sm navbar-btn disabled';
            }
        } else
            $this->btn_class = 'hide';
    }

    /**
     * Проверка бекапа БД
     */
    public function checkBD() {

        if (file_exists("dumper/backup/upload_dump.sql.gz")) {
            $this->log('Бекап базы данных выполнен');
        } else
            $this->log('Бекап базы данных не выполнен', 'warning', 'remove');
    }

    public function log($text, $class = 'success', $icon = 'ok') {
        $this->_log .= '<div class="alert alert-' . $class . '" role="alert"><span class="glyphicon glyphicon-' . $icon . '-sign"></span> ' . __($text) . '</div>';
    }

    public function fulllog($text) {
        $this->_fulllog .= $text . '<br>';
    }

    public function getLog() {
        return $this->_log;
    }

}

/**
 * Удаление файлов перед заменой.
 */
function preExtractCallBack($p_event, $p_header) {
    if ($p_header['folder'] != 1 and ! getenv("COMSPEC")) {
        unlink($p_header['filename']);
    }
    return 1;
}
