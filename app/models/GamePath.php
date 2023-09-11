<?php 

namespace App\Models;
use PDOException;

/*
CREATE TABLE `spotdle`.`game_path`(
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,  -- Identificador único de la fila
    `game_id` INT UNSIGNED NOT NULL,  -- Identificador del juego relacionado
    `hint_id` INT UNSIGNED NOT NULL,  -- Identificador de la pista relacionada
    `value` VARCHAR(255) NOT NULL,  -- Valor asociado al camino del juego
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- Marca de tiempo de creación
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,  -- Marca de tiempo de actualización
    PRIMARY KEY(`id`),  -- Clave primaria de la tabla
    UNIQUE KEY `game_hint_uq` (`game_id`, `hint_id`),  -- Restricción de unicidad para pares de juego y pista
    INDEX `idx_game_id_hint_id` (`game_id`, `hint_id`),  -- Índice para búsqueda eficiente por juego y pista
    INDEX `idx_game_id` (`game_id`)  -- Índice para búsqueda eficiente por juego
);
*/

class GamePath extends BaseModel{


    protected $game_id;
    protected $hint_id;
    protected $value;

    public function __construct(){
        parent::__construct();
    }

    public function get($gameId){
        try{

            $sql = "SELECT * FROM game_path WHERE game_id = :game_id";
            $query = $this->connection->prepare($sql);
            $query->execute(['game_id' => $gameId]);
            return $query->fetchAll(\PDO::FETCH_ASSOC);

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