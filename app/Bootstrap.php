<?php

/**
 * @author TriNT <trint@vng.com.vn>
 */

namespace App;
use Exception;
use App\Controllers\IndexController;



class Bootstrap {

    public static function run() {
        $router = new \AltoRouter();
        foreach (config('route') as $route) {
            $router->map($route[0], config('sub_domain') . $route[1], $route[2]);
        }


        $match = $router->match();

        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        if (is_array($match) && isset($match['target'])) {
            $vars = $match['params'];
            if (is_array($match['target']) && count($match['target']) == 2) {
                [$className, $func] = $match['target'];
                if (! class_exists($className)) {
                    throw new Exception("Route {$uri} defined Class <b>{$className}</b> Not Found");
                }
                $func .= 'Action';
                $controller = new $className;
                if (method_exists($className, $func)) {
                    session_start();
                    $controller = new $className;
                    if (!($controller instanceof IndexController
                    )) {
                        if (!session('account')) {
                            //$_SESSION['login_uri'] = $uri;
                            redirect(url(config('url.base')));
                        }
                    }

                    call_user_func_array([$controller, $func], $vars);
                } else {
                    throw new Exception("Route {$uri} defined class <b>{$className}</b> does not have a method \"<b>{$func}</b>\"");
                }
            } elseif (is_callable($match['target'])) {
                call_user_func_array($match['target'], $vars);
            }
        } else {
            $title = 'Not Found!';
            render_error(404, $title);
        }
    }

}
