<?php 

namespace App\Models;
use PDOException;

class Hint extends BaseModel{

    public function __construct(){
        parent::__construct();
    }


    public function get($game_type){
        try{
            $sql = "SELECT * FROM game_hints WHERE type = :game_type AND state = :state ORDER BY hint_order ASC";
            $query = $this->connection->prepare($sql);
            $query->execute(['game_type' => $game_type, 'state' => 'ACTIVE']);
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }


}