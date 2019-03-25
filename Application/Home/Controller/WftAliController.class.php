<?php
namespace Home\Controller;
use Think\Controller;
class WftAliController extends Controller {
	protected function _initialize(){
		//全局引入微信支付类

    	Vendor('AliIsv.AopClient');
    	Vendor('AliIsv.AlipayOpenAuthTokenAppRequest');
    	Vendor('AliIsv.AlipaySystemOauthTokenRequest');
	}
    public function index(){
		$Code  = $_GET['app_auth_code'];
		$Auth_Code  = $_GET['auth_code'];
		$SysNO = $_GET['systemUserSysNo'];
		
		$data['systemUserSysNo'] = $SysNO;
		$data = json_encode($data);
		$url  = C('SERVER_HOST')."IPP3Customers/IPP3AliPayConfigBySUsysNo"; 
		$head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data )
//            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request( $url, $data, $head );
		$list = json_decode($list,true);
		$public_key = $list['Alipay_public_key'];
		$private_key = $list['Merchant_private_key'];

		
		if($Auth_Code){
			$aop = new \AopClient ();
			$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
			$aop->appId = $list['AppID'];
			$aop->rsaPrivateKeyFilePath =$private_key;
			$aop->alipayPublicKey=$public_key;
			$aop->apiVersion = '1.0';
			$aop->postCharset='utf-8';
			$aop->format='json';
			$request = new \AlipaySystemOauthTokenRequest ();
			$request->setGrantType("authorization_code");
			$request->setCode("$Auth_Code");
			$result = $aop->execute ($request);
//			var_dump($result);exit;
			$ReturnList =$this-> object_to_array($result);
			$this -> assign('userid',$ReturnList['alipay_system_oauth_token_response']['user_id']);
			$this -> assign('systemUserSysNo',$SysNO);
			$this -> assign('UserName',QueryStaffInfo($SysNO));
			$this -> assign('CustomerName',GetCustomerServiceSysNo($SysNO));
			$this->display('index'); 
				

		}
		
    }

	public function jsapi(){
	
	
		$money = I('amount');
		$userid = I('userid');
		$systemUserSysNo = I('systemUserSysNo');
		$CustomerName = I('CustomerName');
		$CustomId =  staffquerystore($systemUserSysNo);
		$data = array(
			
		"buyer_id"=>$userid,
		"total_fee"=>yuan2fee($money),
		"systemUserSysNo"=>$systemUserSysNo,
		"body"=>$CustomerName,
		"buyer_logon_id"=>""
		);
		$data = json_encode($data);
//		$url  = "https://pos.yunlaohu.cn/IPP3Swiftpass/AliPayJsPayApi"; 
		$url  = C('SERVER_HOST')."IPP3Swiftpass/AliPayJsPayApi"; 
		$head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data )
//            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request( $url, $data, $head );
		$list = json_decode($list,true);
		$this->assign('out_trade_no',$list['Data']['PayData']['tradeNO']);
		$this->display();
	
	
	
	
	
	}










		private function object_to_array($obj) 
		{ 
			$_arr= is_object($obj) ? get_object_vars($obj) : $obj; 
			foreach($_arr as $key=> $val) 
			{ 
				$val= (is_array($val) || is_object($val))?$this->object_to_array($val) : $val; 
				$arr[$key] = $val; 
			} 
			return $arr; 
		}

    
}