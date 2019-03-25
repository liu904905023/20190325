<?php
namespace Home\Controller;
use Think\Controller;
class IntelsController extends Controller {

   
    protected function _initialize(){
        //全局引入微信支付类

        Vendor('IntelPay.IntelPay');
        Vendor('WxpayV3.WxPayOut');
        Vendor('AliIsv.AopClient');
        Vendor('AliIsv.AlipayOpenAuthTokenAppRequest');
        Vendor('AliIsv.AlipaySystemOauthTokenRequest');

    }

    public function index(){
        if(IS_POST){

            $fee = yuan2fee(I('amount'));
            $userid = I('userid');
            $PayType = I('PayType');
            $systemUserSysNo=I('systemUserSysNo');
            $CustomerSysNO=I('CustomerSysNO');
            $CustomerName=I('CustomerName');
            $DisplayName=I('DisplayName');
            $AppId=I('AppId');
			$Switch = I('Switch');
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {

                if($PayType==104||$PayType==106){
                    $data['systemUserSysNo']=$systemUserSysNo;
                    $data['body']=$CustomerName;
                    $data['sub_openid']=$userid;
                    $data['total_fee']=$fee;
                    $data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
                    $url  = C('SERVER_HOST')."IPP3Swiftpass/WeiXinJsPayApi";
                    $list = http( $url, $data);
                    $jsApiParameters['PayInfo']['appId']=$list['Data']['PayData']['appId'];
                    $jsApiParameters['PayInfo']['paySign']=$list['Data']['PayData']['paySign'];
                    $jsApiParameters['PayInfo']['package']=$list['Data']['PayData']['package'];
                    $jsApiParameters['PayInfo']['out_trade_no']=$list['Data']['PayData']['out_trade_no'];
                    $jsApiParameters['PayInfo']['signType']=$list['Data']['PayData']['signType'];
                    $jsApiParameters['PayInfo']['timeStamp']=$list['Data']['PayData']['timeStamp'];
                    $jsApiParameters['PayInfo']['nonceStr']=$list['Data']['PayData']['nonceStr'];
                }else{

                    $data['systemUserSysNo'] = $systemUserSysNo;
                    $data['total_fee'] = $fee;
                    $data['openId'] = $userid;
					$url  = "http://suibian.yunlaohu.cn/Payment/Payments/GetUnifiedOrderResult";
//					$url  = C('SERVER_HOST')."Payment/Payments/GetUnifiedOrderResult";
                    $list = http( $url, $data);
																					\Think\Log::record(json_encode($data));
                    $jsApiParameters['PayInfo']=$list['Data']['WxPayData']['m_values'];

                }



            }
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false ) {
                if($PayType==104||$PayType==106){
                    $data = array(
                        "buyer_id"=>$userid,
                        "total_fee"=>$fee,
                        "systemUserSysNo"=>$systemUserSysNo,
                        "body"=>$CustomerName,
                        "buyer_logon_id"=>""
                    );
                    $url  = C('SERVER_HOST')."IPP3Swiftpass/AliPayJsPayApi";
                    $list = http( $url, $data);
                    $jsApiParameters['PayInfo']=$list['Data']['PayData']['tradeNO'];
                }else{
                    $data = array(
                        "buyer_id"=>$userid,
                        "Total_amount"=>$fee,
                        "CustomerSysNo"=>$CustomerSysNO,
                        "Old_SysNo"=>$systemUserSysNo
                    );
                    $url  = C('SERVER_HOST')."IPP3AliPay/TradeCreate";
                    $list = http( $url, $data);
                    $data = json_decode($list['Data'],true);
                    $jsApiParameters['PayInfo']=$data['alipay_trade_create_response']['trade_no'];
                }
            }
            $jsApiParameters['Ad_Info']['UserId'] = $userid;
            $jsApiParameters['Ad_Info']['Fee'] = I('amount');
            $jsApiParameters['Ad_Info']['CustomerName'] = urlencode($CustomerName);
            $jsApiParameters['Ad_Info']['AppId'] = $AppId;
            $jsApiParameters['Ad_Info']['systemUserSysNo'] = $systemUserSysNo;
            $jsApiParameters['Ad_Info']['Switch'] = $Switch;
            $this->ajaxreturn($jsApiParameters);

        }else{
            $systemUserSysNo=I('systemUserSysNo');//2--zijian 2406--zijian123--104 3402--xujiang123--106
            $data['systemUserSysNo'] = $systemUserSysNo;
            $url  = C('SERVER_HOST')."IPP3Customers/GetPayConfig";
            $list = http( $url, $data);
            $CustomerSysNO=$list['CustomerSystemUserBase']['CustomerSysNO'];
            $CustomerName=$list['CustomerSystemUserBase']['CustomerName'];
            $Customer_field_one=$list['CustomerSystemUserBase']['Customer_field_one'];
            $DisplayName=$list['CustomerSystemUserBase']['DisplayName'];
			$Switch=$list['CustomerSystemUserBase']['Switch'];

            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				foreach( $list['PassageWayList'] as $key=>$val){
				if($val['Config']=='SwiftPassageConfig_WX'){
//					$Wx_Appid=$list["SwiftPassageConfig_WX"]["WX_APPID"];
					$Wx_Appid='wxa85e01bfcc3b5d66';
					$Wx_Appsecret='56722ffad7089c826907fc673747c626';
//					$Wx_Appsecret=$list["SwiftPassageConfig_WX"]["WX_APPSECRET"];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WXConfig'){
//					$Wx_Appid = $list['WXConfig']['APPID'];
//					$Wx_Appsecret = $list['WXConfig']['APPSECRET'];
					$Wx_Appid='wxa85e01bfcc3b5d66';
					$Wx_Appsecret='56722ffad7089c826907fc673747c626';
					$PayType= $val['PassageWay'];

				}
			}
				if($PayType==''){
					return false;
				}
//                $Wx_Appid = 'wx261671a6d70c4db5';
//				$Wx_Appsecret = '0559d78cf2a556b1d7b46988f026114a';
                $coco = new \IntelWxPayApi($Wx_Appid,$Wx_Appsecret);
                //			$coco = new \IntelWxPayApi($list['WXConfig']['APPID'],$list['WXConfig']['APPSECRET']);
                $userid = $coco->GetWxOpenId();
				echo $userid;exit;
//				\Think\Log::record(($userid));
            }
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false ) {
				foreach( $list['PassageWayList'] as $key=>$val){
				if($val['Config']=='SwiftPassageConfig_ALi'){
					$Private_Key=$list['SwiftPassageConfig_ALi']['ALi_merchant_private_key']; 
					$Public_Key=$list['SwiftPassageConfig_ALi']['ALi_alipay_public_key']; 
					$AppID=$list['SwiftPassageConfig_ALi']['ALi_APPID']; 
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='AliPayConfig'){
					$Public_Key = $list['AliPayConfig']['Alipay_public_key'];
					$Private_Key = $list['AliPayConfig']['Merchant_private_key'];
					$AppID = $list['AliPayConfig']['AppID'];
					$PayType= $val['PassageWay'];

				}
				}
				if($PayType==''){
					return false;
				}
                $tools = new \IntelAliPayApi($AppID);
                $Auth_Code =  $tools->GetAuthCode();
                $aop = new \AopClient ();
                $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
                $aop->appId =$AppID;
                $aop->rsaPrivateKeyFilePath =$Private_Key;
                $aop->alipayPublicKey=$Public_Key;
                $aop->apiVersion = '1.0';
                $aop->postCharset='utf-8';
                $aop->format='json';
                $request = new \AlipaySystemOauthTokenRequest ();
                $request->setGrantType("authorization_code");
                $request->setCode("$Auth_Code");
                $result = $aop->execute ($request);
                //			$ReturnList =json_decode($result,true);
                $ReturnList = $tools->object_to_array($result);
                $userid=$ReturnList['alipay_system_oauth_token_response']['user_id'];
            }
            $this->assign('userid',$userid);
            $this->assign('CustomerName',$CustomerName);
            $this->assign('CustomerSysNO',$CustomerSysNO);
            $this->assign('DisplayName',$DisplayName);
            $this->assign('PayType',$PayType);
            $this->assign('AppId',$AppID);
            $this->assign('systemUserSysNo',$systemUserSysNo);
			$this->assign('Switch',$Switch);
            $this->display();
        }

    }


	


}                                   