<?php
// +----------------------------------------------------------------------
// | 设计开发：Webster  Tel:17095135002 邮箱：312549912@qq.com
// | 此版本为微信官方最新微信支付V3版本
// +----------------------------------------------------------------------
namespace Home\Controller;
use Think\Controller;
class WxpayController extends Controller {
	protected function _initialize(){
    	Vendor('WxpayV3.WxPayPubHelper');
    	Vendor('WxpayV3.WxPayOut');
		Vendor('IntelPay.IntelPay');
	}
	public function newpay(){
		$tools = new \JsApiPay();
		$systemUserSysNo =$_GET['systemUserSysNo'];
		$data['systemUserSysNo'] =$systemUserSysNo;
		$url  = C('SERVER_HOST')."IPP3Customers/IPP3WxconfigBySUsysNo";
        $data = json_encode( $data );
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:".strlen( $data ),
            //"X-Ywkj-Authentication:" . strlen( $data ),
        );
		$Customer = GetCustomerServiceSysNo($systemUserSysNo);
		$systemUserName= QueryStaffInfo($systemUserSysNo);
        $list = http_request( $url, $data, $head );
        $list = json_decode( $list ,true);
		cookie('appid',$list['APPID']);
		cookie('mchid',$list['NCHID']);
		cookie('key',$list['KEY']);
		cookie('appsecret',$list['APPSECRET']);
		cookie('sub_mch_id',$list['sub_mch_id']);
		cookie('systemUserSysNo',$systemUserSysNo);
		cookie('openId',$tools->GetOpenid());
		//\Think\Log::record("日志".cookie('openId') );
		$this->assign('Customer',$Customer);
		$this->assign('systemUserSysNo',$systemUserName);
		$this->display();	
	
	}
    public function jsapi(){
		
    	//①、获取用户openid
		$fee = yuan2fee($_POST['amount']);
		$openId = cookie('openId');
		$systemUserSysNo=(string)cookie('systemUserSysNo');
		$CustomInfo = GetCustomerInfoBySysNo($systemUserSysNo);
		$Customname = $CustomInfo['CustomerName'];
		$CustomSysNo = $CustomInfo['CustomerSysNo'];
		$tools = new \JsApiPay();
		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$Randoms = new \Random();
		$Out_trade_no = $Randoms->GenerateOutTradeNo($systemUserSysNo);
		$input->SetBody("$Customname");
		$input->SetAttach("$systemUserSysNo");
		$input->SetOut_trade_no("$Out_trade_no");
		$input->SetTotal_fee("$fee");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url("http://payapi.yunlaohu.cn/IPP3Order/Notify");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = \WxPayApi::unifiedOrder($input);
		$jsApiParameters = $tools->GetJsApiParameters($order);
		//获取共享收货地址js函数参数
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->display();	
		$this->postlog($CustomSysNo,$systemUserSysNo,$fee,$Out_trade_no);
    }
	private function postlog($CustomerServiceSysNo,$SystemUserSysNo,$Total_fee,$Out_trade_no) {
        $data['CustomerServiceSysNo']=$CustomerServiceSysNo;
        $data['SystemUserSysNo']=$SystemUserSysNo;
        $data['Total_fee']=$Total_fee;
        $data['Out_trade_no']=$Out_trade_no;
        $data['Pay_Type']="102";
        $data = json_encode($data);
        $url = C('SERVER_HOST') ."/IPP3Order/IPP3So_MasterLog";
        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data), "X-Ywkj-Authentication:" . strlen($data));
        $list = http_request( $url, $data, $head);
    }
    //扫码支付 模式一 模式二 V3版本微信支付
    public function native(){
        $status = session('status');
        if( !isset($status)){
            $this->redirect("Login/login");
        }else{
            R("Base/getMenu");
        }

    	$this->display();
    }
    //刷卡支付 V3版本微信支付
    public function micropay(){
    	if(isset($_REQUEST["auth_code"]) && $_REQUEST["auth_code"] != ""){
    		$auth_code = $_REQUEST["auth_code"];
    		$input = new \WxPayMicroPay();
    		$input->SetAuth_code($auth_code);
    		$input->SetBody("刷卡测试样例-支付");
    		$input->SetTotal_fee("1");
    		$input->SetOut_trade_no(cookie('mchid').date("YmdHis"));
    	
    		$microPay = new \MicroPay();
    		dump($microPay->pay($input));
    	}
    	$this->display();
    }
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
		$data['openId']=$this->GetOpenId();
		$data['total_fee']=yuan2fee(($this->aes($_GET['amount'])));
		$data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
		$data['out_trade_no']=(string)$this->aes($_GET['out_trade_no']);
//		$url  = "https://pos.yunlaohu.cn/IPP3Swiftpass/WeiXinJsPayApi";
		$url  = "http://suibian.yunlaohu.cn/Payment/Payments/GetUnifiedOrderResult";
//				\Think\Log::record(json_encode($data));

		$list = http( $url, $data);
//		dump($list);exit;
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