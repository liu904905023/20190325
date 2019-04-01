<?php
namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;

class OrderController extends Base {

    public function order_search() {
        R("Base/getMenu");
        $this->display();
    }

    public function ordersearch() {//交易订单查询
        $Time_Start = $_POST['Time_Start'];
        $Time_end = $_POST['Time_End'];
        $out_trade_no = I('out_trade_no', "");

        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = $_POST['PageNumber'];
        $PageSize = $_POST['PageSize'];
        $CustomerNames = $_POST['CustomerNames'];
        $Ordertype = $_POST['Ordertype'];
        $ButtonType = $_POST['ButtonType'];
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "Pay_Type" => $Ordertype,

        );
        $flag = session('flag');//服务商商户0 或员工1
        $type = session('servicestoretype');//员工的服务商的类型 0为服务  1为商户
//        if (session('data')['CustomersType'] == 0 & $type == 0) {
//            $stafftype = 0;
//        }

        if ((session('data')['CustomersType'] == 0 &$flag == 0)|| $type == 0&$flag == 1) {//服务商或者服务商员工登陆 必须填写商户名进行查询，不需要传递CustomerSysNo
            $data['Customer'] = $_POST['Customer'];
            $data['CustomerName'] = $_POST['CustomerNames'];
        }

        if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆 传入主键
            $data['CustomersTopSysNo'] = session('SysNO');
            $data['CustomerName'] = $_POST['CustomerNames'];
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登录
            $data['LoginName'] = $_POST['Customer'];
            $data['DisplayName'] = $_POST['CustomerNames'];
            $data['CustomerSysNo'] = session('SysNO');

        }
        if($type==1&$flag==1){
            $data['Customer'] = $_POST['Customer'];
            $data['CustomerName'] = $_POST['CustomerNames'];
        }

        if ($SystemUserSysNo != 'null') {

            $data['SystemUserSysNo'] = $SystemUserSysNo;

        } else {

            if (session('data')['CustomersType'] == 1) {  //商户登陆 查询全部员工的 传$CustomerSysNo
                $data['CustomerSysNo'] = session('SysNO');
            }
            if ($flag == 1 & $type == 1) {
                $data['SystemUserSysNo'] = session('SysNO');//员工登陆 查全部 传$CustomerSysNo

            }
        }
        if ($ButtonType == 0) {
            if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商
                $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListCustomerSP";
            } else if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户
                if ($SystemUserSysNo != "null") {
                    $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListShopUserSP";

                } else {
                    $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListShopSP";
                }

            } else if ($type == 0 & $flag == 1) {//服务商员工
                $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListCustomerUserSP";
            } else if ($type == 1 & $flag == 1) {//商户员工
                $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListShopUserSP";
            }
            if ($type == 0 & $flag == 1) {//服务商员工
                $data['SystemUserTopSysNo'] = session('SysNO');
                $data['CustomersTopSysNo'] = session('servicestoreno');
            }


        } else if ($ButtonType == 1) {

            $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListcollect";
            if ($type == 0 & $flag == 1) {//服务商员工登陆 传入主键
                $data['SystemUserTopSysNo'] = session('SysNO');
                $data['CustomersTopSysNo'] = session('servicestoreno');
            }
        }



        $data['PagingInfo']['PageSize'] = $PageSize;
        $data['PagingInfo']['PageNumber'] = $PageNumber;
