<?php 

namespace App\Controllers;
use App\Controllers\UserController;
use App\Models\Token;


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
            // TODO -> Redirect to error page
            echo 'Error: ' . curl_error($ch);
        } else {
            $responseData = json_decode($response, true);
            $user = new UserController();
            $userData = $user->logUser($responseData);
            $newTokenId = $this->storeToken($responseData['access_token'], $userData['id']);
            $this->saveSession($responseData['access_token'], $userData);
            header('Location: /public/');
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

    private function storeToken($token, $userId){
        $tokenModel = new Token($token, $userId);
        $newToken = $tokenModel->save();
        return $newToken;
    }

    private function saveSession($token, $userData){
        $data = [
            'token' => $token,
            'user' => $userData,
            'expiration' => time() + 3600
        ];
        
        $_SESSION['loggedUser'] = $data;
    }


}