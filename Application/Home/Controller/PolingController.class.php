<?php

namespace Home\Controller;

use Think\Controller;

class PolingController extends Controller{

    public function checkstatus(){
		$Data['out_trade_no'] = I('ordernum');
		$Data['systemUserSysNo'] = 2;
		$type=I('type');
        $start_time=time();
		for($i=0;$i<1000;$i++){
			session_write_close();
			sleep(5);
            set_time_limit(0);
            if($type==102){

                $Url = C('SERVER_HOST')."Payment/Payments/QueryWxOrder";//微信

                $QueryWxOrder = $this->http($Url,$Data);
                $end_time = time();
                if($QueryWxOrder['Data']['WxPayData']['m_values']['trade_state']=="SUCCESS"){
                    $this->ajaxreturn(0);
                    exit;

                }


                if($end_time-$start_time>=55){
                    $Url = C('SERVER_HOST')."Payment/Payments/WXCloseOrder";
                    $WxCloseOrder = $this->http($Url,$Data);
                    if($WxCloseOrder['Code']==0){
                        $this->ajaxreturn(1);
                        exit;

                    }
                    break;
                }

            }
            if($type==103){
                $Url = C('SERVER_HOST')."IPP3AliPay/AliPayquery";//支付宝
                $QueryAliOrder = $this->http($Url,$Data);
                $end_time = time();
                if($QueryAliOrder['alipay_trade_query_response']['trade_status']=="TRADE_SUCCESS"){
                    $this->ajaxreturn(0);
                    exit;

                }
                $aaa=$end_time-$start_time;
                \Think\Log::record("轮询开始".$aaa );
                if($end_time-$start_time>=55){
                    $Url = C('SERVER_HOST')."IPP3AliPay/AliPayCancel";
                    $AliCloseOrder = $this->http($Url,$Data);
                    if($AliCloseOrder['Code']==0){
                        $this->ajaxreturn(1);
                        exit;

                    }
                    exit;
                }

            }





		}
		
    }


	private function http($Url,$Data){

		$Data = json_encode($Data);
		\Think\Log::record("请求".$Data."////".$Url );
		$head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $Data ),
            //"X-Ywkj-Authentication:" . strlen( $Data1 ),
        );
		$list = http_request($Url,$Data,$head);
		\Think\Log::record("返回".$list );
		$list = json_decode($list,true);
		return $list;
	
	}

}
