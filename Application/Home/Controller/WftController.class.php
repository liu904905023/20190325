<?php

namespace Home\Controller;
use Think\Controller;

class WftController extends Controller {
	protected function _initialize(){
    	Vendor('IntelPay.IntelPay');
	}
    public function newpay(){
		$systemUserSysNo =$_GET['systemUserSysNo'];
		$Customer = GetCustomerServiceSysNo($systemUserSysNo);
		$systemUserName= QueryStaffInfo($systemUserSysNo);
		$this->assign('openid',$this->GetOpenId());
		$this->assign('Customer',$Customer);
		$this->assign('systemUserName',$systemUserName);
		$this->assign('systemUserSysNo',$systemUserSysNo);
        $this->display();
    }
	public function index(){
//echo 		$this->GetOpenId();exit;
//		$systemUserSysNo=I('systemUserSysNo');//2--zijian 2406--zijian123--104 3402--xujiang123--106
		$systemUserSysNo=6483;
		$data['systemUserSysNo'] = $systemUserSysNo;
		$data = json_encode($data);
		$url  = C('SERVER_HOST')."IPP3Customers/GetPayConfig"; 
		$head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data )
//            "X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request( $url, $data, $head );
		$list = json_decode( $list ,true);
		$CustomerSysNO=$list['CustomerSystemUserBase']['CustomerSysNO']; 
		$Customer=$list['CustomerSystemUserBase']['CustomerName']; 
		$Customer_field_one=$list['CustomerSystemUserBase']['Customer_field_one']; 
		$systemUserName=$list['CustomerSystemUserBase']['DisplayName'];
		$coco = new \IntelWxPayApi($list['WXConfig']['APPID'],$list['WXConfig']['APPSECRET']);
		$openid = $coco->GetWxOpenId();
		echo $openid;exit;
		$this->assign('openid',$openid);
		$this->assign('Customer',$Customer);
		$this->assign('systemUserName',$systemUserName);
		$this->assign('systemUserSysNo',$systemUserSysNo);
		$this->display();
	}


    public function jsapi(){
		$data['systemUserSysNo']=$_POST['systemUserSysNo'];
		$data['body']=$_POST['Customer'];
		$data['sub_openid']=$_POST['openid'];
		$data['total_fee']=yuan2fee($_POST['amount']);
		$data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
		$url  = C('SERVER_HOST')."IPP3Swiftpass/WeiXinJsPayApi";
        $data = json_encode( $data );
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:".strlen( $data ),
            //"X-Ywkj-Authentication:" . strlen( $data ),
        );
		$list = http_request( $url, $data, $head );
