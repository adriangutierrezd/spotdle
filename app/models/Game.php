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
        parent::__construct();
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

    public function update($gameId, $updateFields) {
        try {
            $updateSql = "UPDATE games SET ";
            $updateData = [];
    
            foreach ($updateFields as $field => $value) {
                $updateSql .= "$field = :$field, ";
                $updateData[$field] = $value;
            }
    
            // Eliminar la última coma y espacio en blanco del SQL
            $updateSql = rtrim($updateSql, ', ');
    
            $updateSql .= " WHERE id = :game_id";
            $updateData['game_id'] = $gameId;
    
            $sql = $updateSql;
            $query = $this->connection->prepare($sql);
            $query->execute($updateData);
    
            return $query->rowCount();
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }
    


}