<?php 


namespace App\Controllers;
use App\Models\Artist;

class ArtistController extends BaseController{


    public function __construct(){
        parent::__construct();
    }
    
    public function getByName($name){
        try{
            $artist = new Artist();
            $artist = $artist->getByName($name);
            $this->httpResponse(200, 'Artist fetched', $artist);
        }catch(\PDOException $e){   
            $this->httpResponse(500, 'An error ocurred', []);
        }
    }

}

