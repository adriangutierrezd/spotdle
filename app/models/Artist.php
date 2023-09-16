<?php 

namespace App\Models;

use Exception;
use PDOException;

class Artist extends BaseModel{

    protected $name;
    protected $external_id;
    protected $external_uri;
    protected $image;


    public function __construct(){
        parent::__construct();
    }

    public function getByName($name){
        try{
            $sql = "SELECT * FROM artists WHERE name = :name";
            $query = $this->connection->prepare($sql);
            $query->execute(['name' => $name]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }


    public function getByExternalId($externalId){
        try{
            $sql = "SELECT * FROM artists WHERE external_id = :external_id";
            $query = $this->connection->prepare($sql);
            $query->execute(['external_id' => $externalId]);
            return $query->fetch(\PDO::FETCH_ASSOC);
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    public function create(){
        try{
            $sql = "INSERT INTO artists (name, external_id, external_uri, image) VALUES(:name, :external_id, :external_uri, :image)";
            $query = $this->connection->prepare($sql);
            $query->execute([
                'name' => $this->name,
                'external_id' => $this->external_id,
                'external_uri' => $this->external_uri,
                'image' => $this->image
            ]);
            return $this->connection->lastInsertId();
        }catch(\PDOException $e){
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

}