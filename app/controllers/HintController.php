<?php 

namespace App\Controllers;
use App\Models\Hint;

class HintController extends BaseController{

    public function __construct(){
        parent::__construct();
    }


    public function getHints($game_type){

        $hint = new Hint();
        $hints = $hint->get($game_type);

        $this->httpResponse(200, 'Hits fetched', $hints);

    }


}