//        var_dump($data);
//        echo $url;
//        exit;
        $list = http($url,$data);

        if ($ButtonType == 0) {
            foreach ($list['model'] as $row => $val) {
                $info['model'][$row]['SysNo'] = $val['SysNo'];
                $info['model'][$row]['loginname'] = $val['LoginName'];
                $info['model'][$row]['displayname'] = $val['DisplayName'];
                $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
                $info['model'][$row]['Pay_Type'] =CheckOrderType($val['Pay_Type']);
                $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
                $info['model'][$row]['fee'] = fee2yuan($val['fee']);
                $info['model'][$row]['Time_Start'] = $val['Time_Start'];
                $info['model'][$row]['CustomerName'] = $val['CustomerName'];
            }
            $info['totalCount'] = $list['totalCount'];
            $info['ButtonType'] = $ButtonType;
            if (session(flag) == 1) {
                $list['flag'] = session('servicestoretype');
            }
        } else if ($ButtonType == 1) {
            foreach ($list['model'] as $row => $val) {
                $info['model'][$row]['customername'] = $val['CustomerName'];
                $info['model'][$row]['loginname'] = $val['LoginName'];
                $info['model'][$row]['total_fee'] = fee2yuan($val['Total_fee']);
                $info['model'][$row]['fee'] = fee2yuan($val['fee']);
                $info['model'][$row]['cash_fee_type'] = $val['Cash_fee_type'];
                $info['model'][$row]['tradecount'] = $val['Tradecount'];
                $info['model'][$row]['displayname'] = $val['DisplayName'];
            }
            $info['totalCount'] = $list['totalCount'];
            $info['ButtonType'] = $ButtonType;
            if (session(flag) == 1) {
                $list['flag'] = session('servicestoretype');
            }

        }
        $this->ajaxReturn($info);

    }

    public function platform_order_search() {
        R("Base/getMenu");
        $Retrun_GetPassageWay = $this->QueryNowPassageWay('WX');
        $this->assign('NowPassageWay', $Retrun_GetPassageWay['NowPassageWay']);
        $this->display();
    }
    public function platformsearch(){

        $Data['Out_Trade_No'] = I('out_trade_no','','trim');
        $Data['Transactionid'] = I('transactionid','','trim');
        $Return_List = $this->AllWxPlatformOrder($Data);
        $this->ajaxreturn($Return_List);
    }

    private function AllWxPlatformOrder($Post_Data) {//平台交易订单查询
        $Retrun_GetPassageWay = $this->QueryNowPassageWay('WX');
       switch($Retrun_GetPassageWay['NowPassageWay']){
           case 102:
               $Data['out_trade_no'] =$Post_Data['Out_Trade_No'];
               $Data['Transaction_id'] =$Post_Data['Transactionid'];
               $Data['SystemUserSysNo'] = session('SysNO');
               $Url = C('SERVER_HOST') . 'Payment/Payments/QueryWxOrder';
               $List = http($Url, $Data);
               if($List['Data']['WxPayData']['m_values']['result_code']=='SUCCESS'){
                   $Code = 0;
               }else{
                   $Code = 1;
               }

               $Out_trade_no = $List['Data']['WxPayData']['m_values']['out_trade_no'];//订单号
               $Trade_State = $List['Data']['WxPayData']['m_values']['trade_state'];//订单状态
               $Transaction_Id = $List['Data']['WxPayData']['m_values']['transaction_id'];//订单状态
               switch ($Trade_State) {
                   case SUCCESS:$Status = "支付成功";break;
                   case REFUND:$Status = "转入退款";break;
                   case NOTPAY:$Status = "未支付";break;
                   case CLOSED:$Status = "已关闭";break;
                   case REVOKED:$Status = "已撤销（刷卡支付）";break;
                   case USERPAYING:$Status = "用户支付中";break;
                   case PAYERROR:$Status = "支付失败";break;
               }
               $TotalFee= fee2yuan($List['Data']['WxPayData']['m_values']['total_fee']);//总额
               if ($List['Data']['WxPayData']['m_values']['time_end']) {
                   $Time_End = date("Y-m-d H:i:s", strtotime($List['Data']['WxPayData']['m_values']['time_end']));//时间
               } else {
                   $Time_End = "无";
               }
               break;
           case 104:
               $Data['out_trade_no'] = $Post_Data['Transactionid'];
               $Data['transaction_id'] = $Post_Data['Out_Trade_No'];
               $Data['systemUserSysNo'] = session('SysNO');
               $Data['Pay_Type'] = 'WX';
               $Url = C('SERVER_HOST') . 'IPP3Swiftpass/OrderQueryApi';
               $List = http($Url, $Data);
//               var_dump($List);
//               exit();

               if(!strpos($List['Data']['WxPayData']['trade_type'],"weixin")){
                   $Code = 1;
               }else{
                   $Code = 0;
               }
               $Trade_State = $List['Data']['WxPayData']['trade_state'];
               switch ($Trade_State) {
                   case SUCCESS:$Status = "支付成功";break;
                   case REFUND:$Status = "转入退款";break;
                   case NOTPAY:$Status = "未支付";break;
                   case CLOSED:$Status = "已关闭";break;
                   case REVOKED:$Status = "已撤销";break;
                   case USERPAYING:$Status = "用户支付中";break;
                   case PAYERROR:$Status = "支付失败(其他原因，如银行返回失败)";break;
               }
               $TotalFee =  fee2yuan($List['Data']['WxPayData']['total_fee']);
               $Out_trade_no =  $List['Data']['WxPayData']['transaction_id'];
               $Transaction_Id =  $List['Data']['WxPayData']['out_trade_no'];
               $Time_End_Temp =  $List['Data']['WxPayData']['time_end'];
               $Time_End = date("Y-m-d h:i:s",mktime(substr($Time_End_Temp, 8, 2),substr($Time_End_Temp, 10, 2),substr($Time_End_Temp, 12, 2),substr($Time_End_Temp, 4, 2) ,substr($Time_End_Temp, 6, 2),substr($Time_End_Temp, 0,4)));//hour,minute,second,month,day,year

               break;
           case 106:
               $Data['out_trade_no'] = $Post_Data['Transactionid'];
               $Data['transaction_id'] = $Post_Data['Out_Trade_No'];
               $Data['systemUserSysNo'] = session('SysNO');
               $Data['Pay_Type'] = 'WX';
               $Url = C('SERVER_HOST') . 'IPP3Swiftpass/OrderQueryApi';
               $List = http($Url, $Data);
               if(!strpos($List['Data']['WxPayData']['trade_type'],"weixin")){
                   $Code = 1;
               }else{
                   $Code = 0;
               }
               $Trade_State = $List['Data']['WxPayData']['trade_state'];
               switch ($Trade_State) {
                   case SUCCESS:$Status = "支付成功";break;
                   case REFUND:$Status = "转入退款";break;
                   case NOTPAY:$Status = "未支付";break;
                   case CLOSED:$Status = "已关闭";break;
                   case REVOKED:$Status = "已撤销";break;
                   case USERPAYING:$Status = "用户支付中";break;
                   case PAYERROR:$Status = "支付失败(其他原因，如银行返回失败)";break;
               }
               $TotalFee =  fee2yuan($List['Data']['WxPayData']['total_fee']);
               $Out_trade_no =  $List['Data']['WxPayData']['transaction_id'];
               $Transaction_Id =  $List['Data']['WxPayData']['out_trade_no'];
               $Time_End_Temp =  $List['Data']['WxPayData']['time_end'];
               $Time_End = date("Y-m-d h:i:s",mktime(substr($Time_End_Temp, 8, 2),substr($Time_End_Temp, 10, 2),substr($Time_End_Temp, 12, 2),substr($Time_End_Temp, 4, 2) ,substr($Time_End_Temp, 6, 2),substr($Time_End_Temp, 0,4)));//hour,minute,second,month,day,year
               break;
           case 108:
               if($Post_Data['Transactionid']){
                   $Data['ReqModel']['OutTradeNo'] =$Post_Data['Transactionid'];
               }else{

                   $QueryYhList = $this->StaffQueryOrder($Post_Data);
//                    var_dump($QueryYhList);
//                    exit();
                   $Data['ReqModel']['OutTradeNo'] =$QueryYhList['Transaction_id'];
               }
               $Data['SystemUserSysNo'] = session('SysNO');
               $Data['ChannelType'] = 'WX';
               $Url = C('SERVER_HOST') . 'IPP3WSOrder/WSPayQuery';
               $List = http($Url, $Data);
               if($List['Data']['WxPayData']['m_values']['ChannelType']!='WX'){
                   $Code = 1;
               }else{
                   $Code = 0;
               }
               $Trade_State = $List['Data']['WxPayData']['m_values']['TradeStatus'];//订单状态
               switch ($Trade_State) {
                   case 'succ':$Status = "支付成功";$Trade_State='SUCCESS';break;
                   case 'fail':$Status = "失败";break;
                   case 'paying':$Status = "支付中";break;
                   case 'closed':$Status = "已关单";break;
                   case 'cancel':$Status = "已撤消";break;
               }
               $TotalFee =  fee2yuan($List['Data']['WxPayData']['m_values']['TotalAmount']);
               $Out_trade_no =  $List['Data']['WxPayData']['m_values']['PayChannelOrderNo'];
               $Transaction_Id =  $List['Data']['WxPayData']['m_values']['OutTradeNo'];
               $Time_End =  $List['Data']['WxPayData']['m_values']['GmtPayment'];
               break;
           case 112:
               $Data['ReqModel']['orgOrderNo'] = $Post_Data['Out_Trade_No'];
               $Data['ReqModel']['orderDt'] = date("Y-m-d",time());
               $Data['ReqModel']['orderNo'] = $Post_Data['Transactionid'];
               $Data['SystemUserSysNo'] = session('SysNO');
               $Data['Remarks'] = 'WX';
               $Url = C('SERVER_HOST') . 'IPP3RuralCredit/RuralCreditOrderQuery';
               $List = http($Url, $Data);
               switch ($List['Data']['paySt']) {
                   case '1':$Status = "待支付";break;
                   case '2':$Status = "支付成功";break;
                   case '3':$Status = "支付失败";break;
                   case '4':$Status = "已关闭";break;
               };
               $TotalFee = fee2yuan($List['Data']['transAmt']);
               $Out_trade_no =  $List['Data']['orgOrderNo'];
               $Transaction_Id =  $List['Data']['orderNo'];
               $Time_End = "";
               $Code = $List['Code'];
       }
        $Info['Code'] = $Code;
        $Info['Trade_State'] = $Trade_State;
        $Info['Out_Trade_No'] = $Out_trade_no;
        $Info['Transaction_Id'] = $Transaction_Id;
        $Info['Total_Fee'] = $TotalFee;
        $Info['Time'] = $Time_End;
        $Info['Status'] = $Status;
        $Info['TypeName'] = CheckOrderType($Retrun_GetPassageWay['NowPassageType']);
        $Info['Type'] = $Retrun_GetPassageWay['NowPassageWay'];

        return $Info;

    }

    public function order_search_alipay() {
        R("Base/getMenu");
        $Retrun_GetPassageWay = $this->QueryNowPassageWay('AliPay');
        $this->assign('NowPassageWay', $Retrun_GetPassageWay['NowPassageWay']);
        $this->display();
    }

    public function ali_search() {
        $Data['Out_Trade_No'] = I('out_trade_no','','trim');
        $Data['Transactionid'] = I('transactionid','','trim');
        $Return_List = $this->AllAliPlatformOrder($Data);

        $this->ajaxreturn($Return_List);
    }
    private function AllAliPlatformOrder($Post_Data) {//平台交易订单查询

        $Retrun_GetPassageWay = $this->QueryNowPassageWay('AliPay');
        switch($Retrun_GetPassageWay['NowPassageWay']){
            case 102:
                $Data['out_trade_no'] =$Post_Data['Out_Trade_No'];
                $Data['Transaction_id'] =$Post_Data['Transactionid'];
                $Data['SystemUserSysNo'] = session('SysNO');
                $Url = C('SERVER_HOST') . 'IPP3AliPay/AliPayquery';
                $List = http($Url, $Data);
                $Code = $List['Code'];
                $List = json_decode($List['Data']['WxPayData'], true);
                $Out_trade_no = $List['alipay_trade_query_response']['out_trade_no'];//订单号
                $Transaction_Id = $List['alipay_trade_query_response']['trade_no'];//平台订单号
                $Trade_State = $List['alipay_trade_query_response']['trade_status'];//订单状态
                switch ($Trade_State) {
                    case WAIT_BUYER_PAY:$Status = "交易创建，等待买家付款";break;
                    case TRADE_CLOSED:$Status = "未付款交易超时关闭，或支付完成后全额退款";break;
                    case TRADE_SUCCESS:$Status = "交易支付成功";$Trade_State='SUCCESS';break;
                    case TRADE_FINISHED:$Status = "交易结束，不可退款";break;
                }
                $TotalFee= $List['alipay_trade_query_response']['total_amount'];//总额
                $Time_End = $List['alipay_trade_query_response']['send_pay_date'];//时间
                break;
            case 104:
                $Data['out_trade_no'] = $Post_Data['Transactionid'];
                $Data['transaction_id'] = $Post_Data['Out_Trade_No'];
                $Data['systemUserSysNo'] = session('SysNO');
                $Data['Pay_Type'] = 'AliPay';
                $Url = C('SERVER_HOST') . 'IPP3Swiftpass/OrderQueryApi';
                $List = http($Url, $Data);
                if(!strpos($List['Data']['WxPayData']['trade_type'],"alipay")){
                    $Code = 1;
                }else{
                    $Code = 0;
                }
                $Trade_State = $List['Data']['WxPayData']['trade_state'];
                switch ($Trade_State) {
                    case SUCCESS:$Status = "支付成功";break;
                    case REFUND:$Status = "转入退款";break;
                    case NOTPAY:$Status = "未支付";break;
                    case CLOSED:$Status = "已关闭";break;
                    case REVOKED:$Status = "已撤销";break;
                    case USERPAYING:$Status = "用户支付中";break;
                    case PAYERROR:$Status = "支付失败(其他原因，如银行返回失败)";break;
                }
                $TotalFee =  fee2yuan($List['Data']['WxPayData']['total_fee']);
                $Out_trade_no=  $List['Data']['WxPayData']['transaction_id'];
                $Transaction_Id=  $List['Data']['WxPayData']['out_trade_no'];
                $Time_End_Temp =  $List['Data']['WxPayData']['time_end'];
                $Time_End = date("Y-m-d h:i:s",mktime(substr($Time_End_Temp, 8, 2),substr($Time_End_Temp, 10, 2),substr($Time_End_Temp, 12, 2),substr($Time_End_Temp, 4, 2) ,substr($Time_End_Temp, 6, 2),substr($Time_End_Temp, 0,4)));//hour,minute,second,month,day,year

                break;
            case 106:
                $Data['out_trade_no'] = $Post_Data['Transactionid'];
                $Data['transaction_id'] = $Post_Data['Out_Trade_No'];
                $Data['systemUserSysNo'] = session('SysNO');
                $Data['Pay_Type'] = 'AliPay';
                $Url = C('SERVER_HOST') . 'IPP3Swiftpass/OrderQueryApi';
                $List = http($Url, $Data);
                if(!strpos($List['Data']['WxPayData']['trade_type'],"alipay")){
                    $Code = 1;
                }else{
                    $Code = 0;
                }
                $Trade_State = $List['Data']['WxPayData']['trade_state'];
                switch ($Trade_State) {
                    case SUCCESS:$Status = "支付成功";break;
                    case REFUND:$Status = "转入退款";break;
                    case NOTPAY:$Status = "未支付";break;
                    case CLOSED:$Status = "已关闭";break;
                    case REVOKED:$Status = "已撤销";break;
                    case USERPAYING:$Status = "用户支付中";break;
                    case PAYERROR:$Status = "支付失败(其他原因，如银行返回失败)";break;
                }
                $TotalFee =  fee2yuan($List['Data']['WxPayData']['total_fee']);
                $Out_trade_no=  $List['Data']['WxPayData']['transaction_id'];
                $Transaction_Id=  $List['Data']['WxPayData']['out_trade_no'];
                $Time_End_Temp =  $List['Data']['WxPayData']['time_end'];
                $Time_End = date("Y-m-d h:i:s",mktime(substr($Time_End_Temp, 8, 2),substr($Time_End_Temp, 10, 2),substr($Time_End_Temp, 12, 2),substr($Time_End_Temp, 4, 2) ,substr($Time_End_Temp, 6, 2),substr($Time_End_Temp, 0,4)));//hour,minute,second,month,day,year
                break;
            case 108:
                if($Post_Data['Transactionid']){
                    $Data['ReqModel']['OutTradeNo'] =$Post_Data['Transactionid'];
                }else{
                    $QueryYhOrder['Out_trade_no'] = $Post_Data['Out_Trade_No'];
                    $QueryYhOrder['SystemUserSysNo'] = session('SysNO');
                    $QueryYhUrl = C('SERVER_HOST') . 'IPP3Order/So_MasterQuery';
                    $QueryYhList = http($QueryYhUrl, $QueryYhOrder);
                    $Data['ReqModel']['OutTradeNo'] =$QueryYhList['Transaction_id'];
                }
                $Data['SystemUserSysNo'] = session('SysNO');
                $Data['ChannelType'] = 'ALI';
                $Url = C('SERVER_HOST') . 'IPP3WSOrder/WSPayQuery';
                $List = http($Url, $Data);
                if($List['Data']['WxPayData']['m_values']['ChannelType']!='ALI'){
                    $Code = 1;
                }else{
                    $Code = 0;
                }
                $Trade_State = $List['Data']['WxPayData']['m_values']['TradeStatus'];//订单状态
                switch ($Trade_State) {
                    case 'succ':$Status = "支付成功";$Trade_State='SUCCESS';break;
                    case 'fail':$Status = "失败";break;
                    case 'paying':$Status = "支付中";break;
                    case 'closed':$Status = "已关单";break;
                    case 'cancel':$Status = "已撤消";break;
                }
                $TotalFee =  fee2yuan($List['Data']['WxPayData']['m_values']['TotalAmount']);
                $Out_trade_no =  $List['Data']['WxPayData']['m_values']['PayChannelOrderNo'];
                $Transaction_Id =  $List['Data']['WxPayData']['m_values']['OutTradeNo'];
                $Time_End =  $List['Data']['WxPayData']['m_values']['GmtPayment'];
                break;
            case 112:
                $Data['ReqModel']['orgOrderNo'] = $Post_Data['Out_Trade_No'];
                $Data['ReqModel']['orderDt'] = date("Y-m-d",time());
                $Data['ReqModel']['orderNo'] = $Post_Data['Transactionid'];
                $Data['SystemUserSysNo'] = session('SysNO');
                $Data['Remarks'] = 'Alipay';
                $Url = C('SERVER_HOST') . 'IPP3RuralCredit/RuralCreditOrderQuery';
                $List = http($Url, $Data);
                switch ($List['Data']['paySt']) {
                    case '1':$Status = "待支付";break;
                    case '2':$Status = "支付成功";break;
                    case '3':$Status = "支付失败";break;
                    case '4':$Status = "已关闭";break;
                };
                $TotalFee = fee2yuan($List['Data']['transAmt']);
                $Out_trade_no =  $List['Data']['orgOrderNo'];
                $Transaction_Id =  $List['Data']['orderNo'];
                $Time_End = "";
                $Code = $List['Code'];
                break;
        }

            $Info['Code'] = $Code;
            $Info['Trade_State'] = $Trade_State;
            $Info['Out_Trade_No'] = $Out_trade_no;
            $Info['Transaction_Id'] = $Transaction_Id;
            $Info['Total_Fee'] = $TotalFee;
            $Info['Time'] = $Time_End;
            $Info['Status'] = $Status;
            $Info['TypeName'] = CheckOrderType($Retrun_GetPassageWay['NowPassageType']);
            $Info['Type'] = $Retrun_GetPassageWay['NowPassageWay'];


        return $Info;

    }


    public function AliSupplyOrder() {
        $Data['Out_Trade_No'] = I('out_trade_no','','trim');
        $Data['Transactionid'] = I('transactionid','','trim');
        $Retrun_GetPassageWay = $this->QueryNowPassageWay('AliPay');
        $YhQueryResult = $this->StaffQueryOrder($Data);
        if (empty($YhQueryResult['SysNo'])) {
            if($Data['Transactionid']==''&&$Retrun_GetPassageWay['NowPassageWay']=='108'){
                $Description = "请用平台订单号进行补单!";
            }else{
                $ActStatus = $this->AddAliOrder($Data,$Retrun_GetPassageWay['NowPassageWay']);
                if ($ActStatus['Code']==0&&$ActStatus) {
                    $Description = "补单成功!";
                } else {
                    $Description = "补单失败!";
                }
            }
        } else {
            $Description = "订单已存在,请勿重复补单!";
        }

        $this->ajaxReturn($Description);

    }





    private function AddAliOrder($Data,$Type) {

        if($Type == 102){
            $SupplyOrderData['Out_trade_no']=$Data['Out_Trade_No'];
            $SupplyOrderData['Transaction_id']=$Data['Transactionid'];
            $SupplyOrderData['systemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3AliPay/AliPaySupplyOrder";
        }else if ($Type == 104) {
            $SupplyOrderData['out_trade_no']=$Data['Transactionid'];
            $SupplyOrderData['transaction_id']=$Data['Out_Trade_No'];
            $SupplyOrderData['Pay_Type']='AliPay';
            $SupplyOrderData['systemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3Swiftpass/SupplyOrder";
        }else if ($Type == 106) {
            $SupplyOrderData['out_trade_no']=$Data['Transactionid'];
            $SupplyOrderData['transaction_id']=$Data['Out_Trade_No'];
            $SupplyOrderData['Pay_Type']='AliPay';
            $SupplyOrderData['systemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3Swiftpass/SupplyOrder";
        }else if ($Type == 108) {
            $SupplyOrderData['OutTradeNo'] = $Data['Transactionid'];
            $SupplyOrderData['ChannelType']='ALI';
            $SupplyOrderData['SystemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3BKMerchantTrade/MCHPaySupplyOrder";
        }
//        var_dump(json_encode($SupplyOrderData));
//        echo $SupplyOrderUrl;
//        exit();
        $list = http($SupplyOrderUrl, $SupplyOrderData);
//        var_dump($list);
//        exit();
        return $list;
    }

    public function showtotalfee() {
        $Time_Start = $_POST['Time_Start'];
        $Time_end = $_POST['Time_End'];
        $out_trade_no = I('out_trade_no', "");
        $SystemUserSysNo = I('SystemUserSysNo', "");
        $PageNumber = $_POST['PageNumber'];
        $PageSize = $_POST['PageSize'];
        $CustomerNames = $_POST['CustomerNames'];
        $Customer = $_POST['Customer'];
        $Ordertype = $_POST['Ordertype'];
        $ButtonType = $_POST['ButtonType'];
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no, "CustomerName" => $CustomerNames, "Pay_Type" => $Ordertype, "Customer" => $Customer

        );
        $flag = session('flag');
        if (session('data')['CustomersType'] == 0 & $flag == 0) {//服务商登陆 传入主键
            $data['CustomersTopSysNo'] = session('SysNO');
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {//商户登陆 传入主键
			if($SystemUserSysNo!='null'){
				$data['SystemUserSysNo'] =$SystemUserSysNo;
			}else{
				$data['CustomerSysNo'] = session('SysNO');
			}
        }
        if (session('servicestoretype') == 0 & $flag == 1) {
            $data['CustomersTopSysNo'] = session('servicestoreno');
            $data['SystemUserTopSysNo'] = session('SysNO');
        }
        if (session('servicestoretype') == 1 & $flag == 1) {
            $data['SystemUserSysNo'] = session('SysNO');
        }
        if (session('data')['CustomersType'] == 1 & $flag == 0) {
			if($SystemUserSysNo!='null'){
				$url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListcollect";
				$list = http($url,$data);

				foreach ($list['model'] as $row => $val) {
					$info['total']['Total_fee'] = fee2yuan($val['Total_fee']);
					$info['total']['Cash_fee'] = fee2yuan($val['fee']);
					$info['total']['Tradecount'] = $val['Tradecount'];
				}
			}else{
            $url = C('SERVER_HOST') . "IPP3Order/IPP3Order_Group_Shop";
            $totalcount = http($url,$data);
            $info['total']['Total_fee'] = fee2yuan($totalcount['Total_fee']);
            $info['total']['Cash_fee'] = fee2yuan($totalcount['Cash_fee']);
            $info['total']['Tradecount'] = $totalcount['Tradecount'];
			}
        } else if (session('data')['CustomersType'] == 0 & $flag == 0) {
            $url = C('SERVER_HOST') . "IPP3Order/IPP3Order_Group_Customer";
            $totalcount = http($url,$data);
            $info['total']['Total_fee'] = fee2yuan($totalcount['Total_fee']);
            $info['total']['Cash_fee'] = fee2yuan($totalcount['Cash_fee']);
            $info['total']['Tradecount'] = $totalcount['Tradecount'];
//			var_dump($info);exit;
        } else if (session('servicestoretype') == 0 & session('flag') == 1) {
            $url = C('SERVER_HOST') . "IPP3Order/IPP3Order_Group_CustomerUser";
            $totalcount = http($url,$data);
            $info['total']['Total_fee'] = fee2yuan($totalcount['Total_fee']);
            $info['total']['Cash_fee'] = fee2yuan($totalcount['Cash_fee']);
            $info['total']['Tradecount'] = $totalcount['Tradecount'];

        }else if (session('servicestoretype') == 1 & session('flag') == 1) {
            $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderListcollect";
            $list = http($url,$data);

            foreach ($list['model'] as $row => $val) {
                $info['total']['Total_fee'] = fee2yuan($val['Total_fee']);
                $info['total']['Cash_fee'] = fee2yuan($val['fee']);
                $info['total']['Tradecount'] = $val['Tradecount'];
            }

        }
//				\Think\Log::record(json_encode($data));

        $this->ajaxreturn($info);


    }

	public function orderbyday(){

		R("Base/getMenu");
        $this->display();
    }
	public function order_by_day(){
        if (session('data')['CustomersType'] == 1 & session('flag') == 0) {

        }else{
            return false;
        }
        $data['CustomerSysNo']=session('SysNO');
        $data['LoginName']=I('LoginName');
        $data['DisplayName']=I('DisplayName');
        $data['Time_End']=I('Time_End');
        $data['Time_Start']=I('Time_Start');
        $data['Pay_Type']=I('Ordertype');
        $data['Pay_Type']=I('Ordertype');
        $data['PagingInfo']['PageSize'] = I(PageSize);
        $data['PagingInfo']['PageNumber'] = I(PageNumber);
        $url = C('SERVER_HOST') ."/IPP3Order/IPP3OrderByDayList";
        $list = http($url,$data);
        foreach ($list['model'] as $row => $val) {
            $info['model'][$row]['DisplayName'] = $val['DisplayName'];
            $info['model'][$row]['LoginName'] = $val['LoginName'];
            $info['model'][$row]['OrderTime'] = $val['OrderTime'];
            $info['model'][$row]['TradeCount'] = $val['TradeCount'];
            $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
            $info['model'][$row]['Refund_fee'] = fee2yuan($val['Refund_fee']);
            $info['model'][$row]['Cash_refund_fee'] = fee2yuan($val['Cash_refund_fee']);
			$info['model'][$row]['Money'] = fee2yuan($val['Money']);
            $info['model'][$row]['Fee'] = fee2yuan($val['Fee']);
            $info['model'][$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);

        }
        $info['totalCount'] = $list['totalCount'];
        $this->ajaxreturn($info);
    }


	//add by qiwei 20170301 微信补单
    public function WXSupplyOrder() {
        $Data['Out_Trade_No'] = I('out_trade_no','','trim');
        $Data['Transactionid'] = I('transactionid','','trim');

        $YhQueryResult = $this->StaffQueryOrder($Data);//订单退款查询

//        var_dump($YhQueryResult);
//        exit();
        $Retrun_GetPassageWay = $this->QueryNowPassageWay('WX');

            if (empty($YhQueryResult['SysNo'])) {
                if($Data['Transactionid']==''&&$Retrun_GetPassageWay['NowPassageWay']=='108'){
                    $Description = "请用平台订单号进行补单!";
                }else{
                    $ActStatus = $this->AddWxOrder($Data,$Retrun_GetPassageWay['NowPassageWay']);
                    if ($ActStatus['Code']==0&&$ActStatus) {
                        $Description = "补单成功!";
                    } else {
                        $Description = "补单失败!";
                    }
                }


            } else {
                $Description = "订单已存在,请勿重复补单!";
            }


        $this->ajaxReturn($Description);
    }

    private function StaffQueryOrder($Data) {

        $data = array("Out_trade_no" => $Data['Out_Trade_No'],"Transaction_id"=>$Data['Transactionid'], "SystemUserSysNo" => session('SysNO')
        );
        $url = C('SERVER_HOST') . "IPP3Order/So_MasterQuery";
        $list = http($url, $data);

        return $list;
    }

    private function AddWxOrder($Data,$Type) {

        if($Type == 102){
            $SupplyOrderData['Out_trade_no']=$Data['Out_Trade_No'];
            $SupplyOrderData['Transaction_id']=$Data['Transactionid'];
            $SupplyOrderData['systemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "POS/WXSupplyOrder";
        }else if ($Type == 104) {
            $SupplyOrderData['out_trade_no']=$Data['Transactionid'];
            $SupplyOrderData['transaction_id']=$Data['Out_Trade_No'];
            $SupplyOrderData['Pay_Type']='WX';
            $SupplyOrderData['SystemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3Swiftpass/SupplyOrder";
        }else if ($Type == 106) {
            $SupplyOrderData['out_trade_no']=$Data['Transactionid'];
            $SupplyOrderData['transaction_id']=$Data['Out_Trade_No'];
            $SupplyOrderData['Pay_Type']='WX';
            $SupplyOrderData['SystemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3Swiftpass/SupplyOrder";
        }else if ($Type == 108) {
            $SupplyOrderData['OutTradeNo'] = $Data['Transactionid'];
            $SupplyOrderData['ChannelType']='WX';
            $SupplyOrderData['SystemUserSysNo']=session('SysNO');
            $SupplyOrderUrl = C('SERVER_HOST') . "IPP3BKMerchantTrade/MCHPaySupplyOrder";
        }
//        var_dump($SupplyOrderData);
//        echo $SupplyOrderUrl;
//        exit();
        $list = http($SupplyOrderUrl, $SupplyOrderData);
//        var_dump($list);
//        exit();
        return $list;
    }

    private function QueryNowPassageWay($PassageType) {
        $Url_GetPassageWay = C('SERVER_HOST') . "IPP3Customers/CustomerServicePassageWayList";
        $Data_GetPassageWay['CustomerSysNo'] = session('servicestoreno');
        $Retrun_GetPassageWay = http($Url_GetPassageWay, $Data_GetPassageWay);

        foreach ($Retrun_GetPassageWay as $key=>$row){
            if ($row['Remarks'] == $PassageType) {
                $data['NowPassageWay'] = $row['PassageWay'];
                $data['NowPassageType'] = $row['Type'];
                return $data;
            }
        }
    }

}