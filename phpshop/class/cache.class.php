<?php

/**
 * Кэширование
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopClass
 */
class PHPShopCache {

    protected $enabled = false;

    public function __construct($cache_key) {
        global $PHPShopSystem;

        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bot']);

        // Блокировка
        $this->block_ip = explode(",", $PHPShopSystem->getSerilizeParam('admoption.block_ip'));
        $this->block_bot = $PHPShopSystem->getSerilizeParam('admoption.block_bot');

        $this->cache_key = $cache_key;
        $this->type = $PHPShopSystem->getSerilizeParam('admoption.cache');
        $this->time = $PHPShopSystem->getSerilizeParam('admoption.cache_time');
        $this->seo = $PHPShopSystem->getSerilizeParam('admoption.cache_seo');

        $this->level = $PHPShopSystem->getSerilizeParam('admoption.cache_gzip');
        if (empty($this->level))
            $this->level = 1;

        $this->server = $PHPShopSystem->getSerilizeParam('admoption.memcached_server');
        $this->port = $PHPShopSystem->getSerilizeParam('admoption.memcached_port');
        $this->hsts = $PHPShopSystem->getSerilizeParam('admoption.hstst');
        $this->debug = $PHPShopSystem->getSerilizeParam('admoption.cache_debug');
        $this->mod = $PHPShopSystem->getSerilizeParam('admoption.cache_mod');
        $this->compres = $PHPShopSystem->getSerilizeParam('admoption.cache_compres');

        // Bot
        if ($this->checkBot()) {
            $this->mod = $this->seo;

            // UTF
            if ($PHPShopSystem->getSerilizeParam('admoption.cache_seo_utf') == 1 and $this->mod == 1)
                $this->seo_utf = true;
            else
                $this->seo_utf = false;
        }
        // User
        elseif ($this->mod > 0) {
            // Переход на частичный кэш
            if ((is_array($_SESSION['cart']) and count($_SESSION['cart']) > 0) or ! empty($_SESSION['UsersId']) or ( is_array($_SESSION['wishlist']) and count($_SESSION['wishlist']) > 0) or ( is_array($_SESSION['compare']) and count($_SESSION['compare']) > 0)) {
                $this->mod = 2;
            }
        }

        // Memcached
        if ($this->type == 1 and ( class_exists('Memcached') or class_exists('Memcache'))) {

            if (class_exists('Memcached'))
                $this->cache = new Memcached();
            else if (class_exists('Memcache'))
                $this->cache = new Memcache();

            $this->cache->addServer($this->server, $this->port);
            $this->enabled = true;
            $this->name = 'Memcached';
        }
        // File
        else if ($this->type == 2) {
            $this->cache = new PHPShopFileCache($this->time);
            $this->enabled = true;
            $this->name = 'Filecache';
        }
        // MySQL
        else if ($this->type == 3) {
            $this->cache = new PHPShopMysqlCache();
            $this->enabled = true;
            $this->name = 'Mysqlcache';
        }

        // Удаление html кеша
        if (!empty($_GET['cache']) and $_GET['cache'] == 'clean' and ! empty($this->cache_key) and ! empty($_SESSION['idPHPSHOP'])) {
            $this->delete($this->cache_key);
        }
    }

    public function valid_element($name) {
        if (!in_array($name, ['usersDisp', 'specMain', 'specMainIcon', 'captcha', 'wishlist', 'pageCss', 'skin', 'cloud', 'topBrands']))
            return true;
    }

    public function debug() {
        global $start_time;

        // Расход памяти
        if (function_exists('memory_get_usage')) {
            $mem = memory_get_usage();
            $_MEM = round($mem / 1024, 2) . " Kb";
        } else
            $_MEM = null;

        // Выключаем таймер
        $time = explode(' ', microtime());
        $seconds = $time[1] + $time[0] - $start_time;

        if ($this->debug and $this->valid_url())
            return PHP_EOL . '<!-- ' . $this->name . ' ~ ' . (int) $GLOBALS['SysValue']['sql']['num'] . ' SQL , ' . substr($seconds, 0, 6) . ' sec, ' . $_MEM . ', Key ' . $this->cache_key . ' -->';
    }

    public function init() {
        ob_start();
        ob_implicit_flush(0);
    }

