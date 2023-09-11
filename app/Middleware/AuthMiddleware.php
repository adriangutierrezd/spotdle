<?php

namespace App\Middleware;

class AuthMiddleware {

    const RETURN_REDIRECT = 'REDIRECT';
    const RETURN_JSON = 'JSON';

    public function handle($return = self::RETURN_REDIRECT) {

        if (!isset($_SESSION['loggedUser']) || $this->isTokenExpired()) {

            if($return === self::RETURN_JSON){
                http_response_code(401);
                echo json_encode(['message' => 'Unauthorized']);
                die();
            }

            header('Location: /public/');
            exit;
        }

    }

    private function isTokenExpired(){
        return $_SESSION['loggedUser']['expiration'] <= time();
    }

}
