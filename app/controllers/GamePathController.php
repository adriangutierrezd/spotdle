<?php 

namespace App\Controllers;
use App\Models\GamePath;
use App\Models\Hint;
use App\Models\Game;
use App\Models\Artist;
use PDOException;

class GamePathController extends BaseController{

    const TOP_ARTISTS_URL = 'https://api.spotify.com/v1/me/top/artists';
    const ARTIST_TOP_TRACKS_URL = 'https://api.spotify.com/v1/artists/{id}/top-tracks?country={country}';

    public function __construct(){
        parent::__construct();
    }

    public function get($gameId){

        try{
            $gamePath = new GamePath();
            $gamePath = $gamePath->get($gameId);
        }catch(\PDOException $e){
            $this->httpResponse($e->getCode(), 'An error ocurred', ['error' => $e->getMessage()]);
        }

        $this->httpResponse(200, 'OK', $gamePath);

    }

    public function getWithNumber($gameId, $hintNumber){

        try{
            $gamePath = new GamePath();
            $gamePath = $gamePath->getWithNumber($gameId, $hintNumber);
        }catch(\PDOException $e){
            $this->httpResponse($e->getCode(), 'An error ocurred', ['error' => $e->getMessage()]);
        }

        $this->httpResponse(200, 'OK', $gamePath);

    }

    public function createStep(){
            
        $request = $this->getRequest();
        $gamePathId = NULL;
    
        try{
            $gamePath = new GamePath();
            $gamePath->__set('game_id', $request['game_id']);
            $gamePath->__set('hint_id', $request['hint_id']);
            $gamePath->__set('value', $request['value']);
            $gamePathId = $gamePath->create();
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }
    
        try{
            $newGamePath = new GamePath();
            $this->httpResponse(200, 'OK', $newGamePath->get($gamePathId));
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }

    }

    public function generate(){

        $request = $this->getRequest();

        $gamePath = $this->generateGamePath();
        $keysPath = array_keys($gamePath);

        try{
            $hint = new Hint();
            $gameHints = $hint->get($request['game_type']);
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }


        try{
            $game = new Game();
            $game->__set('user_id', $_SESSION['loggedUser']['user']['id']);
            $game->__set('game_type', $request['game_type']);
            $game->__set('state', 'IN COURSE');
            $game->__set('success', 0);
            $game->__set('attempts', 0);
            $game->__set('solution', $gamePath['artista']);
            $game->__set('shared', 0);
            $game->__set('date', date('Y-m-d'));
            $gameId = $game->create();
        }catch(\PDOException $e){
            $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
        }
        
        $hintCounter = 0;
        foreach($gameHints as $gameHint){

            $value = $gamePath[$keysPath[$hintCounter]];
            if(is_array($value)) $value = implode(',', $value);

            try{
                $gamePathObj = new GamePath();
                $gamePathObj->__set('game_id', $gameId);
                $gamePathObj->__set('hint_id', $gameHint['id']);
                $gamePathObj->__set('value', $value);
                $gamePathId = $gamePathObj->create();
            }catch(\PDOException $e){
                $this->httpResponse(500, 'An error ocurred', ['error' => $e->getMessage()]);
            }

            $hintCounter++;
        }

        $this->httpResponse(201, 'GamePath generated', ['gameId' => $gameId]);



    }

    private function generateGamePath(){
        $topArtists = $this->getTopArtists();
        $artistToFind = $this->getRandomElementFromArray($topArtists);
        $artistTopTracks = $this->getArtistTopTracks($artistToFind['id'], $_SESSION['loggedUser']['user']['country']);
        $artistTopTrack = $this->getRandomElementFromArray($artistTopTracks['tracks']);

        $this->saveArtist($artistToFind);

        $data = [
            'generos' => $artistToFind['genres'],
            'popularity' => $artistToFind['popularity'],
            'album' => $artistTopTrack['album']['name'],
            'cancion' => $artistTopTrack['name'],
        ];

        if(isset($artistTopTrack['preview_url'])){
            $data['preview_url'] = $artistTopTrack['preview_url'];
        }else{
            $data['similar'] = '...';
        }
        $data['artista'] = $artistToFind['name'];
        return $data;
    }

    private function getTopArtists(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => self::TOP_ARTISTS_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$_SESSION['loggedUser']['token']
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response, true);
        $artists = $data['items'];
        return $artists;
    }

    private function getRandomElementFromArray($arr){
        if($arr === null) return null;
        return $arr[array_rand($arr)] ?? null;
    }

    private function getArtistTopTracks($artistId, $country){

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.spotify.com/v1/artists/$artistId/top-tracks?country=$country",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$_SESSION['loggedUser']['token']
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        return json_decode($response, true);
        
    }

    private function saveArtist($artistData){

        $artistExists = new Artist();
        $data = $artistExists->getByExternalId($artistData['id']);
        if(is_array($data)) return;

        $imagePath = $artistData['images'][0]['url'] ?? 'not found pending';

        try{
            $artist = new Artist();
            $artist->__set('name', $artistData['name']);
            $artist->__set('external_id', $artistData['id']);
            $artist->__set('external_uri', $artistData['uri']);
            $artist->__set('image', $imagePath);
            $artist->create();
        }catch(\PDOException $e){
            // LOG error
        }
    }

}