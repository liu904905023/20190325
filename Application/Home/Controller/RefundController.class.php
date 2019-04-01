<?php

namespace Home\Controller;

//use Think\Controller;
use Common\Compose\Base;

class RefundController extends Base {

    public function refund() {
        R("Base/getMenu");
//		var_dump(session(data));
        $this->display();
    }

    public function refund1() {
        R("Base/getMenu");
//		var_dump(session(data));
        $this->display();
    }

    public function refund2() {
        R("Base/getMenu");
//		var_dump(session(data));
        $this->display();
    }

    public function order_search() {

        $this->display();
    }

    public function refundsearch() {

        $Time_Start = empty($_POST['Time_Start']) ? $_POST['Time_Start'] : $_POST['Time_Start'] . " 00:00:00";
        $Time_end = empty($_POST['Time_end']) ? $_POST['Time_end'] : $_POST['Time_end'] . " 23:59:59";
        $out_refund_no = $_POST['out_refund_no'];
        $data = array("starttime" => $starttime, "endtime" => $endtime, "out_refund_no" => $out_refund_no

        );
        $data = json_encode($data);
//		var_dump($data);exit;
        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data),//"X-Ywkj-Authentication:" . strlen( $data ),
        );
        $list = http_request(C('SERVER_HOST') . "POS/POSRefundList", $data, $head);
        $this->ajaxReturn($list, json);

    }

    public function refundinsert() {//退款新增
        $out_trade_no = $_POST['out_trade_no'];
        $tranno = $_POST['tranno'];
        $total_fee = yuan2fee($_POST['total_fee']);
        $refund_fee = yuan2fee($_POST['refund_fee']);
        $SOSysNo = $_POST['SOSysNo'];
        $paytype = $_POST['paytype'];

        $Url_GetPassageWay = C('SERVER_HOST') . "IPP3Customers/CustomerServicePassageWayList";
        $Data_GetPassageWay['CustomerSysNo'] = session('servicestoreno');
        $Retrun_GetPassageWay = http($Url_GetPassageWay, $Data_GetPassageWay);


        $CheckRefund_Url = C('SERVER_HOST') . "IPP3Customers/IPP3RoleApplicationList";
        $CheckRefund_Data['SystemUserSysNo'] = session(SysNO);
        $CheckRefund_List = http($CheckRefund_Url, $CheckRefund_Data);

        $Istrue = array_filter($Retrun_GetPassageWay, function ($t) use ($paytype) {
            return $t['Type'] == $paytype;
        });

        $timestart = $_POST['timestart'];
        $time = explode(" ", $timestart);
        $Ymd = $time[0];
        $NowDay = date("Y-m-d", time());
        if (!empty($Istrue)) {

        } else {
            $list['Description'] = "非当前通道不允许退款";
            $this->ajaxReturn($list, json);
            exit();
        }


        if (strtotime($Ymd) == strtotime($NowDay)) {

        } else {
            if ($CheckRefund_List['Data'][0]['ApplicationSysNo'] == 1) {

            } else {
                $list['Description'] = "非当天交易不允许退款";
                $this->ajaxReturn($list, json);
                exit();
            }
        }
        if ($paytype == '108' || $paytype == '109') {
            $data = array('ReqModel'=>array("RefundAmount" => $refund_fee, "Total_fee" => $total_fee, "SystemUserSysNo" => session('SysNO')));
            $data['ReqModel']['RefundAmount'] = $refund_fee;
            $data['ReqModel']['Total_fee'] = $total_fee;
            $data['SystemUserSysNo'] = session('SysNO');
        }else{
            $data = array("refund_fee" => $refund_fee, "total_fee" => $total_fee, "SOSysNo" => $SOSysNo);
            if (session(flag) == 0) {
                $data['YwMch_id2'] = session(SysNO);
            } else {
                $data['YwMch_id2'] = session(SysNO);
            }
        }
        if ($paytype == '104' || $paytype == '105' || $paytype == '106' || $paytype == '107') {
            $data["Transaction_id"] = $out_trade_no;
        } else if ($paytype == '102' || $paytype == '103') {
            $data["out_trade_no"] = $out_trade_no;
        } else if ($paytype == '108' || $paytype == '109') {
            $data['ReqModel']['OutTradeNo'] = $out_trade_no;
//            $data['Transaction_id'] = $tranno;
        }


        if ($paytype == '102' || $paytype == '104' || $paytype == '106') {
            $data['Pay_Type'] = 'WX';
        } else if ($paytype == '103' || $paytype == '105' || $paytype == '107') {
            $data['Pay_Type'] = 'AliPay';
        } else if ($paytype == '108') {
            $data['ChannelType']='WX';
        } else if ($paytype == '109') {
            $data['ChannelType'] = 'ALI';
        }


        if ($paytype == '102') {
            $url = C('SERVER_HOST') . "POS/POSRefundInsert";
        }else if ($paytype == '103') {
            $url = C('SERVER_HOST') . "IPP3AliPay/AliPayRefundUnion";
        }else if ($paytype == '104' || $paytype == '105' || $paytype == '106' || $paytype == '107') {
            $url = C('SERVER_HOST') . "IPP3Swiftpass/RefundApiUnion";
        }else if ($paytype=='108'||$paytype=='109') {
            $url = C('SERVER_HOST') . "IPP3WSOrder/WSPayRefundUnion";
        }
//        var_dump(json_encode($data));
//        echo $url;
//        exit();



        $list = http($url, $data);
        $return_data['Code'] = $list['Code'];

        if($list['Code']==0&&$list){
            $return_data['Description']='退款成功！';
        }else{
            $return_data['Description']='退款失败！';
        }



        $this->ajaxReturn($return_data);
    }

    public function checkuserpass() {

        $password = I("password");
        $data = 0;
        if (session(password) == $password) {
            $data = 1;
        }
        $this->ajaxReturn($data, json);


    }

    public function refundlist() {

        $Time_Start = $_POST['Time_Start'];
        $Time_end = $_POST['Time_End'];
        $out_trade_no = $_POST['out_trade_no'];
        //$CustomerSysNo = empty($_POST['CustomerSysNo'])? session(SysNO):$_POST['CustomerSysNo'];
        $PageNumber = $_POST['PageNumber'];
        $PageSize = $_POST['PageSize'];
        $data = array("Time_Start" => $Time_Start, "Time_end" => $Time_end, "Out_trade_no" => $out_trade_no

        );
        if(session(flag)==1&&session(servicestoretype)==1){//员工
            $data['SystemUserSysNo'] = session('SysNO');

        }else if(session(data)['CustomersType'] == 1){

        }


        $data['PagingInfo']['PageSize'] = $PageSize;
        $data['PagingInfo']['PageNumber'] = $PageNumber;
        $data = json_encode($data);
//		var_dump($data);exit;
        $head = array("Content-Type:application/json;charset=UTF-8", "Content-length:" . strlen($data), "X-Ywkj-Authentication:" . strlen($data));
        $url = C('SERVER_HOST') . "IPP3Order/IPP3OrderFundListSP";
        $list = http_request($url, $data, $head);
        $list = json_decode($list, true);
//        var_dump($list);
//        echo $url;
//        exit();
        foreach ($list['model'] as $row => $val) {
            $info['model'][$row]['SysNo'] = $val['SysNo'];
            $info['model'][$row]['Out_trade_no'] = $val['Out_trade_no'];
            $info['model'][$row]['Transaction_id'] = $val['Transaction_id'];
            $info['model'][$row]['Pay_Types'] = $val['Pay_Type'];
            $info['model'][$row]['Pay_Type'] = CheckOrderType($val['Pay_Type']);
            //$info['model'][$row]['Status']=$val['Status'];
            $info['model'][$row]['Total_fee'] = fee2yuan($val['Total_fee']);
            $info['model'][$row]['Time_Start'] = $val['Time_Start'];
            $info['model'][$row]['Cash_fee'] = fee2yuan($val['Cash_fee']);
            $info['model'][$row]['refund_fee'] = fee2yuan($val['refund_fee']);
            $info['model'][$row]['fee'] = fee2yuan($val['fee']);
            $info['model'][$row]['refundCount'] = $val['refundCount'];

        }
        $info['totalCount'] = $list['totalCount'];
        $this->ajaxReturn($info, json);


    }

}
