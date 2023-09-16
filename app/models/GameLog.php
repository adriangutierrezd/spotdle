<?php

namespace App\Models;
use PDOException;

class GameLog extends BaseModel{

    protected $user_id;
    protected $game_id;
    protected $attempt;
    protected $hint;
    protected $response;
    protected $date;

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        try{
            $sql = "INSERT INTO game_log (user_id, game_id, attempt, hint, response, date) VALUES (:user_id, :game_id, :attempt, :hint, :response, :date)";
            $query = $this->connection->prepare($sql);
            $query->execute([
                'user_id' => $this->user_id,
                'game_id' => $this->game_id,
                'attempt' => $this->attempt,
                'hint' => $this->hint,
                'response' => $this->response,
                'date' => $this->date
            ]);
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage());
        }
    }

}