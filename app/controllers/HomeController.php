<?php

namespace App\Controllers;

class HomeController extends BaseController{
    
    public function index(){

        if(isset($_SESSION['loggedUser'])){
            $this->view('home');
        }else{
            $this->view('welcome');
        }

    }

    
}