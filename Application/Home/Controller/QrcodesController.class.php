<?php
namespace Home\Controller;
use Common\Compose\Base;


class QrcodesController extends Base{

	public function index(){
		R("Base/getMenu");
        $this->display();
    }

  



}
