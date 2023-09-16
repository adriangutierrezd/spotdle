<?php 

namespace App\Controllers;
use App\Models\GameLog;
use PDOException;


class GameLogController extends BaseController{


    public function __construct(){
        parent::__construct();
    }


    public function create(){

        $request = $this->getRequest();
    
        try{
            $gameLog = new GameLog();
            $gameLog->__set('game_id', $request['game_id']);
            $gameLog->__set('user_id', $_SESSION['loggedUser']['user']['id']);
            $gameLog->__set('attempt', $request['attempt']);
            $gameLog->__set('hint', $request['hint']);
            $gameLog->__set('response', $request['response']);
            $gameLog->__set('date', date('Y-m-d'));
            $gameLog->create();

            $this->httpResponse(201, 'Succesful request', []);

        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }

    }


}