//				\Think\Log::record($list);
		$list = json_decode($list,true);
		$this->assign('appId',$list['Data']['PayData']['appId']);
		$this->assign('paySign',$list['Data']['PayData']['paySign']);
		$this->assign('package',$list['Data']['PayData']['package']);
		$this->assign('out_trade_no',$list['Data']['PayData']['out_trade_no']);
		$this->assign('signType',$list['Data']['PayData']['signType']);
		$this->assign('timeStamp',$list['Data']['PayData']['timeStamp']);
		$this->assign('nonceStr',$list['Data']['PayData']['nonceStr']);
        $this->display();

    }
	public function recharge(){
		$url  = C('SERVER_HOST')."IPP3Customers/IPP3DYC_Money_TypeList";
//		$url  = "https://pos.yunlaohu.cn/IPP3Customers/IPP3DYC_Money_TypeList";
        $data = json_encode( $data );
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:".strlen( $data ),
            //"X-Ywkj-Authentication:" . strlen( $data )
        );
		$list = http_request( $url, $data, $head );
		$data = json_decode($list,true);
		$this->assign("openid",$this->GetOpenId());
		$this->assign("systemUserSysNo",6949);
		$this->assign("data",$data);
		$this->display();
	}
	public function recharge_jsapi(){
//		\Think\Log::record(json_encode($_POST));
			$data['systemUserSysNo']=I('systemUserSysNo');
			$data['body']="";
			$data['attach']="1|".I('company')."|".I('contact')."|".I('num')."|".I('mobile');
			$data['sub_openid']=I('openid');
			$data['total_fee']=yuan2fee(I('fee'));
			$data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
//			$url  = "https://pos.yunlaohu.cn/IPP3Swiftpass/WeiXinJsPayApi";
			$url  = C('SERVER_HOST')."IPP3Swiftpass/WeiXinJsPayApi";
			
			$list = http( $url, $data);
//			\Think\Log::record(json_encode($list));
			$this->assign('appId',$list['Data']['PayData']['appId']);
			$this->assign('paySign',$list['Data']['PayData']['paySign']);
			$this->assign('package',$list['Data']['PayData']['package']);
			$this->assign('out_trade_no',$list['Data']['PayData']['out_trade_no']);
			$this->assign('signType',$list['Data']['PayData']['signType']);
			$this->assign('timeStamp',$list['Data']['PayData']['timeStamp']);
			$this->assign('nonceStr',$list['Data']['PayData']['nonceStr']);
			$this->display();
	
	
	
	}
	//bank_weixin
	public function weixin(){
//		$oldtime = '20171023105000';
		$posttime = $this->aes($_GET['datetime']);
		if($_GET['datetime']==""){
			$this->redirect("Base/istrue",array('message'=>3));
				return false;
		}else{
			if((time()-strtotime($posttime))>300){
				$this->redirect("Base/istrue");
				return false;
			}
		}

		$data['systemUserSysNo']=$this->aes($_GET['id']);
		$data['body']="";
		$data['sub_openid']=$this->GetOpenId();
		$data['total_fee']=yuan2fee(($this->aes($_GET['amount'])));
		$data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
		$data['out_trade_no']=(string)$this->aes($_GET['out_trade_no']);
//		$url  = "https://pos.yunlaohu.cn/IPP3Swiftpass/WeiXinJsPayApi";
		$url  = C('SERVER_HOST')."IPP3Swiftpass/WeiXinJsPayApi";
//				\Think\Log::record(json_encode($data));

		$list = http( $url, $data);
//		\Think\Log::record(json_encode($list));
//		\Think\Log::record(json_encode($_GET));
		$this->assign('appId',$list['Data']['PayData']['appId']);
		$this->assign('paySign',$list['Data']['PayData']['paySign']);
		$this->assign('package',$list['Data']['PayData']['package']);
		$this->assign('out_trade_no',$list['Data']['PayData']['out_trade_no']);
		$this->assign('signType',$list['Data']['PayData']['signType']);
		$this->assign('timeStamp',$list['Data']['PayData']['timeStamp']);
		$this->assign('nonceStr',$list['Data']['PayData']['nonceStr']);
		$this->assign('callback',$_GET['callback']);
		$this->assign('out_trade_no',$_GET['out_trade_no']);
        $this->display();

    }
	//direct_weixin
	public function weixin_direct(){
		$posttime = $this->aes($_GET['datetime']);
		if($_GET['datetime']==""){
			$this->redirect("Base/istrue",array('message'=>3));
				return false;
		}else{
			if((time()-strtotime($posttime))>300){
				$this->redirect("Base/istrue");
				return false;
			}
		}

		$data['systemUserSysNo']=$this->aes($_GET['id']);
		$data['openId']=$this->GetOpenId();
		$data['total_fee']=yuan2fee(($this->aes($_GET['amount'])));
		$data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
		$data['out_trade_no']=(string)$this->aes($_GET['out_trade_no']);
//		$url  = "https://pa.yunlaohu.cn/IPP3Swiftpass/WeiXinJsPayApi";
		$url  = C('SERVER_HOST')."Payment/Payments/GetUnifiedOrderResult";
//		$url  = "http://suibian.yunlaohu.cn/Payment/Payments/GetUnifiedOrderResult";
//				\Think\Log::record(json_encode($data));

		$list = http( $url, $data);
//		dump($list);exit;
//		\Think\Log::record(json_encode($list));
//		\Think\Log::record(json_encode($_GET));
		$this->assign('appId',$list['Data']['WxPayData']['m_values']['appId']);
		$this->assign('paySign',$list['Data']['WxPayData']['m_values']['paySign']);
		$this->assign('package',$list['Data']['WxPayData']['m_values']['package']);
		$this->assign('out_trade_no',$list['Data']['WxPayData']['m_values']['out_trade_no']);
		$this->assign('signType',$list['Data']['WxPayData']['m_values']['signType']);
		$this->assign('timeStamp',$list['Data']['WxPayData']['m_values']['timeStamp']);
		$this->assign('nonceStr',$list['Data']['WxPayData']['m_values']['nonceStr']);
		$this->assign('callback',$_GET['callback']);
		$this->assign('out_trade_no',$_GET['out_trade_no']);
        $this->display();

    }
	private function aes($de){
		$privateKey = "1234qwer5678asda";
		$iv     = "yCJXKLv4GvySreYK";
		$encryptedData = base64_decode($de);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $privateKey, $encryptedData, MCRYPT_MODE_CBC, $iv);
		return trim($decrypted);
	}
    public function  GetOpenId(){
        if (!isset($_GET['code'])){
            $URL['PHP_SELF'] = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);   //当前页面名称
            $URL['DOMAIN'] = $_SERVER['SERVER_NAME'];  //域名(主机名)
            $URL['QUERY_STRING'] = $_SERVER['QUERY_STRING'];   //URL 参数
            $URL['URI'] = $URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?".$URL['QUERY_STRING'] : "");
            $baseUrl = urlencode("http://".$URL['DOMAIN'].$URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?".$URL['QUERY_STRING'] : ""));
            $url = $this->__CreateOauthUrlForCode($baseUrl);
            Header("Location: $url");
            exit();
        }else {
            $code = $_GET['code'];
            $openid = $this->getOpenidFromMp($code);
            return $openid;

        }



    }
    public function __CreateOauthUrlForCode($redirectUrl)
    {
        $urlObj["appid"] = "wxa85e01bfcc3b5d66";
        $urlObj["redirect_uri"] = "$redirectUrl";
        $urlObj["response_type"] = "code";
        $urlObj["scope"] = "snsapi_base";
        $urlObj["state"] = "STATE"."#wechat_redirect";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
    }
    public function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    public function __CreateOauthUrlForOpenid($code)
    {
        $urlObj["appid"] = "wxa85e01bfcc3b5d66";
        $urlObj["secret"] = "56722ffad7089c826907fc673747c626";
        $urlObj["code"] = $code;
        $urlObj["grant_type"] = "authorization_code";
        $bizString = $this->ToUrlParams($urlObj);
        return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
    }
    public function getOpenidFromMp($code)
    {
        $url = $this->__CreateOauthUrlForOpenid($code);
        //初始化curl
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //运行curl，结果以jason形式返回
        $res = curl_exec($ch);
        curl_close($ch);
        //取出openid
        $data = json_decode($res,true);
        $this->data = $data;
        $openid = $data['openid'];
        return $openid;
    }

}