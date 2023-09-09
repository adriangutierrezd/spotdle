<?php 

namespace App\Models;

use Exception;
use PDOException;

class User extends BaseModel{

    private $id;
    private $username;
    private $display_name;
    private $country;


    public function __construct($username, $display_name, $country){
        parent::__construct();

        $this->username = $username;
        $this->display_name = $display_name;
        $this->country = $country;
    }

    public function save(){
        try{
            $sql = "INSERT INTO users (username, display_name, country) VALUES (:username, :display_name, :country)";
            $query = $this->connection->prepare($sql);

            $query->execute([
                'username' => $this->username,
                'display_name' => $this->display_name,
                'country' => $this->country
            ]);

            if($query->rowCount() < 1) throw new \Exception('Error creando el usuario');
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }
    }

    public function get(){
        try{
            $sql = "SELECT * FROM users WHERE username = :username";
            $query = $this->connection->prepare($sql);
            $query->execute(['username' => $this->username]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }


}