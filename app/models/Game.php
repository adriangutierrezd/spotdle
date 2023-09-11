<?php 

namespace App\Models;
use PDOException;

class Game extends BaseModel{

    protected $user_id;
    protected $game_type;
    protected $state;
    protected $success;
    protected $attempts;
    protected $solution;
    protected $shared;
    protected $date;


    public function __construct(){
        parent::__construct('hints');
    }

    public function get($gameId){
        try{
            $sql = "SELECT * FROM games WHERE id = :game_id";
            $query = $this->connection->prepare($sql);
            $query->execute(['game_id' => $gameId]);
            $game = $query->fetch(\PDO::FETCH_ASSOC);
            if(!$game) throw new \PDOException('Game not found', 404);
            return $game;
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function create(){
        try{
            $sql = "INSERT INTO games (user_id, game_type, state, success, attempts, solution, shared, date) VALUES (:user_id, :game_type, :state, :success, :attempts, :solution, :shared, :date)";
            $query = $this->connection->prepare($sql);
            $query->execute([
                'user_id' => $this->user_id,
                'game_type' => $this->game_type,
                'state' => $this->state,
                'success' => $this->success,
                'attempts' => $this->attempts,
                'solution' => $this->solution,
                'shared' => $this->shared,
                'date' => $this->date
            ]);
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function update($gameId){

        try{
            $sql = "UPDATE games SET state = :state, success = :success, attempts = :attempts, solution = :solution, shared = :shared WHERE id = :game_id";
            $query = $this->connection->prepare($sql);
            $query->execute([
                'game_id' => $gameId,
                'state' => $this->state,
                'success' => $this->success,
                'attempts' => $this->attempts,
                'solution' => $this->solution,
                'shared' => $this->shared
            ]);
            return $query->rowCount();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }


    }


}