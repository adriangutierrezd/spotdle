<?php 

namespace App\Models;

use PDO;
use PDOException;
use Dotenv;


class BaseModel{

    protected $connection;
    private $dotenv;

    public function __construct(){
        $this->dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
        $this->dotenv->load();
        $this->connection = $this->connection();
    }

    public function connection(){
        $conexion = null;
        try{
            $conexion = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_DATABASE']."", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
        return $conexion;
    }

}