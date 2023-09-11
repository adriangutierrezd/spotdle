<?php 

namespace App\Controllers;
use App\Models\GamePath;
use PDOException;

class GamePathController extends BaseController{

    // https://api.spotify.com/v1/artists/00FQb4jTyendYWaN8pK0wa/top-tracks?country=ES
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
        // TODO

        // 1. Obtenemos el top de artistas del usuario

        $topArtists = $this->getTopArtists();
        $artistToFind = $this->getRandomElementFromArray($topArtists);
        $artistTopTracks = $this->getArtistTopTracks($artistToFind['id'], $_SESSION['loggedUser']['user']['country']);
        $artistTopTrack = $this->getRandomElementFromArray($artistTopTracks['tracks']);

        $data = [
            'generos' => $artistToFind['genres'],
            'seguidores' => $artistToFind['followers']['total'],
            'album' => $artistTopTrack['album']['name'],
            'cancion' => $artistTopTrack['name'],
            'artista similar' => '',
            'artista' => $artistToFind['name']
        ];

        echo json_encode($data);
        die();

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

}


