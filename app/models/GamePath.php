<?php 

namespace App\Models;
use PDOException;


class GamePath extends BaseModel{


    protected $game_id;
    protected $hint_id;
    protected $value;

    public function __construct(){
        parent::__construct();
    }

    public function get($gameId){
        try{

            $sql = "SELECT * FROM game_path gp 
            INNER JOIN games g ON g.id = gp.game_id
            INNER JOIN game_hints gh ON gh.type = g.game_type
            WHERE game_id = :game_id
            GROUP BY gp.id";
            $query = $this->connection->prepare($sql);
            $query->execute(['game_id' => $gameId]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);

        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function getWithNumber($gameId, $hintNumber){
        try{
            $sql = "SELECT gh.text, gp.value, hint_order, gh.id as hint_id FROM game_path gp 
            INNER JOIN games g ON g.id = gp.game_id
            INNER JOIN game_hints gh ON gh.type = g.game_type AND gh.state = 'ACTIVE' AND gh.hint_order = :hint_number AND gh.id = gp.hint_id
            WHERE game_id = :game_id 
            GROUP BY gh.id";
            $query = $this->connection->prepare($sql);
            $query->execute(['game_id' => $gameId, 'hint_number' => $hintNumber]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function create(){
        try{
            $sql = "INSERT INTO game_path (game_id, hint_id, value) VALUES (:game_id, :hint_id, :value)";
            $query = $this->connection->prepare($sql);
            $query->execute([
                'game_id' => $this->game_id,
                'hint_id' => $this->hint_id,
                'value' => $this->value
            ]);
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }


}