<?php

namespace Home\Controller;

//use Think\Controller;

//class PayController extends Controller {



use Common\Compose\Base;

class PayController extends Base {

	public function scan_code_payment(){

	R("Base/getMenu");

	// var_dump(session(SysNO));

	//$CustomerSysNo = session( data )['SysNo'];

	// $a = getMenu(session(SysNO));

	// var_dump($a);

	$this -> display();

	}

	public function scan_code_payment1(){

	R("Base/getMenu");

	// var_dump(session(SysNO));

	//$CustomerSysNo = session( data )['SysNo'];

	// $a = getMenu(session(SysNO));

	// var_dump($a);

	$this -> display();

	}

	public function scan_code_payment_Alipay(){

	R("Base/getMenu");

	$this -> display();

	}

    public function pay(){



		$ProductTitle = "apple";

		$Total_fee = yuan2fee($_POST['fee']);

		$auth_code = $_POST['auth_code'];

		$POSID = "123123123";

		$CustomerSysNo = session(servicestoreno);

		$data = array(

			"ProductTitle"=>$ProductTitle,

			"Total_fee"=>$Total_fee,

			"auth_code"=>$auth_code,

			"POSID"=>$POSID,

			"Old_SysNo"=>session(SysNO),

			"CustomerSysNo"=>$CustomerSysNo



		);

        $data = json_encode( $data );

		//var_dump($data);exit;

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            "X-Ywkj-Authentication:" . strlen( $data )

        );

		

		$list = http_request_notime(C('SERVER_HOST')."POS/POSOrderInsert",$data,$head);

		$list = json_decode($list);

        $this->ajaxReturn( $list ,json);

    }

	 public function pay1(){

	 	$out_trade_no=$this->getwxorder();

		$ProductTitle = "apple";

		$Total_fee = yuan2fee($_POST['fee']);

		$auth_code = $_POST['auth_code'];

		$POSID = "123123123";

		$CustomerSysNo = session(servicestoreno);


		$data = array(

			"ProductTitle"=>$ProductTitle,

			"Total_fee"=>$Total_fee,

			"auth_code"=>$auth_code,

			"POSID"=>$POSID,

			"Old_SysNo"=>session(SysNO),

			"CustomerSysNo"=>$CustomerSysNo,

			"out_trade_no"=>$out_trade_no

		);

        $data = json_encode( $data );

		// var_dump($data);exit;

        $head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:" . strlen( $data ),

            "X-Ywkj-Authentication:" . strlen( $data )

        );

		

		$list = http_request_notime(C('SERVER_HOST')."POS/POSOrderInsert",$data,$head);

		$list = json_decode($list);

        $this->ajaxReturn( $list ,json);

    }

	public function ali_pay(){

		$out_trade_no = $this->getaliorder();

		$Total_fee = yuan2fee($_POST['fee']);

		$Auth_code = $_POST['auth_code'];

		$CustomerSysNo = staffquerystore(session(SysNO));

		$data = array(

			"Total_amount"=>$Total_fee,

			"Auth_code"=>$Auth_code,

			"Old_SysNo"=>session(SysNO),

			"CustomerSysNo"=>$CustomerSysNo,

			"out_trade_no"=>$out_trade_no

		);

        $data = json_encode( $data );

		//var_dump($data);exit;

		$head = array(

            "Content-Type:application/json;charset=UTF-8",

            "Content-length:".strlen( $data ),

            "X-Ywkj-Authentication:".strlen( $data )

        );

		$list = http_request_notime(C('SERVER_HOST')."IPP3AliPay/BarcodePayUnion",$data,$head);

		$list = json_decode($list);

        $this->ajaxReturn( $list ,json);

	

	

	

	

	}


 	private function getwxorder(){

		$data = null;

		$data = json_encode( $data );

		$head = array(

		"Content-Type:application/json;charset=UTF-8",

		"Content-length:" . strlen( $data ),

		"X-Ywkj-Authentication:" . strlen( $data)

		);

		$list = http_request(C('SERVER_HOST')."IPP3OrderSysNo/GetWXOrder",$data,$head);

		$list = json_decode($list,true);

		// var_dump($list['Data']);exit;

		return $list['Data'];

	}

 	private function getaliorder(){

		$data = null;

		$data = json_encode( $data );

		$head = array(

		"Content-Type:application/json;charset=UTF-8",

		"Content-length:" . strlen( $data ),

		"X-Ywkj-Authentication:" . strlen( $data)

		);

		$list = http_request(C('SERVER_HOST')."IPP3OrderSysNo/GetAliOrder",$data,$head);

		$list = json_decode($list,true);

		// var_dump($list['Data']);exit;

		return $list['Data'];

	}




}