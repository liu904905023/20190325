<?php
namespace Home\Controller;
use Think\Controller;
class IsvController extends Controller {
	protected function _initialize(){
    	Vendor('AliIsv.AopClient');
    	Vendor('AliIsv.AlipayOpenAuthTokenAppRequest');
    	Vendor('AliIsv.AlipaySystemOauthTokenRequest');
	}
    public function index(){
		$Code  = $_GET['app_auth_code'];
		$Auth_Code  = $_GET['auth_code'];
		$SysNO = $_GET['systemUserSysNo'];
		if (preg_match("/^\d*$/", $SysNO)&&$SysNO!='') {

		}else{
			return false;
		}
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
		if($Code){
		$aop = new \AopClient ();
		$aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
		$aop->appId = $list['AppID'];
		$aop->rsaPrivateKeyFilePath =$private_key;
		$aop->alipayPublicKey=$public_key;
		$aop->apiVersion = '1.0';
		$aop->postCharset='utf-8';
		$aop->format='json';
		$aop->signType='RSA';
		$request = new \AlipayOpenAuthTokenAppRequest();
		$request->setBizContent("{" .
		"    \"grant_type\":\"authorization_code\"," .
		"    \"code\":\"$Code\"" .
		"  }");


		$result = $aop->execute ( $request);
    	$ReturnList =$this-> object_to_array($result);
		$Post_List['app_auth_token'] = $ReturnList['alipay_open_auth_token_app_response']['app_auth_token'];
		$Post_List['PID'] = $ReturnList['alipay_open_auth_token_app_response']['user_id'];
		$Post_List['CustomerServiceSysNo'] = staffquerystore($SysNO);
		$Post_List['Token_EndTime'] = date('Y-m-d H:i:s',strtotime('+360 day'));
		if($Post_List['app_auth_token']){
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		echo "<div style=' position: fixed; top: 50%; margin-top: -50px; width: 100%; text-align: center; height: 100px;font-size:100px; color:#00aaee;'>授权成功</div>";
		}else{
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
		echo "<div style=' position: fixed; top: 50%; margin-top: -50px; width: 100%; text-align: center; height: 100px;font-size:100px; color:#ee2200;'>授权失败</div>";
		exit;
		};

	
//		var_dump($data);exit;
		$Post_Url = C('SERVER_HOST') ."IPP3Customers/IPP3CustomerAliPayConfigTokenUpdate";
		$Get_List = http( $Post_Url, $Post_List);

		}
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
			$ReturnList =$this-> object_to_array($result);
			$this -> assign('userid',$ReturnList['alipay_system_oauth_token_response']['user_id']);
			$this -> assign('systemUserSysNo',$SysNO);
			$this -> assign('UserName',QueryStaffInfo($SysNO));
			$this -> assign('CustomerName',GetCustomerServiceSysNo($SysNO));
			$this->display('Isv:index');


		}

    }

	public function jsapi(){


		$money = I('amount');
		$userid = I('userid');
		$systemUserSysNo = I('systemUserSysNo');
		$CustomId =  staffquerystore($systemUserSysNo);
		$data = array(

		"buyer_id"=>$userid,
		"Total_amount"=>yuan2fee($money),
		"CustomerSysNo"=>$CustomId,
		"Old_SysNo"=>$systemUserSysNo
		);
		$data = json_encode($data);
		$url  = C('SERVER_HOST')."IPP3AliPay/TradeCreate";
		$head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data )
//            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request( $url, $data, $head );
		$list = json_decode($list,true);
		$data = json_decode($list['Data'],true);
		$this->assign('out_trade_no',$data['alipay_trade_create_response']['trade_no']);
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