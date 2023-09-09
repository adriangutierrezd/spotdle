<?php 


namespace App\Controllers;
use App\Models\User;

class UserController extends BaseController{


    public function __construct(){
        parent::__construct();
    }


    public function logUser($data){

        $token = $data['access_token'];
        $userData = $this->getUserData($token);

        $user = new User($userData['id'], $userData['display_name'], $userData['country']);

        $userLogged = $user->get();

        if(!$userLogged){
            $user->save();
            $userLogged = $user->get();
        }

        return $userLogged;
    }


    private function getUserData($token){
        $url = "https://api.spotify.com/v1/me";

        $authorizationHeader = "Authorization: Bearer $token";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($authorizationHeader)); 
        curl_setopt($ch, CURLOPT_HTTPGET, true); 

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error en la solicitud cURL: ' . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true);
    }


}

