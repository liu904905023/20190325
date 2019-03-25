<?php

namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;

class IndexController extends Base{

    public function index(){
        dump($_SESSION);
		R("Base/getMenu");
        $this->display();
    }

}
