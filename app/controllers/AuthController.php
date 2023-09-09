<?php 

namespace App\Controllers;

class AuthController extends BaseController{

    function __construct(){
        parent::__construct();
    }

    public function login(){
        $state = $this->generateRandomString(16);
        $scope = 'user-read-private user-read-email user-top-read';

        $query = array(
            'response_type' => 'code',
            'client_id' => $_ENV['SP_CLIENT_ID'],
            'scope' => $scope,
            'redirect_uri' => $_ENV['SP_REDIRECT_URI'],
            'state' => $state
        );

        $url = $_ENV['SP_AUTHORIZE_URI'] . http_build_query($query);

        header('Location: ' . $url);
    }

    public function callback($code, $status){

        $authHeader = 'Basic ' . base64_encode($_ENV['SP_CLIENT_ID'] . ':' . $_ENV['SP_CLIENT_SECRET']);
        
        $data = [
            'code' => $code,
            'redirect_uri' => $_ENV['SP_REDIRECT_URI'],
            'grant_type' => 'authorization_code',
        ];
        
        $ch = curl_init($_ENV['SP_TOKEN_URI']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $authHeader]);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            echo 'Error: ' . curl_error($ch);
        } else {
            echo $response;
        }
        
        curl_close($ch);
    }

    private function generateRandomString($length) {
        $text = '';
        $possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    
        for ($i = 0; $i < $length; $i++) {
        $text .= $possible[random_int(0, strlen($possible) - 1)];
        }
    
        return $text;
    }

    private function storeToken(){

    }

    public function getToken(){
        
    }


}