    public function get($key) {
        if ($this->enabled)
            return $this->cache->get($key);
    }

    public function set($key, $val, $time = false, $name = false) {
        if ($this->enabled) {

            if (class_exists('Memcached') and $this->type == 1)
                $this->cache->set($key, $val, $this->time * 60 * 60 * 24);
            elseif (class_exists('Memcache') and $this->type == 1)
                $this->cache->set($key, $val, MEMCACHE_COMPRESSED, $this->time * 60 * 60 * 24);
            else
                $this->cache->set($key, $val, $this->time * 60 * 60 * 24, $name);
        }
    }

    public function delete($key) {
        if ($this->enabled)
            return $this->cache->delete($key);
    }

    public function flush() {
        if ($this->enabled) {
            $this->cache->flush();
        }
    }

    private function header() {
        @header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
        @header("Pragma: no-cache");

        if (!empty($this->hsts))
            @header("Strict-Transport-Security:max-age=63072000");

        @header("Last-Modified: " . gmdate("D, d M Y H:i:s", (time() - $this->time * 60 * 60 * 24)) . " GMT");
        @header("X-Powered-By: PHPShop");

        if ($this->seo_utf)
            @header('Content-type: text/html; charset=utf-8');
    }

    private function utf($content) {
        return PHPShopString::win_utf8(str_replace('windows-1251', 'utf-8', $content), false);
    }

    public function display($cache_key) {
        if ($this->enabled) {
            $content = $this->get($cache_key, $this->level);
            if (!empty($content)) {
                $this->header();
                $content = gzuncompress($content);

                if ($this->seo_utf)
                    $content = $this->utf($content);

                return $content;
            }
        }
    }

