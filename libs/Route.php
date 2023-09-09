<?php

namespace libs;
class Route{    

    private static $routes = [];

    public static function get($uri, $callback){
        self::$routes['GET'][$uri] = $callback;
    }

    public static function post($uri, $callback){
        self::$routes['POST'][$uri] = $callback;
    }

    public static function delete($uri, $callback){
        self::$routes['DELETE'][$uri] = $callback;
    }

    public static function put($uri, $callback){
        self::$routes['PUT'][$uri] = $callback;
    }

    public static function dispatch($uri, $method){

        $uri = self::formatUri($uri);

        $GLOBALS['matches'] = [];
        $matchRoutes = array_filter(self::$routes[$method], function($route) use ($uri){
            if(strpos($route, ':')) $route = preg_replace('#:([a-zA-Z0-9-_]+)#', '([a-zA-Z0-9-_]+)', $route);
            $res = preg_match("#^$route$#", $uri, $matches);
            if($res) $GLOBALS['matches'] = $matches;
            return $res;
        }, ARRAY_FILTER_USE_KEY);

        if(count($matchRoutes) === 0){
            echo "No se ha encontrado la ruta $uri";
            return;
        }else{
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