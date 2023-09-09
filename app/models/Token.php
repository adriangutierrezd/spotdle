<?php 

namespace App\Models;

use Exception;
use PDOException;

class Token extends BaseModel{

    private $token;
    private $userId;

    public function __construct($token, $userId){
        parent::__construct();
        $this->token = $token;
        $this->userId = $userId;
    }

    public function save(){
        try{
            $query = "INSERT INTO tokens (token, user_id, date) VALUES (:token, :user_id, :date)";
            $result = $this->connection->prepare($query);
            $result->execute([
                'token' => $this->token,
                'user_id' => $this->userId,
                'date' => date('Y-m-d')
            ]);
            if($result->rowCount() < 1) throw new \Exception('An error ocurred while saving the token');
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), 500);
        }
    }


}