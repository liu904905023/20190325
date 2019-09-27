<?php

namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;
use http\Url;

class RefundSearchController extends Base{
   
	public function refund_search(){
  		R("Base/getMenu");
		//var_dump(session(data));
        $this->display();
    }

    public function platformrefund() {
        if (IS_POST) {

            $out_refund_no = I('Out_refund_no');
            $Out_trade_no = I('Out_trade_no');
            $Ordertype = I('Ordertype');
            $data['SystemUserSysNo']=I('SystemUserSysNo');
            if ($Ordertype == 102) {
                $data['out_refund_no'] = $out_refund_no;
                $url = C('SERVER_HOST') . "Payment/Payments/RefundWxQuery";
            }else if($Ordertype==103){
                $data['out_request_no'] = $out_refund_no;
                $data['out_trade_no'] = $Out_trade_no;
                $url =  C('SERVER_HOST').'IPP3AliPay/AliPayRefundQuery';//yl

            }else if($Ordertype==114){
                $data['ReqModel']['OutRefundNo'] = $out_refund_no;
                $data['Remarks'] = 'WX';
                $url =  C('SERVER_HOST').'IPP3LMFPay/RefundQuery';//yl

            }else if($Ordertype==116){
                $data['ReqModel']['orderNo'] = $out_refund_no;
                $data['PayType'] = 'WX';
                $url =  C('SERVER_HOST').'IPP3YLPay/RefundQuery';//yl

            } else if($Ordertype==117){
                $data['ReqModel']['orderNo'] = $out_refund_no;
                $data['PayType'] = 'AliPay';
                $url =  C('SERVER_HOST').'IPP3YLPay/RefundQuery';//yl

            }else if($Ordertype==118){
                $data['ReqModel']['orderNo'] = $out_refund_no;
                $data['PayType'] = 'QUICKPASS';
                $url =  C('SERVER_HOST').'IPP3YLPay/RefundQuery';//yl

            }else if($Ordertype==108){
                $data['ReqModel']['OutRefundNo'] = $out_refund_no;
                $data['ChannelType'] = 'WX';
                $url =  C('SERVER_HOST').'IPP3WSOrder/WSPayRefundQueryUnion';//ws

            }else if($Ordertype==109){
                $data['ReqModel']['OutRefundNo'] = $out_refund_no;
                $data['ChannelType'] = 'ALI';
                $url =  C('SERVER_HOST').'IPP3WSOrder/WSPayRefundQueryUnion';//ws
            }
            $temp_list = http($url, $data);
            if($temp_list['Code']==0&&$temp_list){
                if ($Ordertype == 108 || $Ordertype == 109) {
                    $info['Code'] = 0;
                    $info['RefundAmount'] = fee2yuan($temp_list['Data']['WxPayData']['m_values']['RefundAmount']);
                    $info['TradeStatus'] = $temp_list['Data']['WxPayData']['m_values']['TradeStatus'];
                    switch ( $temp_list['Data']['WxPayData']['m_values']['TradeStatus']) {
                        case 'succ' :
                            $status = '退款成功';
                            break;
                        case 'fail':
                            $status = '退款失败';
                            break;
                        case 'refunding':
                            $status = '退款处理中';
                            break;
                    }
                    $info['TradeStatus'] =$status;
                    $info['GmtRefundment'] = $temp_list['Data']['WxPayData']['m_values']['GmtRefundment'];
                }
                if ($Ordertype == 116||$Ordertype == 117||$Ordertype == 118) {
                    $info['Code'] = 0;
                    $info['RefundAmount'] = fee2yuan($temp_list['Data']['txnAmt']);
                    $info['TradeStatus'] = $temp_list['Data']['origRespMsg'];
                    $info['GmtRefundment'] =$temp_list['Data']['payTime']? date('Y-m-d H:i:s', strtotime($temp_list['Data']['payTime'])):"";
                } elseif ($Ordertype == 102) {
                    $info['Code'] = 0;
                    switch (  $temp_list['Data']['WxPayData']['m_values']['refund_status_0']) {
                        case 'SUCCESS' :
                            $status = '退款成功';
                            break;
                        case 'REFUNDCLOSE':
                            $status = '退款关闭';
                            break;
                        case 'PROCESSING':
                            $status = '退款处理中';
                            break;
                        case 'CHANGE':
                            $status = '退款异常';
                            break;
                    }
                    $info['TradeStatus'] =$status;
                    $info['RefundAmount'] = fee2yuan($temp_list['Data']['WxPayData']['m_values']['refund_fee_0']);
                    $info['GmtRefundment'] = $temp_list['Data']['WxPayData']['m_values']['refund_success_time_0'];
                } elseif ($Ordertype == 103) {
                    $info['Code'] = 0;
                    $temp_list_1 =json_decode( $temp_list['Data']['WxPayData'],true);
                    switch (  $temp_list_1['alipay_trade_fastpay_refund_query_response']['msg']) {
                        case 'Success' :
                            $status = '退款成功';
                            break;
                        default:
                            $status = '退款异常';
                            break;
                    }
                    $info['TradeStatus'] =$status;
                    $info['RefundAmount'] =  $temp_list_1['alipay_trade_fastpay_refund_query_response']['refund_amount'];
                    $info['GmtRefundment'] ="";
                } elseif ($Ordertype == 114) {
                    $info['Code'] = 0;
                    switch (  $temp_list['Data']['PayData']['orderStatus1']) {
                        case 'REFUNDING' :
                            $status = '退款处理中';
                            break;
                        case 'REFUNDED':
                            $status = '退款成功';
                            break;
                        case 'REFUNDFAIL':
                            $status = '退款失败';
                            break;
                    }
                    $info['TradeStatus'] =$status;
                    $info['RefundAmount'] = ($temp_list['Data']['PayData']['refundAmount1']);
//                    $info['GmtRefundment'] = $temp_list['Data']['PayData']['endTime1'];
                    $info['GmtRefundment'] = "";
                }


            }else{
                $info['Code'] = 1;
            }
            $this->ajaxReturn($info);
            exit();
        }else{
            R("Base/getMenu");
            $post_passageway_data['CustomerSysNo'] =  session('servicestoreno');
            $post_passageway_url = C('SERVER_HOST').'IPP3Customers/CustomerServicePassageWayList';
            $post_passageway_list = http($post_passageway_url, $post_passageway_data);
            if($post_passageway_list){
                foreach ($post_passageway_list as $row=>$val){
                    foreach(json_decode($_COOKIE['passageway_list'],true ) as $k=>$v) {
                        if ($val['Type']==$v['T']) {
                            $data[$val['Type']]=$v['N'];
                        }
                    }
                }
            }
            //var_dump(session(data));
            $this->assign('data', $data);
            $this->display();
        }
    }

  
	public function refundsearch(){
		$Time_Start = empty($_POST['Time_Start'])? $_POST['Time_Start']:$_POST['Time_Start']." 00:00:00";
		$Time_end = empty($_POST['Time_end'])? $_POST['Time_end']:$_POST['Time_end']." 23:59:59";
		$CustomerNames =  $_POST['CustomerNames'];
		$Storename =  $_POST['Storename'];
		$Out_trade_no = $_POST['Out_trade_no'];
		$Ordertype = $_POST['Ordertype'];
		//$refund_fee = yuan2fee($_POST['refund_fee']);
		$Create_Time_Start =$_POST['Create_Time_Start'];
		$Create_Time_end = $_POST['Create_Time_end'];
		
		$data = array(
			"Transaction_id"=>$Transaction_id,
			"Out_trade_no"=>$Out_trade_no,
			"Time_Start"=>$Time_Start,
			"Time_end"=>$Time_end,
			"Create_Time_Start"=>$Create_Time_Start,
			"Create_Time_end"=>$Create_Time_end,
			"CustomerName"=>$CustomerNames,
			"Customer"=>$Storename,
			"Pay_Type"=>$Ordertype

		);


		$flag =  session(flag);//�������̻�0 ��Ա��1
		$type=3;
		if($flag ==1){
		$type =  staffstoreorservice(session(SysNO));
		}
		

		if(session(data)['CustomersType']==0&$flag==0){
		$data['CustomersTopSysNo']=session(SysNO);
		}
		if(session(data)['CustomersType']==1&$flag==0){
		$data['CustomerSysNo']=session(SysNO);
		}
		if($type==1){
		$data['SystemUserSysNo']=session(SysNO);
		}
		if($type==0){
            $info['totalCount']=0;
            $this->ajaxReturn($info,json);
            exit;
		}
		$url=C('SERVER_HOST')."IPP3Order/IPP3RMA_RequestSP";


		$data['PagingInfo']['PageSize'] = $_POST['PageSize'];
		$data['PagingInfo']['PageNumber'] = $_POST['PageNumber'];
        $data = json_encode( $data );
//		var_dump($data);echo "\n".$url ;exit;
        $head = array(
            "Content-Type:application/json;charset=UTF-8",
            "Content-length:" . strlen( $data ),
            "X-Ywkj-Authentication:" . strlen( $data ),
        );
        $list = http_request($url,$data,$head);
		$list = json_decode($list,true);
//		var_dump($list);
		foreach ($list['model'] as $row=>$val){
		$info['model'][$row]['SystemUserSysNo']=$val['SystemUserSysNo'];
		$info['model'][$row]['Pay_Types']=$val['Pay_Type'];
		$info['model'][$row]['LoginName']=$val['LoginName'];
		$info['model'][$row]['DisplayName']=$val['DisplayName'];
		$info['model'][$row]['Out_trade_no']=$val['Out_trade_no'];
		$info['model'][$row]['Out_refund_no']=$val['Out_refund_no'];
		$info['model'][$row]['Pay_Type']=CheckOrderType($val['Pay_Type']);
		$info['model'][$row]['Time_Start']=$val['Time_Start'];
		$info['model'][$row]['customername']=$val['CustomerName'];
		$info['model'][$row]['Total_fee']=fee2yuan($val['Total_fee']);
		$info['model'][$row]['refund_fee']=fee2yuan($val['Refund_fee']);
		//$info['model'][$row]['CreateTime']=date("y-m-d H:i:s","1465721624910");
		$info['model'][$row]['CreateTime']=$val['CreateTime'];
		$totalfee =$totalfee+ fee2yuan($val['refund_fee']);
		
		}
		$info['totalCount'] =$list['totalCount'];
		$info['totalfee'] =$totalfee;


        $this->ajaxReturn($info,json);
	
	
	
	}

}
