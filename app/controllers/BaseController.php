<?php 

namespace App\Controllers;

use Dotenv;

class BaseController{


    protected $dotenv;
    public function __construct(){
        $this->dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
        $this->dotenv->load();
    }

    public function view($name){

        $name = str_replace('.', '/', $name);
        
        if(file_exists("../resources/views/$name.php")){
            ob_start();
            include  "../resources/views/{$name}.php";
            print(ob_get_clean());
        }else{
            echo "No se ha encontrado la vista $name";
        }

    }


    public function getRequest(){
        header('Content-Type: application/json');
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        return $decoded;
    }


    public function httpResponse($code, $message, $data){
        http_response_code($code);
        $response = [
            'status' => $code,
            'message' => $message,
            'data' => $data
        ];
        echo json_encode($response);
        die();
    }

}