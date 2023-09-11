<?php 

namespace App\Controllers;
use App\Models\Game;

class GameController extends BaseController{

    public function __construct(){
        parent::__construct();
    }

    public function get($gameId){

        try{
            $game = new Game();
            $game = $game->get($gameId);
        }catch(\PDOException $e){
            $this->httpResponse($e->getCode(), 'An error ocurred', ['error' => $e->getMessage()]);
        }

        $this->httpResponse(200, 'OK', ['game' => $game]);

    }

    public function create(){

        $request = $this->getRequest();
        $game_id = NULL;

        try{
            $game = new Game();
            $game->__set('user_id', $_SESSION['loggedUser']['user']['id']);
            $game->__set('game_type', $request['game_type']);
            $game->__set('state', 'IN COURSE');
            $game->__set('success', 0);
            $game->__set('attempts', 0);
            $game->__set('solution', NULL);
            $game->__set('shared', 0);
            $game->__set('date', date('Y-m-d'));
            $game_id = $game->create();
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }

        try{
            $newGame = new Game();
            $this->httpResponse(200, 'OK', $newGame->get($game_id));
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }

    }

    public function update($gameId){

        $request = $this->getRequest();

        try{
            $game = new Game();
            $game->__set('state', $request['state']);
            $game->__set('success', $request['success']);
            $game->__set('attempts', $request['attempts']);
            $game->__set('solution', $request['solution']);
            $game->__set('shared', $request['shared']);
            $game->update($gameId);
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }

        $this->httpResponse(200, 'OK', ['game_id' => $gameId]);

    }


}


