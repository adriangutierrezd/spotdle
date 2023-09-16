<?php

namespace libs;
use App\Controllers\BaseController;

class Route{    

    private static $routes = [];

    public static function get($uri, $callback, $middlewares = []){
        self::$routes['GET'][$uri] = $callback;
        self::$routes['GET'][$uri]['middlewares'] = $middlewares;
    }

    public static function post($uri, $callback, $middlewares = []){
        self::$routes['POST'][$uri] = $callback;
        self::$routes['POST'][$uri]['middlewares'] = $middlewares;
    }

    public static function delete($uri, $callback, $middlewares = []){
        self::$routes['DELETE'][$uri] = $callback;
        self::$routes['DELETE'][$uri]['middlewares'] = $middlewares;
    }

    public static function put($uri, $callback, $middlewares = []){
        self::$routes['PUT'][$uri] = $callback;
        self::$routes['PUT'][$uri]['middlewares'] = $middlewares;
    }

    public static function dispatch($uri, $method){

        $uri = self::formatUri($uri);

        $GLOBALS['matches'] = [];
        $GLOBALS['matchedRawRoute'] = null;
        $matchRoutes = array_filter(self::$routes[$method], function($route) use ($uri){
            $rawRoute = $route;
            if(strpos($route, ':')) $route = preg_replace('#:([a-zA-Z0-9-_]+)#', '([a-zA-Z0-9-_]+)', $route);
            $res = preg_match("#^$route$#", $uri, $matches);
            if($res){
                $GLOBALS['matches'] = $matches;
                $GLOBALS['matchedRawRoute'] = $rawRoute;
            } 
            return $res;
        }, ARRAY_FILTER_USE_KEY);

        if(count($matchRoutes) === 0){
            $baseController = new BaseController();
            $baseController->error404();
            die();
        }else{

            foreach(self::$routes[$method][$GLOBALS['matchedRawRoute']]['middlewares'] as $middleware){
                $middleware = new $middleware;
                $returnType = strpos($GLOBALS['matchedRawRoute'], '/api/') === false ? $middleware::RETURN_REDIRECT : $middleware::RETURN_JSON;
                $middleware->handle($returnType);
            }

            $callback = end($matchRoutes);
            $controller = $callback[0];
            $method = $callback[1];
            $controller = new $controller;
            $controller->$method(...array_slice($GLOBALS['matches'], 1));
        }


    }

    private static function formatUri($uri){

        $posMark = strpos($uri, '?');
        if($posMark === false) return $uri;
        return substr($uri, 0, $posMark).'/'.implode('/', array_values($_GET));

    }

}