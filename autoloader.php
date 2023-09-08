<?php

spl_autoload_register(function($class){

    $route = "../".str_replace("\\", "/", $class).".php";
    if(file_exists($route)){
        require_once $route;
    }else{
        echo "No se ha encontrado la clase $class";
    }
});