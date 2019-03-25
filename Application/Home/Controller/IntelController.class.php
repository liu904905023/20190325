<?php
namespace Home\Controller;
use Think\Controller;
class IntelController extends Controller {

   
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
                }else if($PayType==102){
                    $data['systemUserSysNo'] = $systemUserSysNo;
                    $data['total_fee'] = $fee;
                    $data['openId'] = $userid;
					$url  = C('SERVER_HOST')."Payment/Payments/GetUnifiedOrderResult";
                    $list = http( $url, $data);

                    $jsApiParameters['PayInfo']=$list['Data']['WxPayData']['m_values'];

                }else if($PayType==108){
					$data['ReqModel']['TotalAmount']=$fee;
					$data['ReqModel']['ChannelType']='WX';
					$data['ReqModel']['OpenId']=$userid;
					$data['SystemUserSysNo']=$systemUserSysNo;
					$data['ReqModel']['SubAppId']='wxa85e01bfcc3b5d66';
					$url  = "https://payapi.yunlaohu.cn/IPP3WSOrder/WSJsPayUnion";
					$list = http( $url, $data);
					$jsApiParameters['PayInfo']=$list['Data']['PayInfo'];
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
                }else if($PayType==102){
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
                }else if($PayType==108){
				
					$data['ReqModel']['TotalAmount']=$fee;
					$data['ReqModel']['ChannelType']='ALI';
					$data['ReqModel']['OpenId']=$userid;
					$data['SystemUserSysNo']=$systemUserSysNo;
					$url  = "https://payapi.yunlaohu.cn/IPP3WSOrder/WSJsPayUnion";
                    $list = http( $url, $data);
//					\Think\Log::record(json_encode($list));
					$jsApiParameters['PayInfo']=$list['Data']['PrePayId'];
				}
            }
			$jsApiParameters['Code']=$list['Code'];
			$jsApiParameters['Description']=$list['Description'];
            $jsApiParameters['Ad_Info']['UserId'] = $userid;
            $jsApiParameters['Ad_Info']['Fee'] = I('amount');
            $jsApiParameters['Ad_Info']['CustomerName'] = urlencode($CustomerName);
            $jsApiParameters['Ad_Info']['AppId'] = $AppId;
            $jsApiParameters['Ad_Info']['systemUserSysNo'] = $systemUserSysNo;
            $jsApiParameters['Ad_Info']['Switch'] = $Switch;
            $this->ajaxreturn($jsApiParameters);

        }else{
            $systemUserSysNo=I('systemUserSysNo');//2--zijian 2406--zijian123--104 3402--xujiang123--106
			if (preg_match("/^\d*$/", $systemUserSysNo)&&$systemUserSysNo!='') {

			}else{
				return false;
			}
            $data['systemUserSysNo'] = $systemUserSysNo;
            $url  = "https://payapi.yunlaohu.cn/IPP3Customers/GetPayConfig";
            $list = http( $url, $data);
//			dump($list);exit;
            $CustomerSysNO=$list['CustomerSystemUserBase']['CustomerSysNO'];
            $CustomerName=$list['CustomerSystemUserBase']['CustomerName'];
            $Customer_field_one=$list['CustomerSystemUserBase']['Customer_field_one'];
            $DisplayName=$list['CustomerSystemUserBase']['DisplayName'];
			$Switch=$list['CustomerSystemUserBase']['Switch'];

            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
				foreach( $list['PassageWayList'] as $key=>$val){
				if($val['Config']=='SwiftPassageConfig_WX'){
					$Wx_Appid=$list["SwiftPassageConfig_WX"]["WX_APPID"];
//					$Wx_Appid='wx261671a6d70c4db5';;
//					$Wx_Appsecret='0559d78cf2a556b1d7b46988f026114a';
					$Wx_Appsecret=$list["SwiftPassageConfig_WX"]["WX_APPSECRET"];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WXConfig'){
					$Wx_Appid = $list['WXConfig']['APPID'];
					$Wx_Appsecret = $list['WXConfig']['APPSECRET'];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WS_PassageConfig_WX'){
					if($list['WS_PassageConfig_WX']['WS_APPIDTwo']==''&&$list['WS_PassageConfig_WX']['WS_APPSECRET']==''){
						$Wx_Appid = $list['WS_PassageConfig_WX']['WX_APPID'];
						$Wx_Appsecret = $list['WS_PassageConfig_WX']['WX_APPSECRET'];
					}else{
						$Wx_Appid = $list['WS_PassageConfig_WX']['WS_APPIDTwo'];
						$Wx_Appsecret = $list['WS_PassageConfig_WX']['WS_APPSECRET'];
					}
					$PayType= $val['PassageWay'];
				
				
				}
			}
				if($PayType==''){
					return false;
				}
                $coco = new \IntelWxPayApi($Wx_Appid,$Wx_Appsecret);
				try{
				$userid = $coco->GetWxOpenId();
				}
				catch(Exception $e){
					\Think\Log::record($e->getMessage());
				}
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

				}else if($val['Config']=='WS_PassageConfig_ALi'){
					$Public_Key = $list['WS_PassageConfig_ALi']['ALi_alipay_public_key'];
					$Private_Key = $list['WS_PassageConfig_ALi']['ALi_merchant_private_key'];
					$AppID = $list['WS_PassageConfig_ALi']['ALi_APPID'];
					$PayType= $val['PassageWay'];

				}else if($val['PassageWay']=='110'){
                        $arr_url["SystemUserSysNo"]  = $systemUserSysNo;
                        $select_ali_url = C('SERVER_HOST')."IPP3Customers/SystemUser_Extend_AliList";
                        $return_ali_url = http($select_ali_url, $arr_url);
                        if($return_ali_url['Data']['Ali_url']){
                            header( 'Location:'.$return_ali_url['Data']['Ali_url'] );
                        }else{
                            $this->redirect("Base/info",array('message'=>1,'icon_href'=>'ico_hint.png'));
                            return false;
                        }
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
			if($userid==''){
					header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/index.php/Intel/index?systemUserSysNo='.$systemUserSysNo);
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


    public function index_v1(){
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
					$url  = C('SERVER_HOST')."Payment/Payments/GetUnifiedOrderResult";
                    $list = http( $url, $data);
//																					\Think\Log::record($list);
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
			if (preg_match("/^\d*$/", $systemUserSysNo)&&$systemUserSysNo!='') {

			}else{
				return false;
			}
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
					$Wx_Appid=$list["SwiftPassageConfig_WX"]["WX_APPID"];
//					$Wx_Appid='wx261671a6d70c4db5';;
//					$Wx_Appsecret='0559d78cf2a556b1d7b46988f026114a';
					$Wx_Appsecret=$list["SwiftPassageConfig_WX"]["WX_APPSECRET"];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WXConfig'){
					$Wx_Appid = $list['WXConfig']['APPID'];
					$Wx_Appsecret = $list['WXConfig']['APPSECRET'];
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
			if($userid==''){
					header( 'Location: http://'.$_SERVER['HTTP_HOST'].'/index.php/Intel/index?systemUserSysNo='.$systemUserSysNo);
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


	    public function EFuliPay(){

        if (IS_POST) {
            $fee = yuan2fee(I('amount'));
            $userid = I('userid');
            $systemUserSysNo = I('systemUserSysNo');
            $PayType = I('PayType');
            $EFuli_Money_TypeSysNo=I('cashVoucher');
            $Purchase_Amount=yuan2fee(I('cashAmount'));
            $Purchase_Num=I('count');
            $SMS_One=I('userName');
            $Mobile=I('telephone');
            $Switch=I('Switch');

//			$Query_Stock['CustomerTopSysNo']=1;
            $Query_Stock['CustomerTopSysNo']=I('CustomersTopSysNo');
            $Query_Stock['EFuli_Money_TypeSysNo']=I('cashVoucher');

            if($PayType==102||$PayType==103||$PayType==110){
                $jsApiParameters['Code']=1;
                $jsApiParameters['Description']="支付失败！！";
                $this->ajaxreturn($jsApiParameters);
                exit;
            }
            $Query_Stock_Url  = C('SERVER_HOST')."IPP3EFuli/EFuli_StockRealtimeList";
            $Query_Stock_List = http( $Query_Stock_Url, $Query_Stock);
//						\Think\Log::record($PayType."---".$Mobile);

            $Left_Count = $Query_Stock_List['Data'][0]['Selling_Num'];
            if($Purchase_Num>$Left_Count){
                $jsApiParameters['Code']=1;
                $jsApiParameters['Description']="库存不足！";
                $this->ajaxreturn($jsApiParameters);
                exit;
            }
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {

                if($PayType==104||$PayType==106){

                    $data['systemUserSysNo']=$systemUserSysNo;
                    $data['body']=$CustomerName;
                    $data['sub_openid']=$userid;
                    $data['total_fee']=$fee;

                    $data['CashExtend']['EFuli_Money_TypeSysNo']=$EFuli_Money_TypeSysNo;
                    $data['CashExtend']['Selling_Amount']=$Purchase_Amount;
                    $data['CashExtend']['Selling_Num']=$Purchase_Num;

                    $data['Buyer']['Name']=$SMS_One;
                    $data['Buyer']['PhoneNo']=$Mobile;


                    $data['callback_url']="web.yunlaohu.cn/index.php/Wft/jsapi";
                    $url  = C('SERVER_HOST')."IPP3Swiftpass/WeiXinJsPayApiWithCash";
                    $list = http( $url, $data);
                    $jsApiParameters['PayInfo']['appId']=$list['Data']['PayData']['appId'];
                    $jsApiParameters['PayInfo']['paySign']=$list['Data']['PayData']['paySign'];
                    $jsApiParameters['PayInfo']['package']=$list['Data']['PayData']['package'];
                    $jsApiParameters['PayInfo']['out_trade_no']=$list['Data']['PayData']['out_trade_no'];
                    $jsApiParameters['PayInfo']['signType']=$list['Data']['PayData']['signType'];
                    $jsApiParameters['PayInfo']['timeStamp']=$list['Data']['PayData']['timeStamp'];
                    $jsApiParameters['PayInfo']['nonceStr']=$list['Data']['PayData']['nonceStr'];
                }else if($PayType==108){
                    $data['ReqModel']['TotalAmount']=$fee;
                    $data['ReqModel']['ChannelType']='WX';
                    $data['ReqModel']['OpenId']=$userid;
                    $data['ReqModel']['SubAppId']='wxa85e01bfcc3b5d66';
                    $data['SystemUserSysNo']=$systemUserSysNo;
                    $data['EFuli_Money_TypeSysNo']=$EFuli_Money_TypeSysNo;
                    $data['Selling_Amount']=$Purchase_Amount;
                    $data['Selling_Num']=$Purchase_Num;
                    $data['SMS_One']=$SMS_One;
                    $data['Mobile']=$Mobile;
//					\Think\Log::record(json_encode($data));
                    $url  = C('SERVER_HOST')."IPP3WSOrder/WSJsPayUnionWithEFuli";
                    $list = http( $url, $data);
                    $jsApiParameters['PayInfo']=$list['Data']['PayInfo'];

                }
            }
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false ) {
                if($PayType==104||$PayType==106){
                    $data['buyer_id'] = $userid;
                    $data['total_fee'] = $fee;
                    $data['systemUserSysNo'] = $systemUserSysNo;
                    $data['body'] = $CustomerName;
                    $data['buyer_logon_id'] = '';

                    $data['CashExtend']['EFuli_Money_TypeSysNo']=$EFuli_Money_TypeSysNo;
                    $data['CashExtend']['Selling_Amount']=$Purchase_Amount;
                    $data['CashExtend']['Selling_Num']=$Purchase_Num;

                    $data['Buyer']['Name']=$SMS_One;
                    $data['Buyer']['PhoneNo']=$Mobile;


                    $url  = C('SERVER_HOST')."IPP3Swiftpass/AliPayJsPayApiWithCash";
                    $list = http( $url, $data);

                    $jsApiParameters['PayInfo']=$list['Data']['PayData']['tradeNO'];
                }else if($PayType==108){

                    $data['ReqModel']['TotalAmount']=$fee;
                    $data['ReqModel']['ChannelType']='ALI';
                    $data['ReqModel']['OpenId']=$userid;
                    $data['SystemUserSysNo']=$systemUserSysNo;
                    $data['EFuli_Money_TypeSysNo']=$EFuli_Money_TypeSysNo;
                    $data['Selling_Amount']=$Purchase_Amount;
                    $data['Selling_Num']=$Purchase_Num;
                    $data['SMS_One']=$SMS_One;
                    $data['Mobile']=$Mobile;
                    $url  = C('SERVER_HOST')."IPP3WSOrder/WSJsPayUnionWithEFuli";
                    $list = http( $url, $data);
//																				\Think\Log::record(json_encode($list));

                    $jsApiParameters['PayInfo']=$list['Data']['PrePayId'];
                }
            }
            $jsApiParameters['Code']=$list['Code'];
            $jsApiParameters['Ad_Info']['Switch'] = $Switch;
            if($list['Code']==''||$list['Code']!=0){
                $jsApiParameters['Description']='支付失败！';
            }
            $this->ajaxreturn($jsApiParameters);

        }else{
            $systemUserSysNo = I('systemUserSysNo');
            if (preg_match("/^\d*$/", $systemUserSysNo)&&$systemUserSysNo!='') {

            }else{
                return false;
            }
            $data['systemUserSysNo'] = $systemUserSysNo;
            $url  = C('SERVER_HOST')."IPP3Customers/GetPayConfig";
            $list = http( $url, $data);
//			\Think\Log::record(json_encode($list));
            $CustomerSysNO=$list['CustomerSystemUserBase']['CustomerSysNO'];
            $CustomersTopSysNo=$list['CustomerSystemUserBase']['CustomersTopSysNo'];
            $CustomerName=$list['CustomerSystemUserBase']['CustomerName'];
            $Customer_field_one=$list['CustomerSystemUserBase']['Customer_field_one'];
            $DisplayName=$list['CustomerSystemUserBase']['DisplayName'];
            $Switch=$list['CustomerSystemUserBase']['Switch'];

            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
                foreach( $list['PassageWayList'] as $key=>$val){
				if($val['Config']=='SwiftPassageConfig_WX'){
					$Wx_Appid=$list["SwiftPassageConfig_WX"]["WX_APPID"];
//					$Wx_Appid='wx261671a6d70c4db5';;
//					$Wx_Appsecret='0559d78cf2a556b1d7b46988f026114a';
					$Wx_Appsecret=$list["SwiftPassageConfig_WX"]["WX_APPSECRET"];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WXConfig'){
					$Wx_Appid = $list['WXConfig']['APPID'];
					$Wx_Appsecret = $list['WXConfig']['APPSECRET'];
					$PayType= $val['PassageWay'];

				}else if($val['Config']=='WS_PassageConfig_WX'){
					if($list['WS_PassageConfig_WX']['WS_APPIDTwo']==''&&$list['WS_PassageConfig_WX']['WS_APPSECRET']==''){
						$Wx_Appid = $list['WS_PassageConfig_WX']['WX_APPID'];
						$Wx_Appsecret = $list['WS_PassageConfig_WX']['WX_APPSECRET'];
					}else{
						$Wx_Appid = $list['WS_PassageConfig_WX']['WS_APPIDTwo'];
						$Wx_Appsecret = $list['WS_PassageConfig_WX']['WS_APPSECRET'];
					}
					$PayType= $val['PassageWay'];
				
				
				}
				}
                if($PayType==''){
                    return false;
                }
                $coco = new \IntelWxPayApi($Wx_Appid,$Wx_Appsecret);
                try{
                    $userid = $coco->GetWxOpenId();

                }
                catch(Exception $e){
                    \Think\Log::record($e->getMessage());
                }

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

                    }else if($val['Config']=='WS_PassageConfig_ALi'){
                        $Public_Key = $list['WS_PassageConfig_ALi']['ALi_alipay_public_key'];
                        $Private_Key = $list['WS_PassageConfig_ALi']['ALi_merchant_private_key'];
                        $AppID = $list['WS_PassageConfig_ALi']['ALi_APPID'];
                        $PayType= $val['PassageWay'];

                    }else if($val['PassageWay']=='110'){
                        $PayType= $val['PassageWay'];
//                        $arr_url["SystemUserSysNo"]  = $systemUserSysNo;
//                        $select_ali_url = "http://css.yunlaohu.cn/IPP3Customers/SystemUser_Extend_AliList";
//                        $return_ali_url = http($select_ali_url, $arr_url);
//                        if($return_ali_url['Data']['Ali_url']){
//                            header( 'Location:'.$return_ali_url['Data']['Ali_url'] );
//                        }else{
//                            $this->redirect("Base/info",array('message'=>1,'icon_href'=>'ico_hint.png'));
//                            return false;
//                        }
                    }
                }

                if($PayType==''){
                    return false;
                }
                if($AppID){
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
                    $ReturnList = $tools->object_to_array($result);
                    $userid=$ReturnList['alipay_system_oauth_token_response']['user_id'];
                }
            }
            $Query_Data['CustomerTopSysNo'] =  $CustomersTopSysNo;
            $Query_Url = C('SERVER_HOST')."IPP3EFuli/EFuli_Money_TypeList";
            //        $Query_Url = C('SERVER_HOST').'IPP3EFuli/EFuli_Money_TypeList';
            $Query_List = http($Query_Url, $Query_Data);
            foreach($Query_List['Data'] as $key=>$row){
                foreach($row as $k =>$v) {
                    if($k=='Money'){
                        $temp[$key]['SysNo'] = $row['SysNo'];
                        $temp[$key]['Money'] = fee2yuan($v);
                    }
                }
            }
            $this->assign('data',$temp);
            $this->assign('userid',$userid);
            $this->assign('CustomerName',$CustomerName);
            $this->assign('CustomerSysNO',$CustomerSysNO);
            $this->assign('CustomersTopSysNo',$CustomersTopSysNo);
            $this->assign('DisplayName',$DisplayName);
            $this->assign('PayType',$PayType);
            $this->assign('AppId',$AppID);
            $this->assign('systemUserSysNo',$systemUserSysNo);
            $this->assign('Switch',$Switch);
            $this->display();
        }



    }



}                                   