    private function encoding() {
        if (headers_sent() || connection_aborted()) {
            return false;
        }
        if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'x-gzip') !== false)
            return "x-gzip";
        if (strpos($_SERVER["HTTP_ACCEPT_ENCODING"], 'gzip') !== false)
            return "gzip";
        return false;
    }

    public function compression($content) {

        if (!empty($this->compres)) {

            // Комментарии
            $content = preg_replace('#<!--.*-->#', '', $content);
            $content = preg_replace('#// .*#', '', $content);

            // Переводы строк
            $content = preg_replace('([\r\n\t])', '', $content);

            // 2 и более пробелов
            $content = preg_replace('/ {2,}/', ' ', $content);
        }

        return $content;
    }

    public function gzip($cache) {

        $encoding = $this->encoding();
        if ($encoding) {

            $Contents = ob_get_contents();

            if ($cache)
                $Contents = $this->compression($Contents);

            ob_end_clean();
            header("Content-Encoding: $encoding");
            print "\x1f\x8b\x08\x00\x00\x00\x00\x00";
            $Size = strlen($Contents);
            $Crc = crc32($Contents);
            $Contents = gzcompress($Contents, $this->level);

            if ($cache and $this->valid_url() and $this->mod == 1)
                $this->set($this->cache_key, $Contents);

            $Contents = substr($Contents, 0, strlen($Contents) - 4);
            print $Contents;
            print pack('V', $Crc);
            print pack('V', $Size);
            exit;
        } else {
            ob_end_flush();
            exit;
        }
    }

    public function valid_url() {
        if ((count($_POST) == 0 and empty($_COOKIE['UserChecked']) and ! in_array(parse_url($_SERVER['REQUEST_URI'])['path'], ['/search/'])) or $this->mod == 2) {
            return true;
        }
    }

    function checkService() {
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.service_enabled', 1)) {

            $ip = explode(",", $this->PHPShopSystem->getSerilizeParam('admoption.service_ip'));
            if (is_array($ip) and in_array(trim($_SERVER['REMOTE_ADDR']), $ip))
                return;
            else {

                $title = $this->PHPShopSystem->getSerilizeParam('admoption.service_title');
                $message = $this->PHPShopSystem->getSerilizeParam('admoption.service_content');

                if (empty($title))
                    $title = '503 Service Temporarily Unavailable';

                if (empty($message))
                    $message = 'Website is under construction';


                PHPShopParser::set('message', $message);
                PHPShopParser::set('title', $title);
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                exit(PHPShopParser::file($_SERVER['DOCUMENT_ROOT'] . '/phpshop/lib/templates/error/service.tpl', false, true, true));
            }
        }
    }

    public function checkBlockBot($name, $userAgent) {

        if ($this->block_bot > 0) {

            $data = $this->PHPShopOrm->getOne(['*'], ['name' => '="' . $name . '"']);
            if (is_array($data)) {
                if ($data['enabled'] == 0)
                    return true;
            } elseif ($this->block_bot == 2) {
                $this->PHPShopOrm->insert(['name_new' => $name, 'description_new' => $userAgent, 'date_new' => time(), 'enabled_new' => '1']);
            } elseif ($this->block_bot == 1) {
                return true;
            }
        }
    }

    public function checkBlockIP() {

        if (is_array($this->block_ip) and in_array(trim($_SERVER['REMOTE_ADDR']), $this->block_ip)) {
            header("HTTP/1.0 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }
    }

    public function checkBot() {

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            preg_match('/(\S+bot)([\/ ]([0-9.])|(;)+)/i', $userAgent, $bot);

            if (!empty($bot[1])) {
                if ($this->checkBlockBot($bot[1], $userAgent)) {
                    header('HTTP/1.0 403 Forbidden', true, 403);
                    header("Status: 403 Forbidden");
                    exit;
                }
            }
        } else
            $userAgent = null;

        $botPatterns = [
            '/bot/i',
            '/crawler/i',
            '/spider/i',
            '/curl/i',
            '/facebookexternalhit/i',
            '/twitterbot/i',
            '/pingdom/i',
            '/google/i',
            '/yahoo/i',
            '/bing/i',
            '/lighthouse/i',
            '/whatsapp/i',
            '/compatible/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        return false;
    }

}

/**
 *  Кеш в файловой системе
 */
class PHPShopFileCache {

    var $time = 0;
    var $check_time = true;

    public function __construct($time) {
        $this->dir = "/UserFiles/Cache/html/";
        $this->time = $time;
    }

    public function get($key) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $this->dir . $key;
        if (file_exists($file)) {

            // Проверка даты
            if ($this->check_time) {
                if ((filemtime($file) + $this->time * 60 * 60 * 24) > time())
                    return file_get_contents($file);
            } else
                return file_get_contents($file);
        }
    }

    public function set($key, $val, $time = false, $name = false) {

        if (is_writable($_SERVER['DOCUMENT_ROOT'] . $this->dir)) {
            $file = $_SERVER['DOCUMENT_ROOT'] . $this->dir . $key;
            file_put_contents($file, $val);
        }
    }

    public function delete($key) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $this->dir . $key;
        if (file_exists($file))
            unlink($file);
    }

    public function flush() {
        $files = glob('../..' . $this->dir . "/*");
        if (count($files) > 0) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }

}

/**
 *  Кеш в БД
 */
class PHPShopMysqlCache {

    public function __construct() {
        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['cache']);
    }

    public function delete($key) {
        $this->PHPShopOrm->delete(['uid' => '="' . $key . '"']);
    }

    public function flush() {
        $this->PHPShopOrm->query("TRUNCATE " . $GLOBALS['SysValue']['base']['cache']);
    }

    public function get($key) {
        $cache = $this->PHPShopOrm->getOne(['*'], ['uid' => '="' . $key . '"']);
        $time = time();
        if (!empty($cache)) {
            if ($cache['time'] > $time)
                return base64_decode($cache['content']);
            else
                $this->delete($key);
        }
    }

    public function set($key, $val, $time, $name = false) {
        $content = $this->get($key);
        $time += time();

        if (empty($content)) {

            $path = $_SERVER['REQUEST_URI'];
            if (!empty($_POST))
                $path .= http_build_query($_POST);

            if (!empty($name))
                $path = $name;

            $this->PHPShopOrm->insert(['uid_new' => $key, 'content_new' => base64_encode($val), 'time_new' => $time, 'path_new' => $path, 'size_new' => strlen($val) / 100]);
        } else
            $this->PHPShopOrm->update(['content_new' => base64_encode($val), 'time_new' => $time, 'size_new' => strlen($val) / 100], ['uid' => '="' . $key . '"',]);
    }

}
