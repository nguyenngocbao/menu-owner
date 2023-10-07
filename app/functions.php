<?php

/**
 * @author TriNT <trint@vng.com.vn>
 */
if (config('debug')) {
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', 'on');
    ini_set('error_reporting', E_ALL ^ E_NOTICE);
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
set_error_handler('handle_error');
set_exception_handler('handle_exception');
register_shutdown_function('shutdown');

magic_quotes();

function set_log(string $msg, string $type = 'log') {
    $file = config('path_log');
    $date_format = 'H:i:s';
    $message_format = '%date% | %type% | %message%';

    $dir = dirname($file);
    is_dir($dir) or mkdir($dir, 0777, true);
    if (in_array(strtolower($type), ['log', 'debug', 'error', 'warning', 'critical', 'custom', 'alert', 'notice', 'info', 'emergency', 'special', 'custom'])) {
        $msg = str_replace(
                        ['%date%', '%type%', '%message%'],
                        [date($date_format, time()), strtoupper($type), $msg],
                        $message_format
                ) . PHP_EOL;

        return file_put_contents($file, $msg, FILE_APPEND | LOCK_EX);
    }
    return false;
}

function handle_error(int $errno, string $errstr, string $errfile, int $errline) {
    if ($errno & error_reporting()) {
        throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
    }
}

function handle_exception($e) {
    switch ($e->getCode()) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $type = 'notice';
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $type = 'warning';
            break;
        default:
            $type = 'error';
    }
    set_log($e->getMessage(), $type);

    if (config('debug')) {
        $whoops = new Whoops\Run;
        if (is_ajax()) {
            $whoops->pushHandler(new Whoops\Handler\JsonResponseHandler);
        } else {
            $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
        }

        $whoops->handleException($e);
        $whoops->register();
    } else {
        $title = $e->getMessage();
        render_error(503, $title);
    }
}

function data_json() {
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    return $data;
}

function render_error(int $code, string $title) {
    if (is_ajax()) {
        http_response_code($code);
        echo_json(['err' => 1, 'msg' => $title]);
    }
    //http_response_code($code);
    //render_page('_error', ['title' => $title, 'code' => $code]);
    redirect(config('url.err'));
}

function shutdown() {
    $isFatal = function ($errno) {
        return in_array($errno, [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING]);
    };
    if (!is_null($error = error_get_last()) && $isFatal($error['type'])) {
        throw new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']);
    }
}

function render_template(string $template, array $data = []) {
    extract((array) $data, EXTR_SKIP);
    $__filepath__ = config('path_view') . $template . '.phtml';
    if (!is_file($__filepath__)) {
        die("View file does not exist：{$__filepath__}");
    }
    ob_start('ob_gzhandler');
    include $__filepath__;

    return ob_get_clean();
}

function render_page(string $name, $data = null) {
    $_data = is_callable($data) ? $data() : $data;
    $data = (array) $_data;
    $data['content'] = render_template($name, $data);

    echo render_template('_layouts', $data);
    exit();
}
function render_page_layout(string $name, $data = null,$layout = '_layouts') {
    $_data = is_callable($data) ? $data() : $data;
    $data = (array) $_data;
    $data['content'] = render_template($name, $data);

    echo render_template($layout, $data);
    exit();
}

function render_block(string $name, $data = []) {
    $data = is_callable($data) ? $data() : $data;
    return render_template("_blocks/{$name}", $data);
}

function echo_json($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit();
}

function config(string $name, $default = null) {
    static $config = [];
    $path = APP_PATH . "config/{$name}.php";
    if (file_exists($path)) {
        if (!isset($config[$name])) {
            $config[$name] = include $path;
        }
        return $config[$name];
    }

    $data = include APP_PATH . 'config/app.php';

    return _get_data($data, $name, $default);
}

function _get_data($data, string $name, $default = null) {
    foreach ($name ? explode('.', $name) : [] as $key) {
        if (!isset($data[$key])) {
            return $default;
        }
        $data = $data[$key];
    }

    return $data;
}

function magic_quotes() {
    $_GET = add_magic_quotes($_GET);
    $_POST = add_magic_quotes($_POST);
    $_COOKIE = add_magic_quotes($_COOKIE);
    $_SERVER = add_magic_quotes($_SERVER);
    $_REQUEST = array_merge($_GET, $_POST);
}

function add_magic_quotes($array) {
    foreach ((array) $array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = add_magic_quotes($v);
        } elseif (is_string($v)) {
            $array[$k] = addslashes($v);
        } else {
            continue;
        }
    }

    return $array;
}

function request(string $name = null, $default = null) {
    if ($name) {
        return _get_data($_REQUEST, $name, $default);
    }
    return $_REQUEST;
}

function post(string $name = null, $default = null) {
    if ($name) {
        return _get_data($_POST, $name, $default);
    }
    return $_POST;
}

function get(string $name = null, $default = null) {
    if ($name) {
        return _get_data($_GET, $name, $default);
    }
    return $_GET;
}

function session(string $name = null, $default = null) {
    if ($name) {
        return _get_data($_SESSION, $name, $default);
    }
    return $_SESSION;
}

function files(string $name = null, $default = null) {
    if ($name) {
        return $_FILES[$name] ?? $default;
    }
    return $_FILES;
}

/**
 * @static $cache
 * @return \DivineOmega\DOFileCache\DOFileCache
 */
function cache() {
    static $cache = null;

    if (is_null($cache)) {
        $cache = new DivineOmega\DOFileCache\DOFileCache();
    }

    return $cache;
}

function redirect(string $link) {
    header("Location: {$link}");
    exit();
}

function is_ajax() {
    return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
}

function is_mobile(): bool {
    return (bool) preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT']);
}

function call_api(string $method, $data, $timeout = 30) {
    $params = $data;

    $url  = config('api.url') . $method;
    
    $curl = curl_init($url);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    
    $curl_response = curl_exec($curl);
    $info          = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    is_string($curl_response) && $curl_response = json_decode($curl_response, true);

    return $info == 200 ? $curl_response : false;
}

function url(string $u = '') {
    return config('sub_domain').'/'.trim($u, '/');
}
