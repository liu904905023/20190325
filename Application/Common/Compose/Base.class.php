<?php

namespace Common\Compose;

use Think\Controller;

class Base extends Controller{

    public function __construct(){
        parent::__construct();
		$status = session('status');
        if( !isset($status)){
			$this->redirect("Login/login");
        }


    }


}
